<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Medium;
use App\Http\Resources\Medium as MediumResource;

class AssessController extends Controller
{
    public function show()
    {
        return view('assess');
    }
}
