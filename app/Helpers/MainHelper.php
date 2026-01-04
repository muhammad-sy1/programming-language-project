<?php

if (! function_exists('success')) {
    function success($data = [], $statusCode = 200)
    {
        return response()->json([
            'data' => $data,
        ], $statusCode);

    }
}
