<?php

namespace App\Http\Controllers;

use App\Models\SupportRequest;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function create(Request $request)
    {
        return view('support.form', [
            'user'   => auth()->user(),
            'module' => $request->get('module'),
            'url'    => url()->previous(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'description' => 'required|string|min:10',
            'email' => 'required|email',
        ]);

        SupportRequest::create([
            'user_id'    => auth()->id(),
            'name'       => $request->name,
            'email'      => $request->email,
            'department' => $request->department,
            'type'       => $request->type,
            'description'=> $request->description,
            'module'     => $request->module,
            'url'        => $request->url,
            'urgency'    => $request->urgency,
            'status'     => 'open',
        ]);

        return back()->with('success', 'Sol·licitud enviada correctament.');
    }
}
