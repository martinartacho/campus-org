{{-- resources/views/components/dashboard/widgets/recent_registrations.blade.php --}}

<div class="bg-white p-6 rounded-lg shadow-md mb-6">
    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
        <i class="bi bi-clock-history me-2 text-green-600"></i>
        Matriculacions Recents (widgets/recent_registrations linia 6)
    </h2>

    @php
        $recentRegistrations = \App\Models\CampusCourseStudent::with(['student', 'course'])
            ->latest('enrollment_date')
            ->limit(10)
            ->get();
    @endphp

    <div class="space-y-2">
        @forelse($recentRegistrations as $registration)
            <div class="flex justify-between items-center text-sm border-b pb-2">
                <div class="flex-1">
                    <div class="font-medium">{{ $registration->student->name ?? '-' }}</div>
                    <div class="text-gray-500">{{ $registration->course->title ?? '-' }}</div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="px-2 py-1 text-xs rounded-full 
                        @if($registration->status == 'confirmed') bg-green-100 text-green-800
                        @elseif($registration->status == 'pending') bg-yellow-100 text-yellow-800
                        @elseif($registration->status == 'cancelled') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ $registration->status }}
                    </span>
                    <div class="text-gray-400 text-xs">
                        {{ $registration->created_at->diffForHumans() }}
                    </div>
                </div>
            </div>
        @empty
            <p class="text-gray-500 text-center py-4">No hi ha matriculacions recents</p>
        @endforelse
    </div>

    @if($recentRegistrations->count() > 0)
        <div class="mt-4 text-center">
            <a href="{{ route('campus.registrations.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                Veure totes les matriculacions →
            </a>
        </div>
    @endif
</div>
