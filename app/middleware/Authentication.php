<?php

namespace app\middleware;

use app\common\controller\JsonWebToken;
use think\Response;

class Authentication
{
    public function handle($request, \Closure $next)
    {
        $token = str_replace('Bearer ', '', $request->header('authorization'));
        if (empty($token)) {
            return json([
                'ADP_LOGOUT' => true,
                'message' => lang('must use the token', [$request->url()]),
            ], 401);
        }

        $jwt = new JsonWebToken();
        $response = $jwt->verification($token);
        if ($response instanceof Response) {
            return $response;
        }

        return $next($request);
    }
}
