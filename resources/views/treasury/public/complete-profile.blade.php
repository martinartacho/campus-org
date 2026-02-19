@extends('campus.shared.layout')

@section('content')
<div class="max-w-2xl mx-auto p-6 bg-white rounded shadow">
    <h1 class="text-xl font-bold mb-4">Completar dades del professor</h1>

    <form method="POST" action="{{ route('campus.treasury.teacher.public.profile', $token) }}">
        @csrf

        <h2 class="font-semibold mt-4">Dades bàsiques</h2>
        <input name="first_name" value="{{ $teacher->first_name }}" required class="input">
        <input name="last_name" value="{{ $teacher->last_name }}" required class="input">
        <input name="email" value="{{ $teacher->user->email }}" required class="input">

        <h2 class="font-semibold mt-4">RGPD</h2>
        <label>
            <input type="checkbox" name="consent_rgpd" required>
            Accepto el consentiment RGPD
        </label>

        <h2 class="font-semibold mt-4">Dades financeres</h2>
       {{--  <label>
            <input type="checkbox" name="needs_payment" value="1">
            Necessita pagaments
        </label> --}}

        <input name="dni" placeholder="DNI / NIE" class="input">
        <input name="postal_code" placeholder="Codi postal" class="input">
        <input name="iban" placeholder="IBAN" class="input">
        <input name="bank_holder" placeholder="Titular compte" class="input">

        <button class="mt-4 px-4 py-2 bg-green-600 text-white rounded">
            Enviar
        </button>
    </form>
</div>
@endsection
