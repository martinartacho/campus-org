<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Cart;
use Illuminate\Http\Request;

class ShareCartData
{
    public function handle(Request $request, Closure $next)
    {
        // Start session if not started
        if (!session()->isStarted()) {
            session()->start();
        }
        
        // Share cart data with all views
        $cart = Cart::getCurrent();
        $cartItemsCount = $cart ? $cart->items_count : 0;
        
        // Force refresh cart data to get latest items
        if ($cart) {
            $cart->refresh();
            $cartItemsCount = $cart->items_count;
        }
        
        view()->share('cartItemsCount', $cartItemsCount);
        view()->share('cart', $cart);
        
        return $next($request);
    }
}
