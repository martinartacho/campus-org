@extends('campus.shared.layout')

@section('content')
<h1 class="text-xl font-semibold mb-4">
    Historial de consentiments RGPD
</h1>

<p class="mb-4">
    <strong>{{ $teacher->name }}</strong> – {{ $teacher->email }}
</p>

<table class="table-auto w-full border">
    <thead class="bg-gray-100">
        <tr>
            <th class="p-2">Temporada</th>
            <th class="p-2">Acceptat</th>
            <th class="p-2">Checksum</th>
            <th class="p-2">Document</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($consents as $consent)
            <tr class="border-t">
                <td class="p-2">{{ $consent->season }}</td>
                <td class="p-2">{{ $consent->accepted_at->format('d/m/Y H:i') }}</td>
                <td class="p-2 text-xs">{{ $consent->checksum }}</td>
                <td class="p-2">
                    <a
                        href="{{ route('consents.download', ['consent' => $consent->id]) }}"
                        class="text-blue-600 underline"
                    >
                        Descarregar PDF
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<a href="{{ route('campus.treasury.teachers.show', $teacher) }}"
   class="inline-block mt-4 text-sm underline">
    ← Tornar
</a>
@endsection
