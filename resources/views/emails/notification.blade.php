<!DOCTYPE html>
<html>
<head>
    <title>{{ $notification->title }}</title>
</head>
<body>
    <h1>{{ $notification->title }}</h1>
    <div>
        {!! nl2br(e($notification->content)) !!}
    </div>
    
    <p>
        {{ __('site.Sent_by') }}: {{ $notification->sender->name }}<br>
        {{ $notification->created_at->format('d/m/Y H:i') }}
    </p>
</body>
</html>