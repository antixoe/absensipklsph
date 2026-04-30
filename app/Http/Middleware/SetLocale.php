<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $supportedLocales = ['id', 'en', 'zh'];
        $defaultLocale = config('app.locale', 'en');
        $locale = session('locale');

        if (auth()->check() && auth()->user()->locale) {
            $locale = auth()->user()->locale;
        }

        if (!in_array($locale, $supportedLocales, true)) {
            $locale = in_array($defaultLocale, $supportedLocales, true) ? $defaultLocale : 'en';
        }

        App::setLocale($locale);
        session(['locale' => $locale]);

        return $next($request);
    }
}
