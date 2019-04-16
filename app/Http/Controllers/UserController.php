<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function upgrade(Request $request)
    {
        DB::insert('insert into surveys (query, user_id, answer, created_at, updated_at) values (?, ?, ?, ?, ?)', ['How much would you pay for the upgrade', Auth::user()->id, '{"amount": ' . $request->amount . '}', now(), now()]);
    }
}
