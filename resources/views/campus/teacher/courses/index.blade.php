@extends('campus.shared.layout')

@section('title', 'Mis cursos')

@section('content')
    <x-dashboard-teacher-cards :teacherCourses="$courses"/>
@endsection
