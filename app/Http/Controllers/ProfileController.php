<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ChangePasswordRequest;
use App\Models\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Class PageController
 */
class ProfileController extends Controller
{
    protected $title    = 'Change Password';
    protected $viewPath = 'auth/change_password';
    protected $link     = 'change-password';

    /**
     * Display listing page.
     */
    public function index()
    {
        $page = collect();
        $page->title = $this->title;
        $page->link = url($this->link);
        $page->form_url = url($this->link);
        $page->form_method = 'POSt';
        return view($this->viewPath, compact('page'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ChangePasswordRequest $request)
    {
        // Auth::user()->shop->name;
        // Auth::user()->name ;
        // $user = Auth::user();
        // $user->update(['password' => bcrypt($request->new_password)]);
        // $url = url($this->link);
        // $error = false;
        // $message = Str::singular(Str::title($this->title)) . ' saved successfully';
        // return compact('error', 'message', 'url');
    }
}
