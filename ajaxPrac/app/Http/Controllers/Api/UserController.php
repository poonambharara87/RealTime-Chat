<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\PasswordReset;
use App\Mail\ForgotPassword;
use Illuminate\Support\Str;
use Validator, Hash;
use App\Models\User;
use Carbon\Carbon;
class UserController extends Controller
{
    // public function register(Request $request)
    // {

    //     $rules = [
    //         'name' => 'required|string',
    //         'email' => 'required|email',
    //         'password' => 'required',
    //         'password_confirmation' => 'same:password'
    //     ];

    //     $validator = Validator::make($request->all(), $rules);

    //     if($validator->fails())
    //     {
    //         $error = $validator->getMessageBag()->first();
    //         return response()->json(['message' => $error]);
    //     }

    //     $user = new User;
    //     $user->name = $request->name ? $request->name : '';

    //     $user->password = bcrypt($request->password);
    //     $user->email = $request->email ? $request->email : '';
    //     $user->save();
     
    //     $success['user'] = $user;
    //     $success['token'] =  $user->createToken('MyAuthApp')->plainTextToken;
    //     // $token = $user->createToken('user-access')->accessToken;
    //     return response()->json(['success' => $success]);
    // }


    public function register(Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'email' => 'required|email',
            'password'=> 'required',
            'password_confirmation' => 'same:password'
        ];

        $validator = Validator::make($request->all(), $rules);
        if($validator->fails())
        {
            $error = $validator->getMessageBag()->first();
            return response()->json(['error' => $error]);
        }

        $user = new User;
        $user->name = $request->name ? $request->name : '';
        $user->email = $request->email ? $request->email : '';
        $user->password = $request->password ? $request->password : '';
        $user->save();

        $success['user'] = $user;
        $success['token']=$user->createToken('MyAuth')->plainTextToken;
        return response()->json(['success' => $success]);
    }


    public function login(Request $request)
    {
        $user = User::where('email',$request->email)->first();
        if(!$user)
        {
            return response()->json(['error' => 'Not Authenticated!']);
        }else{
            if(Hash::check($request->password, $user->password))
            {
                $success['token'] = $user->createToken('access-token')->plainTextToken();
                $success['user'] = $user;
                return response()->json(['success' => $success]);
            }
        }
      
        return response()->json(['user'=> $user, 'message' => 'User logged In successfully!']);
    }

    public function change_password(Request $request)
    {
        $rules = [
            'current_password' => 'required',
            'new_password' => 'required',
            'new_confirm_password' => 'same:new_password'
        ];

        $validator = Validator::make($request->all(), $rules);
        if($validator->fails)
        {
            $error = $validator->getMessageBag()->first();
            return response()->json(['error' => $error]);
        }

        $user = User::where('password', $request->password)->first();

        if(!$user)
        {
            return response()->json(['error' => 'Incorrent Password']);
        }else{

            if(Hash::check($request->current_password, $user->password))
            {
                $success['user'] =  $user;
                $success['token'] = $user->createToken('authentication-token')->plainTextToken;

               return response()->json(['success' => $success]);
            }
        }
    }


    public function forgot_password(Request $request)
    {
        $user = User::where('email',$request->email)->first();
       
        if(!$user)
        {
            return response()->json(['error' => 'Email not registered']);
        }
        $token = str::random(40);
        $data['user'] = $user;
        $data['token'] = $token;
        $data['email'] = $request->email;
        $domain = URL::to('/');
        $data['url'] = $domain.'/reset-password?token='.$token; 
        $data['body'] = 'Please click on link to reset your password';

        Mail::send('mail.forgot_password',['data' => $data],function($message) use ($data){
            $message->to($data['email'])->subject($data['body']);
        });

        $date_time = Carbon::now()->format('Y-m-d H:i:s');  
        
        if($request->email)
        {
            PasswordReset::where('email', $request->email)->delete();
        }
        $reset_password = new PasswordReset;
        $reset_password->email = $request->email;
        $reset_password->token = $token;
        $reset_password->created_at = $date_time ;
        $reset_password->save();

        return response()->json(['success' => 'Please check your mail to reset your passowrd!']);
    }

  
}
