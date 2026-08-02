<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = config('blog.supported_locales', ['de', 'en']);
        $locale = (string) $request->query('locale', '');

        if (in_array($locale, $supportedLocales, true)) {
            Session::put('locale', $locale);
        } else {
            $locale = (string) Session::get('locale', config('blog.default_locale', 'de'));
        }

        if (! in_array($locale, $supportedLocales, true)) {
            $locale = config('blog.default_locale', 'de');
        }

        App::setLocale($locale);

        return $next($request);
    }
}
