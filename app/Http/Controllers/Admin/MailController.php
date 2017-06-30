<?php

namespace App\Http\Controllers\Admin;

use Mail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\EmailSendTest;
use App\User;

class MailController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }
    
    public function index()
    {
        return view('admin.email.index');
    }

    public function postMail(Request $request)
    {
        $toAddresses = explode(',', $request->email);
        $successAddress = [];
        foreach($toAddresses as $to) {
            $to = trim($to);
            $user = User::where('email', $to)->first();
            if( !is_null($user)) {
                Mail::to($user)->send(new EmailSendTest());
                array_push($successAddress, $to);
            }
        }

        return redirect(route('admin.email'))->with('successAddress', $successAddress);
    }
}
