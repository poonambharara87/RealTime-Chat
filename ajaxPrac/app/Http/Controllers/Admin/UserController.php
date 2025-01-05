<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use App\Models\PasswordReset;
use App\Mail\ForgotPassword;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Validator, Hash;
use Carbon\Carbon;

class UserController extends Controller
{
    public function addUser(Request $request)
    {
        $image_path = "";
        if($request->file('image'))
        {
            $file = $request->file('image');
            $filename = time()." ". $file->getClientOriginalName();
            $image_path = $file->storeAs('public', $filename);
        }
      
        $user = new User;
        $user->name = $request->input('name') ? $request->input('name') : '';
        $user->image  = $image_path ? $image_path : '';
        $user->email  = $request->input('email') ? $request->input('email') : '';
        $user->password  = $request->password ? $request->password : '';
        $user->save();
        return response()->json(['res' => 'User created Successfully!']);
    }

    public function index(){
        $users = User::all();

        return view('users.index', ['users' => $users]);
    }
    
    public function update(Request $request)
    {
        // return 
        $user = User::find($request->id);
        if(!$user)
        {
            return response()->json(['res' => 'User not Found!']);
        }else{
            $user->name= $request->name;
            $user->email = $request->email;
            $user->save();
            return response()->json(['res' => $user]);
        }
    }

    public function getUserData($id)
    {
        $user = User::find($id);
        if(!$user)
        {
            $data = json_decode('User not Found');
            return $data;    
        }
        $data = json_decode($user);

        return $data;
    }

    public function resetPasswordLoad(Request $request)
    {
       
        $passwordReset = PasswordReset::where('token', $request->token)->get();
        if(isset($request->token) && count($passwordReset) > 0)
        {
            $user = User::where('email', $passwordReset[0]['email'])->get();
            return view('mail.resetPassword',compact('user'));
        }
        else{
            return view('404');
        }
        
    }

    public function resetPassword(Request $request)
    {
        //Confirmed will validate match password and password_confirmation
        $request->validate([
            'password' => 'required|min:6|confirmed|string'
        ]);

        $user = User::find($request->id);
        $user->password = $request->password;
        $user->save();
        
        return "<h1>Your password has been reset successfully.</h1>";
    }
}
