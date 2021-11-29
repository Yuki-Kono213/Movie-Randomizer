<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class LogOutController extends Controller
{
    //   
    /**
    * ログアウトしたときの画面遷移先
    */
    public function loggedOut()
    {
        Auth::logout();
        return redirect('/');
    }
}
