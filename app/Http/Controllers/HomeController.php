<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

    //     if( Auth::user()->is_admin == NULL )
    //     {
    //         echo "user";
    //     }else{
    //         echo "admin";
    //     }
        
    //  exit;
        // echo  USER_ROLE ; exit;
        return view('home');
    }
    
}
