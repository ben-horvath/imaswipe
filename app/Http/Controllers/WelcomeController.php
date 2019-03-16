<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Medium;
use App\Http\Resources\Medium as MediumResource;

class WelcomeController extends Controller
{
    public function show()
    {
        return view('welcome');
    }

    public function startWith($name)
    {
        $first_medium = $name;

        return view('welcome', compact('first_medium'));
    }
}
