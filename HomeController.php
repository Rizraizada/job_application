<?php

namespace App\Http\Controllers;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function redirect()
    {

        $departments = Department::all();
         $user = Auth::user();

        //  dd($user);

         if ($user && $user->user_type == '1') {
            return view('admin.home');
        } else {
            return view('dashboard', compact('departments'));
        }
    }


}
