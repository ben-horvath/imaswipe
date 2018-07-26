<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Medium;
use App\Http\Resources\Medium as MediumResource;

class AssessController extends Controller
{
    public function show()
    {
        $media = Medium::where('approved', null)->limit(2)->get();

        if (!empty($media[0])) {
            $initial_medium = (new MediumResource($media[0]))->toArray($media[0]);
        } else {
            $initial_medium = null;
        }
        if (!empty($media[1])) {
            $next_medium = (new MediumResource($media[1]))->toArray($media[1]);
        } else {
            $next_medium = null;
        }

        return view('assess', compact('initial_medium', 'next_medium'));
    }
}
