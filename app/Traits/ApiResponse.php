<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Str;

trait ApiResponse
{
    public function successResponse($data)
    {
        return response()->json($data, 200);
    }

    protected function errorResponse($message, $code)
    {
        return response()->json(['error' => $message, 'code' => $code], $code);
    }
}
