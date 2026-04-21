<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cart;

class CleanExpiredCarts extends Command
{
    protected $signature = 'carts:clean';
    protected $description = 'Clean expired and duplicate carts';

    public function handle()
    {
        $this->info('Cleaning expired carts...');
        
        // Delete expired carts
        $expiredCount = Cart::where('expires_at', '<', now())
            ->delete();
        
        $this->info("Deleted {$expiredCount} expired carts");
        
        // Handle duplicate session_id carts
        $duplicates = Cart::select('session_id')
            ->where('session_id', '!=', '')
            ->whereNotNull('session_id')
            ->groupBy('session_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();
        
        foreach ($duplicates as $duplicate) {
            // Keep the most recent cart, delete others
            $cartsToDelete = Cart::where('session_id', $duplicate->session_id)
                ->orderBy('created_at', 'desc')
                ->skip(1)
                ->get();
            
            foreach ($cartsToDelete as $cart) {
                $cart->delete();
            }
        }
        
        $this->info('Cleaned duplicate session carts');
        $this->info('Cart cleaning completed!');
        
        return 0;
    }
}
