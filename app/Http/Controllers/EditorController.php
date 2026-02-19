<?php

namespace App\Http\Controllers;

class EditorController extends Controller
{
    public function index()
    {
        return view('editor.panel', [
            'title' => 'Panel/Dashboard Edición'
        ]);
    }
}
