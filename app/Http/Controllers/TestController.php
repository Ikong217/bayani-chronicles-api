<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;

class TestController extends Controller
{
    //
    public function Test1()
    {
        return view('testing.testing_1');
    }

    public function GetRequest(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'links' => 'required|string',
            ]);

            if ($validator->fails()) {
                $errors = "";

                foreach ($validator->errors()->all() as $error) {
                    $errors .= "-- " . $error . "\n";
                }

                return response()->json([
                    'status'  => 'err',
                    'message' => trim($errors),
                ]);
            }

            $title   = $request->input('links');
            $message = "";

            if ($title === "userAgent") {
                $message = $request->userAgent();
            } elseif ($title === "ip") {
                $message = $request->ip();
            } elseif ($title === "token") {
                $message = csrf_token(); // returns Laravel's CSRF token
            }elseif($title === "remember"){
                try{
                    $token = Cookie::get('remember');
                    $message = Crypt::decrypt($token);
                }catch(\Throwable){
                    $message == "Remember cookie is invalid";
                }
            } else {
                return response()->json([
                    'status'  => 'err',
                    'message' => "Invalid value",
                ]);
            }

            return response()->json([
                'status'  => 'success',
                'title'   => $title,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'err',
                'message' => "Problem Occurred: " . $e->getMessage(),
            ]);
        }
    }
    public function allRequest(Request $request)
    {
        dd($request);
    }
}
