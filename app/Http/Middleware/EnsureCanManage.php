<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureCanManage
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$user->canManage()) {
            if ($request->expectsJson()) {
                abort(403, 'Không có quyền thực hiện.');
            }

            if ($user && (int) $user->trang_thai === 2) {
                return redirect()->route('hoso.index')
                    ->with('error', 'Tài khoản của bạn đang đợi duyệt, không có quyền truy cập chức năng này.');
            }

            return redirect()->route('phanca.index')
                ->with('error', 'Bạn không có quyền truy cập chức năng này.');
        }

        return $next($request);
    }
}
