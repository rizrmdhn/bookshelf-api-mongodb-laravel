<?php

namespace App\Helpers;

/**
 * Validator helper.
 */

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ResponseFormatter;

class ValidatorHelper
{
    /**
     * Validate request.
     *
     * @param Request $request
     * @param array $rules
     * @param array $messages
     * @return void
     */
    public static function validateRequest(Request $request, array $rules, array $messages = [])
    {
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }
    }
}
