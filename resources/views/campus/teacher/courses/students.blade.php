@extends('campus.shared.layout')

@section('title', 'Alumnos del curso')

@section('content')
    <h2 class="text-xl font-semibold mb-4">{{ $course->name }}</h2>

    <table class="w-full bg-white shadow rounded">
        <thead>
        <tr class="text-left border-b">
            <th class="p-3">Alumno</th>
            <th class="p-3">Email</th>
        </tr>
        </thead>
        <tbody>
        @foreach($students as $reg)
            <tr class="border-b">
                <td class="p-3">{{ $reg->student->user->name }}</td>
                <td class="p-3">{{ $reg->student->user->email }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
