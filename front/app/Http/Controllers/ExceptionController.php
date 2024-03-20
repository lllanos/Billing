<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class ExceptionController extends Controller
{
    public function index()
    {
        // something went wrong and you want to throw CustomException
        return response()->view('errors.500', [], 500);
        throw new \App\Exceptions\CustomException('Error.');
    }
}
?>
