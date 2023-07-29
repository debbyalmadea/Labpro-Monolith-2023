<?php

namespace App\Exceptions;

use App\Http\Responses\JsonResponse;
use Illuminate\Database\QueryException;

class QueryExceptionHandler extends ExceptionHandlerChain
{
    protected function canHandle($e): bool
    {
        return $e instanceof QueryException;
    }
    protected function getResponse($e, $request)
    {
        if ($request->is('api/*')) {
            return JsonResponse::error($e->getMessage(), $e->getCode());
        }

        if ($e->getCode() === 404) {
            return view('404');
        }

        return back()->with('error', $e->getMessage());
    }
}