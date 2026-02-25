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
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'type' => 'required|in:service,incident,improvement,consultation',
            'description' => 'required|string|min:10',
            'urgency' => 'required|in:low,medium,high,critical',
            'module' => 'nullable|string|max:255',
            'url' => 'nullable|url',
            'department' => 'nullable|string|max:255',
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
            'status'     => 'pending',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Sol·licitud enviada correctament.');
    }
}
