<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureAccountNotLocked
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && (int) $user->trang_thai === 0) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                abort(403, 'Tài khoản của bạn đã bị khóa.');
            }

            return redirect()->route('login')
                ->withErrors(['email' => 'Tài khoản của bạn đã bị khóa hoặc từ chối duyệt.']);
        }

        return $next($request);
    }
}
