<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller as BaseController;
use App\Http\Trait\IdentityTrait;



class ApiController extends BaseController {

    use IdentityTrait;

    /** 
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message = 'Successfully fetched data', $code = 200) {
        $response = [
            'message' => $message,
            'success' => true,
            'data'    => $result,
        ];

        return response()->json($response, $code);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = 'An error occured', $code = 422) {
        $response = [
            'message' => $error ?: $errorMessages,
            'success' => false,
        ];

        return response()->json($response, $code);
    }
}
