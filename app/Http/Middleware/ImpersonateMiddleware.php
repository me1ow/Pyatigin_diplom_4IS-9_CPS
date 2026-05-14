<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ImpersonateMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Проверяем, что администратор включил режим имперсонации
        if ($request->session()->has('impersonator_id')) {
            // Получаем ID оригинального администратора
            $impersonatorId = $request->session()->get('impersonator_id');
            
            // Проверяем, что текущий пользователь - администратор
            if (Auth::id() === $impersonatorId) {
                return $next($request);
            }
        }
        
        return $next($request);
    }
}
