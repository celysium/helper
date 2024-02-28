<?php

namespace Celysium\Helper\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IranianMobile
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string $parameters
     * @return Response
     */
    public function handle(Request $request, Closure $next, string $parameters = 'mobile'): Response
    {
        $fields = $request->only(explode(',', $parameters));

        $numbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        foreach ($fields as $key => $field) {
            $field = str_replace($numbers, array_keys($numbers), $field);
            if (preg_match('/^((9)[0-9]{9})+$/', $field)) {
                $request->merge([$key => '0' . $field]);
            } elseif (
                preg_match('/^((\+989)[0-9]{9})+$/', $field) ||
                preg_match('/^((0989)[0-9]{9})+$/', $field) ||
                preg_match('/^((989)[0-9]{9})+$/', $field)
            ) {
                $request->merge([$key => '0' . substr($field, -10)]);
            } else {
                $request->merge([$key => $field]);
            }
        }

        return $next($request);
    }
}
