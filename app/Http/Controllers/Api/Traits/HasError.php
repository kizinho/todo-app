<?php

namespace App\Http\Controllers\Api\Traits;

use Validator;

trait HasError {

    public static function getErrorMessage($input, $rules) {
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return [
                'status' => 422,
                'message' => $validator->errors()
            ];
        }
    }
}
