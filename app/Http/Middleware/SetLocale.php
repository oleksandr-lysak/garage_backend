<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        // For SEO, always set Ukrainian locale for public routes
        if (!$request->is('admin/*') && !$request->is('api/*')) {
            App::setLocale('uk');
            session(['locale' => 'uk']);
            return $next($request);
        }

        // For admin and API routes, allow locale switching
        $localeHeader = $request->header('locale');
        $acceptLang   = $request->header('accept-language');
        $queryParam   = $request->get('locale');

        // Extract first locale from Accept-Language if present, e.g. "uk-UA,uk;q=0.9"
        if ($acceptLang && !$localeHeader) {
            // split by comma and take the first part before possible ';'
            $parts = explode(',', $acceptLang);
            $primary = isset($parts[0]) ? trim($parts[0]) : '';
            // take language code before region, e.g. uk-UA -> uk
            $localeHeader = substr($primary, 0, 2);
        }

        $sessionLocale = session('locale');
        $locale = $localeHeader ?? $queryParam ?? $sessionLocale ?? config('app.locale', 'uk');

        App::setLocale($locale);
        session(['locale' => $locale]);

        return $next($request);
    }
}
