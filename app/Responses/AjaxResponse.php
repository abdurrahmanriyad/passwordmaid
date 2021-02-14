<?php

namespace App\Responses;

class AjaxResponse {
    public static function render($status = 'success', $data = [], $message = '', $code = 200)
    {
        return response()->json([
            'status' => $status,
            'data' => $data,
            'message' => $message
        ], $code);
    }
}