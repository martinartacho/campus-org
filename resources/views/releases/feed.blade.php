@php
header('Content-Type: application/xml');
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>Campus UPG - Release Notes</title>
        <description>Ultimes novetats, millores i correccions de Campus UPG</description>
        <link>{{ url('/releases') }}</link>
        <atom:link href="{{ url('/releases/feed') }}" rel="self" type="application/rss+xml" />
        <language>ca</language>
        <lastBuildDate>{{ now()->format('D, d M Y H:i:s O') }}</lastBuildDate>
        
        @foreach($releases as $release)
            <item>
                <title>{{ $release->title }}</title>
                <link>{{ url('/releases/' . $release->slug) }}</link>
                <guid>{{ url('/releases/' . $release->slug) }}</guid>
                <pubDate>{{ $release->published_at->format('D, d M Y H:i:s O') }}</pubDate>
                <description>
                    <![CDATA[
                        <div>
                            <p><strong>{{ $release->summary }}</strong></p>
                            <p>Tipus: {{ $release->type }}</p>
                            <p>Commits: {{ $release->getCommitCount() }}</p>
                            @if(!empty($release->affected_modules))
                                <p>Moduls: {{ implode(', ', $release->affected_modules) }}</p>
                            @endif
                            @if($release->hasBreakingChanges())
                                <p style="color: red;">⚠️ Conte canvis disruptius!</p>
                            @endif
                            <hr>
                            <div>{{ Str::limit(strip_tags($release->content), 500) }}</div>
                        </div>
                    ]]>
                </description>
                <category>{{ $release->type }}</category>
                @if(!empty($release->affected_modules))
                    @foreach($release->affected_modules as $module)
                        <category>{{ $module }}</category>
                    @endforeach
                @endif
            </item>
        @endforeach
    </channel>
</rss>
