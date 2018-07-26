<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Medium;
use App\Http\Resources\Medium as MediumResource;

class WelcomeController extends Controller
{
    public function show()
    {
        $media = Medium::where('approved', true)->inRandomOrder()->limit(2)->get();

        $initial_medium = (new MediumResource($media[0]))->toArray($media[0]);
        $next_medium = (new MediumResource($media[1]))->toArray($media[1]);

        return view('welcome', compact('initial_medium', 'next_medium'));
    }

    public function startWith($name)
    {
        $first_m = Medium::where('name', $name)->first();
        $second_m = Medium::where('approved', true)->inRandomOrder()->first();

        $initial_medium = (new MediumResource($first_m))->toArray($first_m);
        $next_medium = (new MediumResource($second_m))->toArray($second_m);

        return view('welcome', compact('initial_medium', 'next_medium'));
    }
}
