<?php

namespace App\Exceptions;

use App\Enums\HttpStatusCodes;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;


class ConnectionExceptionHandler extends ExceptionHandlerChain
{
    protected function canHandle($e): bool
    {
        return $e instanceof ConnectionException;
    }
    protected function getResponse($e, $request)
    {
        if ($request->is('api/*')) {
            return JsonResponse::error(
                "Internal Server Error. Couldn't connect to the server",
                HttpStatusCodes::INTERNAL_SERVER_ERROR
            );
        }

        return back()->with('error', "Internal Server Error. Couldn't connect to the server");
    }
}