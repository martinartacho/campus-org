@extends('campus.shared.layout')


@section('title', __('campus.treasury'))
@section('subtitle', __('campus.treasury_management'))

@section('breadcrumbs')
    <li>
        <div class="flex items-center">
            <i class="bi bi-chevron-right text-gray-400 mx-1"></i>
            <a href="{{ route('campus.treasury.teachers.index') }}" class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2">
               {{ __('campus.teachers')}}
            </a>
        </div>
    </li>
@endsection

@section('content')
<h1 class="text-xl font-semibold mb-4">
    Professor – Dades Econòmiques
</h1>

@if (session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
        {{ session('success') }}
    </div>
@endif

<div class="mb-6">
    <h2 class="font-medium text-lg mb-2">Dades bàsiques</h2>

    <ul class="text-sm text-gray-700">
        <li><strong>Nom:</strong> {{ $teacher->name }}</li>
        <li><strong>Email:</strong> {{ $teacher->email }}</li>
    </ul>
</div>

<div class="mb-6">
    <h2 class="font-medium text-lg mb-2">Estat RGPD</h2>

    @php
        $consent = $teacher->treasuryData
            ->where('key', 'consent_signed_at')
            ->first();
    @endphp

    @if ($consent)
        <p class="text-green-700">
            ✅ Consentiment acceptat el {{ \Carbon\Carbon::parse($consent->value)->format('d/m/Y H:i') }}
        </p>
        
        @if ($teacher->latestConsent)

            <a href="{{ route('consents.download', $teacher->latestConsent) }}"
                class="inline-block mt-2 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">            
                ⬇️ Descarregar PDF del consentiment
            </a>
        @endif
    @else
        <p class="text-red-700 mb-4">
            ❌ Consentiment pendent
        </p>

        <form method="POST"
            action="{{ route('campus.treasury.teachers.consent.store', $teacher) }}">
            @csrf

            {{-- Senyal explícit d’acceptació --}}
            <input type="hidden" name="accept_consent" value="1">

            {{-- Delegació (només si NO és el professor) --}}
            @if(auth()->id() !== $teacher->id)
                <div class="bg-yellow-50 border border-yellow-200 p-4 rounded mb-4">
                    <label class="flex items-center gap-2 font-semibold">
                        <input type="checkbox" name="is_delegated" required>
                        <span>Actuo com a persona delegada del professor</span>
                    </label>

                    <textarea name="delegated_reason"
                            class="mt-2 w-full border rounded p-2"
                            placeholder="Motiu de la delegació (absència, no ús del sistema, etc.)"
                            required></textarea>
                </div>
            @endif

            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Registrar consentiment RGPD
            </button>
        </form>

    @endif
</div>

<a href="{{ route('campus.treasury.teachers.index') }}"
   class="text-sm text-gray-600 underline">
    ← Tornar al llistat
</a>
@endsection
