<?php

namespace App\Http\Middleware;

use App\Support\CartStorage;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LoadCartFromDatabase
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check() || $request->session()->get('cart_loaded_from_db')) {
            return $next($request);
        }

        $sessionCart = $request->session()->get('cart', []);
        $sessionCart = is_array($sessionCart) ? $sessionCart : [];

        $cart = CartStorage::mergeForUser((int) Auth::id(), $sessionCart);

        $request->session()->put('cart', $cart);
        $request->session()->put('cart_loaded_from_db', true);

        return $next($request);
    }
}
