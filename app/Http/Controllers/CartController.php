<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CampusCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    /**
     * Display the cart
     */
    public function index()
    {
        $cart = Cart::getCurrent();

        if (!$cart || $cart->isEmpty()) {
            return view('cart.empty');
        }

        // Load cart with items and courses
        $cart->load(['items.course', 'items.course.season', 'items.course.category']);

        // Validate all items in cart
        $invalidItems = [];
        $validItems = collect();

        foreach ($cart->items as $item) {
            $issues = $item->getValidationIssues();
            if (!empty($issues)) {
                $invalidItems[] = [
                    'item' => $item,
                    'issues' => $issues
                ];
            } else {
                $validItems->push($item);
            }
        }

        return view('cart.index', compact(
            'cart',
            'validItems',
            'invalidItems'
        ));
    }

    /**
     * Add course to cart
     */
    public function add(Request $request, CampusCourse $course)
    {
        try {
            // Validate course can be added
            $this->validateCourseForCart($course);

            // Get or create cart
            $cart = Cart::getCurrent();

            // Add course to cart
            $cartItem = $cart->addCourse($course);

            // Flash message
            $message = "¡\"{$course->title}\" afegit a la cistella correctament!";

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'cart_count' => $cart->items_count,
                    'cart_total' => $cart->total_amount,
                    'cart_item' => $cartItem
                ]);
            }

            return redirect()
                ->back()
                ->with('success', $message);

        } catch (\Exception $e) {
            $message = $e->getMessage();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 422);
            }

            return redirect()
                ->back()
                ->with('error', $message);
        }
    }

    /**
     * Remove course from cart
     */
    public function remove(Request $request, CampusCourse $course)
    {
        $cart = Cart::getCurrent();

        if (!$cart) {
            $message = 'No hi ha cistella activa';
        } else {
            $removed = $cart->removeCourse($course->id);
            $message = $removed ? 
                "\"{$course->title}\" eliminat de la cistella" : 
                'El curs no estava a la cistella';
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => $removed ?? false,
                'message' => $message,
                'cart_count' => $cart?->items_count ?? 0,
                'cart_total' => $cart?->total_amount ?? 0
            ]);
        }

        return redirect()
            ->route('cart.index')
            ->with($removed ? 'success' : 'error', $message);
    }

    /**
     * Clear entire cart
     */
    public function clear(Request $request)
    {
        $cart = Cart::getCurrent();

        if (!$cart) {
            $message = 'No hi ha cistella activa';
        } else {
            $cleared = $cart->clear();
            $message = $cleared ? 'Cistella buidada correctament' : 'La cistella ja estava buida';
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => $cleared ?? false,
                'message' => $message,
                'cart_count' => 0,
                'cart_total' => 0
            ]);
        }

        return redirect()
            ->route('cart.index')
            ->with($cleared ? 'success' : 'error', $message);
    }

    /**
     * Update cart item quantity (for future use)
     */
    public function update(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:cart_items,id',
            'items.*.quantity' => 'required|integer|min:1|max:10'
        ]);

        $cart = Cart::getCurrent();

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'No hi ha cistella activa'
            ], 404);
        }

        $updated = 0;
        foreach ($request->items as $itemData) {
            $item = $cart->items()->find($itemData['id']);
            
            if ($item) {
                // For now, we only allow quantity 1 (one enrollment per course)
                // But this method is prepared for future changes
                $item->update(['quantity' => 1]);
                $updated++;
            }
        }

        $cart->recalculateTotal();

        return response()->json([
            'success' => true,
            'message' => "{$updated} elementos actualizados",
            'cart_total' => $cart->total_amount
        ]);
    }

    /**
     * Remove invalid items from cart
     */
    public function removeInvalid(Request $request)
    {
        $cart = Cart::getCurrent();

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'No hi ha cistella activa'
            ], 404);
        }

        $cart->load('items.course');
        $removed = 0;

        foreach ($cart->items as $item) {
            if ($item->hasValidationIssues()) {
                $item->delete();
                $removed++;
            }
        }

        $cart->recalculateTotal();

        return response()->json([
            'success' => true,
            'message' => "{$removed} elementos inválidos eliminados",
            'cart_count' => $cart->items_count,
            'cart_total' => $cart->total_amount
        ]);
    }

    /**
     * Get cart summary (AJAX endpoint)
     */
    public function summary()
    {
        $cart = Cart::getCurrent();

        if (!$cart || $cart->isEmpty()) {
            return response()->json([
                'items_count' => 0,
                'total_amount' => 0,
                'items' => []
            ]);
        }

        $cart->load(['items.course' => function($query) {
            $query->select('id', 'title', 'code', 'slug', 'price');
        }]);

        $items = $cart->items->map(function ($item) {
            return [
                'id' => $item->id,
                'course_id' => $item->course_id,
                'course_title' => $item->course_title,
                'course_code' => $item->course_code,
                'course_slug' => $item->course->slug ?? null,
                'price' => $item->price_at_time,
                'quantity' => $item->quantity,
                'subtotal' => $item->subtotal,
                'has_issues' => $item->hasValidationIssues(),
                'issues' => $item->getValidationIssues()
            ];
        });

        return response()->json([
            'items_count' => $cart->items_count,
            'total_amount' => $cart->total_amount,
            'items' => $items,
            'expires_at' => $cart->expires_at?->toISOString(),
            'is_expired' => $cart->isExpired()
        ]);
    }

    /**
     * Validate course can be added to cart
     */
    private function validateCourseForCart(CampusCourse $course): void
    {
        // Check if course is public and active
        if (!$course->is_public || !$course->is_active) {
            throw ValidationException::withMessages([
                'course' => 'Este curso no está disponible para matrícula'
            ]);
        }

        // Check if course has available spots
        if (!$course->hasAvailableSpots()) {
            throw ValidationException::withMessages([
                'course' => 'No hay plazas disponibles para este curso'
            ]);
        }

        // Check if registration period is open (temporarily disabled for testing)
        // if ($course->season && !$course->season->isRegistrationOpen()) {
        //     throw ValidationException::withMessages([
        //         'course' => 'El período de matriculación para este curso está cerrado'
        //     ]);
        // }

        // Check if course is in valid status
        $validStatuses = ['planning', 'active', 'registration', 'in_progress'];
        if (!in_array($course->status, $validStatuses)) {
            throw ValidationException::withMessages([
                'course' => 'Este curso no está disponible para matrícula'
            ]);
        }

        // Check if user already has a registration for this course
        if (auth()->check()) {
            $existingRegistration = \App\Models\CampusRegistration::where('student_id', auth()->id())
                ->where('course_id', $course->id)
                ->whereIn('status', ['pending', 'confirmed', 'completed'])
                ->first();

            if ($existingRegistration) {
                throw ValidationException::withMessages([
                    'course' => 'Ya estás matriculado en este curso'
                ]);
            }
        }
    }

    /**
     * Transfer guest cart to user on login
     */
    public function transferOnLogin()
    {
        if (!auth()->check()) {
            return;
        }

        Cart::transferGuestCartToUser(Session::getId(), auth()->id());
    }

    /**
     * Clean up expired carts (can be called via cron/scheduler)
     */
    public function cleanup()
    {
        $cleaned = Cart::cleanupExpired();

        return response()->json([
            'success' => true,
            'message' => "{$cleaned} cistelles expirades eliminades",
            'cleaned_count' => $cleaned
        ]);
    }
}
