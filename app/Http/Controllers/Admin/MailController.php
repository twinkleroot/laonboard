<?php

namespace App\Http\Controllers\Admin;

use Mail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\MailSendTest;
use App\User;

class MailController extends Controller
{
    public function index()
    {
        return view('admin.mail.index');
    }

    public function postMail(Request $request)
    {
        $toAddresses = explode(',', $request->email);
        $successAddress = [];
        foreach($toAddresses as $to) {
            $to = trim($to);
            $user = User::where('email', $to)->first();
            if( !is_null($user)) {
                Mail::to($user)->send(new MailSendTest());
                array_push($successAddress, $to);
            }
        }

        return redirect(route('admin.mail'))->with('successAddress', $successAddress);
    }
}
