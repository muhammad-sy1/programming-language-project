<?php

if (! function_exists('success')) {
    function success($data = [], $statusCode = 200)
    {
        if (count($data) == 0) {
            $statusCode = 204;
        }

        return response()->json([
            'data' => $data,
        ], $statusCode);

    }
}
