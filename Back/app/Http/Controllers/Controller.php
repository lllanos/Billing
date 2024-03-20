<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use YacyretaPackageController\ControllerExtended;

class Controller extends ControllerExtended {
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    /**
     * @param $options
     * @return JsonResponse
     */
    protected function responseJson($options): JsonResponse
    {
        $params = [
            'status'  => true
        ] + $options;

        return response()->json($params);
    }

    /**
     * @param  array  $errors
     * @return JsonResponse
     */
    protected function responseJsonError(array $errors): JsonResponse
    {
       foreach ($errors as $error) {
           ession::flash('error', $error);
       }

        $params = [
            'status'  => true,
            'message' => $errors,
        ];

       return $this->responseJson($params);
    }
}
