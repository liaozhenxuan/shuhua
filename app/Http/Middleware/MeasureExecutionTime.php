<?php

namespace App\Http\Middleware;

use Closure;

/**
 * measure execution time and return
 * @author Tom 2017-07-26
 * @param request \Request
 * @param next \Closure
 * @return Closure next
 */
class MeasureExecutionTime {
    /**
     * Entry point
     */
    public function handle($request, Closure $next) {
        $response = $next($request);

        if ($response->headers->get('content-type') == 'application/json') {
            $collection = json_decode(json_encode($response->original), true);
            $collection['execution_time'] = microtime() - LUMEN_START;
            $response->setContent(json_encode($collection));
        }
        return $response;
    }
}
