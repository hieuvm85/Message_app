<?php


class ResponseCustom {


    public static function error(Exception $e) {
        return response()->json([
            'status' => $e->getCode(),
            'message' => $e->getMessage(),
        ]);
    }

    public static function false($message) {
        return response()->json([
            'status' => 301,
            'message' => $message,
        ]);
    }

    public static function success($data) {
        return response()->json([
            'status' => 200,
            'message' => 'success',
            'data' => $data
        ]);
    }


}