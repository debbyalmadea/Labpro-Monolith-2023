<?php

namespace App\Exceptions;

use App\Http\Responses\JsonResponse;

class HttpCustomExceptionHandler extends ExceptionHandlerChain
{
    protected function canHandle($e): bool
    {
        return $e instanceof HttpCustomException;
    }
    protected function getResponse($e, $request)
    {
        if ($request->is('api/*')) {
            return JsonResponse::error($e->getMessage(), $e->getCode());
        }

        if ($e->getRedirect() != '') {
            return redirect($e->getRedirect())->with('error', $e->getMessage());
        }

        if ($e->getCode() === 404) {
            return view('404');
        }

        return back()->with('error', $e->getMessage());
    }
}