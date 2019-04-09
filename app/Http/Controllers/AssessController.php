<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Medium;
use App\Http\Resources\Medium as MediumResource;

class AssessController extends Controller
{
    public function show()
    {
        if (Auth::user()->isAdmin()) {
            return view('assess');
        } else {
            return redirect()->route('welcome');
        }
    }
}
