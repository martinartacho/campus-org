<?php

namespace App\Http\Controllers\Campus;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'Test controller works!']);
    }
}
