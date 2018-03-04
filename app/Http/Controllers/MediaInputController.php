<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\ProcessMediaInputFolder;

class MediaInputController extends Controller
{
    public function process()
    {
        ProcessMediaInputFolder::dispatch();
    }
}
