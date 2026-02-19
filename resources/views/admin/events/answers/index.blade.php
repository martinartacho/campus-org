<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('site.Answers for Event') }}: {{ $event->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Botones de acción -->
                    <div class="flex justify-between items-center mb-6">
                        <a href="{{ route('admin.events.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('site.Back to Events') }}
                        </a>

                        <div class="space-x-2">
                            <a href="{{ route('admin.events.answers.export', ['event' => $event->id, 'format' => 'pdf']) }}"                            
                               class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                {{ __('site.Download PDF') }}
                            </a>
                            <a href="{{ route('admin.events.answers.export', ['event' => $event->id, 'format' => 'excel']) }}" 
                               class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                {{ __('site.Download Excel') }}
                            </a>
                            <a href="{{ route('admin.events.answers.print', $event->id) }}" 
                               target="_blank"
                               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                {{ __('site.Print View') }} 
                            </a>
                        </div>
                    </div>

                   @if(count($groupedAnswers) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('site.User') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('site.Email') }}
                                    </th>
                                    @foreach($questions as $question)
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ $question->question }}
                                    </th>
                                    @endforeach
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('site.Submission Date') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($groupedAnswers as $userId => $userAnswers)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $userAnswers['user']->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $userAnswers['user']->email }}
                                    </td>
                                    @foreach($questions as $question)
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                           
                                            @php
                                                $answer = $userAnswers['answers']->firstWhere('question_id', $question->id);
                                            @endphp
                                            {{ $answer->answer ?? '-' }}
                                        </td>
                                    @endforeach
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @php
                                            $firstAnswer = collect($userAnswers['answers'])->first();
                                        @endphp
                                        {{ $firstAnswer->created_at->format('Y-m-d H:i') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <p class="text-gray-500">{{ __('site.No answers found for this event.') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>