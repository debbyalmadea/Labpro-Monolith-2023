<?php

namespace App\Exceptions;

use Illuminate\Http\Exceptions\HttpResponseException;


class HttpResponseExceptionHandler extends ExceptionHandlerChain
{
    protected function canHandle($e): bool
    {
        return $e instanceof HttpResponseException;
    }
    protected function getResponse($e, $request)
    {
        if ($request->is('api/*')) {
            return $e->getResponse();
        }
        return back()->with('error', $e->getMessage());
    }
}