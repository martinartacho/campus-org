{{-- resources/views/components/dashboard/widgets/task_board.blade.php --}}

@isset($stats['task_boards'])
<div class="bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
        <i class="bi bi-kanban text-blue-600 me-2"></i>
        Tauler de Tasques
    </h3>
    
    <div class="grid grid-cols-2 gap-4">
        <div class="text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $stats['total_boards'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Taulers totals</div>
        </div>
        <div class="text-center">
            <div class="text-2xl font-bold text-green-600">{{ $stats['active_boards'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Actius</div>
        </div>
        <div class="text-center">
            <div class="text-2xl font-bold text-purple-600">{{ $stats['total_tasks'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Tasques totals</div>
        </div>
        <div class="text-center">
            <div class="text-2xl font-bold text-orange-600">{{ $stats['completed_tasks'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Completades</div>
        </div>
    </div>
    
    <div class="mt-4">
        <a href="{{ route('task.boards.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
            Gestionar Taulers ->
        </a>
    </div>
</div>
@endif
