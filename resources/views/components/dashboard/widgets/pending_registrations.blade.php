{{-- resources/views/components/dashboard/widgets/pending_registrations.blade.php --}}

<div class="bg-white p-6 rounded-lg shadow-md mb-6">
    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
        <i class="bi bi-hourglass-split me-2 text-orange-600"></i>
        Matriculacions Pendents (widgets/pending_registrations linia 6)
    </h2>

    @php
        $pendingRegistrations = \App\Models\CampusCourseStudent::with(['student', 'course'])
            ->where('academic_status', 'enrolled')
            ->latest('enrollment_date')
            ->limit(10)
            ->get();
    @endphp

    <div class="mb-4">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-600">
                Total pendents: <span class="font-bold text-orange-600">{{ $pendingRegistrations->count() }}</span>
            </div>
            @if($pendingRegistrations->count() > 0)
        <div class="mt-4 text-center">
            <a href="{{ route('campus.students.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                Veure totes les matriculacions →
            </a>
        </div>
    @endif
        </div>
    </div>

    <div class="space-y-2">
        @forelse($pendingRegistrations as $registration)
            <div class="flex justify-between items-center text-sm border-b pb-2">
                <div class="flex-1">
                    <div class="font-medium">{{ $registration->student->name ?? '-' }}</div>
                    <div class="text-gray-500 text-xs">{{ $registration->course->title ?? '-' }}</div>
                    <div class="text-gray-400 text-xs">
                        {{ $registration->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>
                <div class="flex space-x-1">
                    @can('campus.registrations.manage')
                        <form action="{{ route('campus.registrations.update', $registration->id) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="confirmed">
                            <button type="submit" class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded hover:bg-green-200"
                                    onclick="return confirm('Confirmar matriculació?')">
                                ✓
                            </button>
                        </form>
                        <form action="{{ route('campus.registrations.update', $registration->id) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded hover:bg-red-200"
                                    onclick="return confirm('Cancel·lar matriculació?')">
                                ✗
                            </button>
                        </form>
                    @endcan
                </div>
            </div>
        @empty
            <div class="text-center py-8 text-gray-500">
                <i class="bi bi-check-circle text-4xl text-green-500 mb-2"></i>
                <p class="text-sm">No hi ha matriculacions pendents</p>
            </div>
        @endforelse
    </div>
</div>
