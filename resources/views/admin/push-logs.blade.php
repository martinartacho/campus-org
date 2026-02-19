<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
           {{ __('site.Push') }}
        </h2>
    </x-slot>

    <div class="py-10 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-medium mb-4">{{ __('site.Push log files') }}</h3>

            @if($logs->isEmpty())
                <p>{{ __('site.No logs available') }}</p>
            @else
                <ul class="list-disc list-inside space-y-2">
                    @foreach ($logs as $log)
                        <li class="flex items-center justify-between">
                            <span>{{ $log->getFilename() }} ({{ \Carbon\Carbon::createFromTimestamp($log->getCTime())->diffForHumans() }})</span>
                            <a href="{{ route('push.logs.download', $log->getFilename()) }}" class="text-blue-600 hover:underline">
                                {{ __('site.Download') }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</x-app-layout>
