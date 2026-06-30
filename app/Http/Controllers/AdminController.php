<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Section;
use App\Models\Teacher;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    //
    public function showAdmins(){
        $users = User::all()->count();
        $teachers = Teacher::all()->count();
        $sections = Section::all()->count();
        return view('admin_dashboard',compact('users','teachers','sections'));
    }
}
