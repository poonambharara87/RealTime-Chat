<?php

namespace App\Http\Controllers;

use App\Events\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function messages(Request $request)
    {
        event(new Message($request->input(key:'username'), $request->input(key:'message')));
        return [];
    }
}
