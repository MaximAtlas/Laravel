<?php

use Illuminate\Http\JsonResponse;

function responseOk(string $text = 'Операция выполнена успешно', int $code = 200): JsonResponse
{
    return response()->json([
        'message' => "success: $text",
    ], $code
    );
}

function responseSuccessWithId($model, string $text = 'Операция выполнена успешно', int $code = 200): JsonResponse
{
    return response()->json([
        'message' => "success: $text",
        'id' => $model->id,
    ], $code
    );
}
function responseFail(Exception $e, string $text = 'Ошибка выполнения', int $code = 200): JsonResponse
{
    return response()->json(['error' => "$text: ".$e->getMessage()], $code);
}
function responseSimpleFail(string $text = 'Ошибка выполнения', int $code = 400): JsonResponse
{
    return response()->json(['error' => "$text"], $code);
}
