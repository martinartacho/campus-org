<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'status',
        'total_amount',
        'expires_at',
        'metadata'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'metadata' => 'array'
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_ABANDONED = 'abandoned';
    const STATUS_CONVERTED = 'converted';

    /**
     * Get the items for the cart.
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get the user that owns the cart.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get active cart for current user or session
     */
    public static function getCurrent(): ?self
    {
        if (auth()->check()) {
            return static::getForUser(auth()->id());
        } else {
            return static::getForSession(Session::getId());
        }
    }


/**
 * Get or create cart for user
 */
public static function getForUser(int $userId): self
{
    // First, clean up any carts with null session_id for this user (legacy data)
    static::where('user_id', $userId)
           ->where('session_id', null)
           ->delete();

    $cart = static::where('user_id', $userId)
                  ->where('status', self::STATUS_ACTIVE)
                  ->first();

    if (!$cart) {
        $cart = static::create([
            'user_id' => $userId,
            'session_id' => 'user_' . $userId . '_' . uniqid(), // Unique session_id for authenticated users
            'expires_at' => now()->addDays(7),
            'status' => self::STATUS_ACTIVE
        ]);
    }

    return $cart;
}

    /**
     * Get or create cart for session (guest users)
     */
    public static function getForSession(string $sessionId): self
    {
        // Look for existing cart with items
        $cart = static::where('session_id', $sessionId)
                      ->where('user_id', null)
                      ->where('status', self::STATUS_ACTIVE)
                      ->with('items')
                      ->first();

        if (!$cart) {
            // Clean any existing carts with this session_id (to avoid duplicates)
            static::where('session_id', $sessionId)
                   ->where('user_id', null)
                   ->delete();
            
            // Create new cart
            $cart = static::create([
                'session_id' => $sessionId,
                'expires_at' => now()->addDays(7),
                'status' => self::STATUS_ACTIVE
            ]);
        }

        return $cart;
    }

    /**
     * Add course to cart
     */
    public function addCourse(CampusCourse $course): CartItem
    {
        // Check if course already in cart
        $existingItem = $this->items()
                            ->where('course_id', $course->id)
                            ->first();

        if ($existingItem) {
            throw new \Exception('El curso ya está en el carrito');
        }

        // Validate course can be added
        $this->validateCourseForCart($course);

        $item = $this->items()->create([
            'course_id' => $course->id,
            'quantity' => 1,
            'price_at_time' => $course->price ?? 0,
            'course_snapshot' => [
                'title' => $course->title,
                'code' => $course->code,
                'description' => $course->description,
                'hours' => $course->hours,
                'level' => $course->level,
                'start_date' => $course->start_date?->format('Y-m-d'),
                'end_date' => $course->end_date?->format('Y-m-d'),
                'location' => $course->location,
                'format' => $course->format
            ]
        ]);

        $this->recalculateTotal();
        $this->touch(); // Update timestamp

        return $item;
    }

    /**
     * Remove course from cart
     */
    public function removeCourse(int $courseId): bool
    {
        $deleted = $this->items()
                       ->where('course_id', $courseId)
                       ->delete();

        if ($deleted) {
            $this->recalculateTotal();
            $this->touch();
        }

        return $deleted;
    }

    /**
     * Clear all items from cart
     */
    public function clear(): bool
    {
        $deleted = $this->items()->delete();
        
        if ($deleted) {
            $this->update([
                'total_amount' => 0,
                'status' => self::STATUS_ABANDONED
            ]);
        }

        return $deleted;
    }

    /**
     * Get courses collection
     */
    public function getCoursesAttribute(): Collection
    {
        return $this->items->map(function ($item) {
            $course = $item->course;
            $course->cart_quantity = $item->quantity;
            $course->cart_price = $item->price_at_time;
            $course->cart_item_id = $item->id;
            return $course;
        });
    }

    /**
     * Get total items count
     */
    public function getItemsCountAttribute(): int
    {
        return $this->items()->sum('quantity');
    }

    /**
     * Check if cart is empty
     */
    public function isEmpty(): bool
    {
        return $this->items()->count() === 0;
    }

    /**
     * Check if cart has expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Mark cart as converted (after successful payment)
     */
    public function markAsConverted(): void
    {
        $this->update([
            'status' => self::STATUS_CONVERTED
        ]);
    }

    /**
     * Recalculate total amount
     */
    public function recalculateTotal(): void
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->price_at_time * $item->quantity;
        }

        $this->update(['total_amount' => $total]);
    }

    /**
     * Validate course can be added to cart
     */
    private function validateCourseForCart(CampusCourse $course): void
    {
        // Check if course is public and active
        if (!$course->is_public || !$course->is_active) {
            throw new \Exception('Este curso no está disponible para matrícula');
        }

        // Check if course has available spots
        if (!$course->hasAvailableSpots()) {
            throw new \Exception('No hay plazas disponibles para este curso');
        }

        // Check if registration period is open (temporarily disabled for testing)
        // if ($course->season && !$course->season->isRegistrationOpen()) {
        //     throw new \Exception('El período de matriculación para este curso está cerrado');
        // }

        // Check if course is in valid status
        $validStatuses = ['planning', 'active', 'registration', 'in_progress'];
        if (!in_array($course->status, $validStatuses)) {
            throw new \Exception('Este curso no está disponible para matrícula');
        }
    }

    /**
     * Transfer guest cart to user cart on login
     */
    public static function transferGuestCartToUser(string $sessionId, int $userId): void
    {
        $guestCart = static::where('session_id', $sessionId)
                          ->where('user_id', null)
                          ->where('status', self::STATUS_ACTIVE)
                          ->first();

        if (!$guestCart || $guestCart->isEmpty()) {
            return;
        }

        $userCart = static::getForUser($userId);

        // Transfer items
        foreach ($guestCart->items as $item) {
            try {
                // Check if user already has this course in cart
                $existingItem = $userCart->items()
                                         ->where('course_id', $item->course_id)
                                         ->first();

                if (!$existingItem) {
                    $userCart->items()->create([
                        'course_id' => $item->course_id,
                        'quantity' => $item->quantity,
                        'price_at_time' => $item->price_at_time,
                        'course_snapshot' => $item->course_snapshot
                    ]);
                }
            } catch (\Exception $e) {
                // Skip items that can't be transferred
                continue;
            }
        }

        // Mark guest cart as abandoned
        $guestCart->update(['status' => self::STATUS_ABANDONED]);

        // Recalculate user cart total
        $userCart->recalculateTotal();
    }

    /**
     * Clean up expired carts
     */
    public static function cleanupExpired(): int
    {
        return static::where('expires_at', '<', now())
                    ->where('status', self::STATUS_ACTIVE)
                    ->update(['status' => self::STATUS_ABANDONED]);
    }
}
