<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StatusController extends Controller
{
    /**
     * ステータス画面
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // ここで統計や履歴などを読みたくなったら後で追加すればOK
        return view('status.index', compact('user'));
    }
}
