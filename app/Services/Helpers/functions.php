<?php

use Illuminate\Http\JsonResponse;

function responseSuccess(string $text, int $code = 200): JsonResponse
{
    return response()->json([
        'message' => "success: $text",
    ], $code
    );
}

function responseSuccessWithId(string $text, $model, int $code = 200): JsonResponse
{
    return response()->json([
        'message' => "success: $text",
        'id' => $model->id,
    ], $code
    );
}
function responseFail(Exception $e, string $text, int $code = 200): JsonResponse
{
    return response()->json(['error' => "$text: ".$e->getMessage()], $code);
}
