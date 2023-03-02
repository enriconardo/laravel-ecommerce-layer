<?php

namespace EcommerceLayer\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use PrinsFrank\Standards\Http\HttpStatusCode;

class InvalidEntityException extends Exception
{
    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request): Response
    {
        $data = config('app.debug') ? [
            'message' => $this->getMessage(),
            'exception' => get_class($this),
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'trace' => collect($this->getTrace())->map(fn ($trace) => Arr::except($trace, ['args']))->all(),
        ] : [
            'message' => $this->getMessage(),
        ];

        return response($data, HttpStatusCode::Bad_Request->value);
    }
}