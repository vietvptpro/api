<?php

namespace App\Http\Controllers;

use App\Constants\Constants;
use App\Models\Response;
use Illuminate\Http\Request;
use Validator;
use App\Models\UserModel;

class UserController extends Controller
{
    public function Login(Request $request)
    {
        $responseData = new Response();
        $input = $request->all();
        $validator = Validator::make($input, [
            'email' => 'required',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            $responseData->Status = Constants::RESPONSE_STATUS_ERROR;
            $responseData->Message = $validator->errors()->first();
        } else {
            $user = UserModel::where('email', $input['email'])->where('password', $input['password'])->first();
            if (empty($user)) {
                $responseData->status = Constants::RESPONSE_STATUS_ERROR;
                $responseData->message = trans('login.wrong_password');
            } else {
                $_SESSION[Constants::SESSION_KEY_USER] = $user;
                $responseData->status = Constants::RESPONSE_STATUS_SUCCESS;
                $responseData->data = $user;
            }
        }
        return json_encode($responseData);
    }

    public function SignUp(Request $request)
    {
        $responseData = new Response();
        $input = $request->all();
        $validator = Validator::make($input, [
            'email' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            $responseData->Status = Constants::RESPONSE_STATUS_ERROR;
            $responseData->Message = $validator->errors()->first();
        } else {
            $checkEmail = UserModel::where('email', $input['email'])->first();
            if (!empty($checkEmail)) {
                $responseData->status = Constants::RESPONSE_STATUS_ERROR;
                $responseData->message = trans('user.email_exist');
            } else {
                $user = new UserModel();
                $user->email = $input['email'];
                $user->first_name = $input['first_name'];
                $user->elast_nameail = $input['last_name'];
                $user->password = md5($input['password']);
                $user->role = Constants::USER;
                $responseData->status = Constants::RESPONSE_STATUS_SUCCESS;
                $responseData->data = $user;
            }
        }
        return json_encode($responseData);
    }

    public function SignInFacebook(Request $request)
    {
        $responseData = new Response();
        $input = $request->all();
        $validator = Validator::make($input, [
            'facebook_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required'
        ]);
        if ($validator->fails()) {
            $responseData->status = Constants::RESPONSE_STATUS_ERROR;
            $responseData->message = $validator->errors()->first();
        } else {
            $checkId = UserModel::where('facebook_id', $input['facebook_id'])->first();
            if (empty($checkId)) {
                $user = new UserModel();
                $user->first_name = $input['first_name'];
                $user->last_name = $input['last_name'];
                $user->facebook_id = $input['facebook_id'];
                $user->login_token = md5($input['first_name'] . $input['last_name'] . time());
                $user->role = Constants::USER;
                $user->save();
                $_SESSION[Constants::SESSION_KEY_USER] = $user;
                $responseData->status = Constants::RESPONSE_STATUS_SUCCESS;
                $responseData->data = $user;
            } else {
                $checkId->login_token = md5($checkId->first_name . $checkId->last_name . time());
                $checkId->save();
                $_SESSION[Constants::SESSION_KEY_USER] = $checkId;
                $responseData->status = Constants::RESPONSE_STATUS_SUCCESS;
                $responseData->data = $checkId;
            }
        }
        return json_encode($responseData);
    }
}
