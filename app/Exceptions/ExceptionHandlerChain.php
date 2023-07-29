<?php

namespace App\Exceptions;

use App\Enums\HttpStatusCodes;
use App\Http\Responses\JsonResponse;
use Exception;
use Illuminate\Http\Request;

abstract class ExceptionHandlerChain
{
    protected ?ExceptionHandlerChain $nextHandler = null;

    function setNextHandler(ExceptionHandlerChain $nextHandler)
    {
        $this->nextHandler = $nextHandler;
        return $this;
    }

    function handle(Exception $e, Request $request)
    {
        if ($this->canHandle($e)) {
            return $this->getResponse($e, $request);
        } else if ($this->nextHandler) {
            return $this->nextHandler->handle($e, $request);
        } else {
            if ($request->is('api/*')) {
                return JsonResponse::error(
                    'Something went wrong',
                    HttpStatusCodes::INTERNAL_SERVER_ERROR
                );
            }
            return back()->with('error', $e->getMessage());
        }
    }

    protected abstract function canHandle($e): bool;
    protected abstract function getResponse($e, $request);
}