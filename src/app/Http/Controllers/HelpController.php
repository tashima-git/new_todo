<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelpController extends Controller
{
    /**
     * ヘルプ画面
     * GET /help
     */
    public function index()
    {
        return view('help.index');
    }
}
