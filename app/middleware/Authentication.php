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
            header('ADP-ACTION: LOGOUT');
            return json(['message' => lang('invalid token')], 401);
        }
        $jwt = new JsonWebToken();
        $response = $jwt->verification($token);
        if ($response instanceof Response) {
            return $response;
        }

        return $next($request);
    }
}
