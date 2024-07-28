<?php

namespace Celysium\Helper\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Mobile
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

        foreach ($fields as $key => $field) {
            $request->merge([$key => regularIranianMobile($field)]);
        }

        return $next($request);
    }
}
