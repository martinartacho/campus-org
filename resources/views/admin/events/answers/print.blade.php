<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('site.Events') }} - {{ config('app.name', 'Laravel') }}</title>    
    
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .header { text-align: center; margin-bottom: 20px; }
        @media print {
            .no-print { display: none; }
            body { margin: 0; padding: 15px; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1> {{ config('app.name', 'Laravel') }}</h1>
        <h2>{{ __('site.Answers for Event') }} {{ $event->title }}</h2>
        <p>Generated on: {{ now()->format('Y-m-d H:i') }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>{{ __('User') }}</th>
                <th>{{ __('Email') }}</th>
                @foreach($questions as $question)
                <th>{{ $question->question }}</th>
                @endforeach
                <th>{{ __('site.Submission Date') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($groupedAnswers as $userId => $userAnswers)
            <tr>
                <td>{{ $userAnswers['user']->name }}</td>
                <td>{{ $userAnswers['user']->email }}</td>
                @foreach($questions as $question)
                   @foreach($questions as $question)
                    <td class="px-6 py-4 text-sm text-gray-900">
                        @php
                            $answer = $userAnswers['answers']->firstWhere('question_id', $question->id);
                        @endphp
                        {{ $answer->answer ?? '-' }}
                    </td>
                @endforeach
                @endforeach
                <td>{{ $userAnswers['answers']->first()->created_at->format('Y-m-d H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            {{ __('Print') }}
        </button>
        <button onclick="window.close()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            {{ __('Close') }}
        </button>
    </div>
</body>
</html>