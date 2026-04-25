@php echo '<' . '?xml version="1.0" encoding="UTF-8"?' . '>'; @endphp
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:content="http://purl.org/rss/1.0/modules/content/">
    <channel>
        <title>{{ $siteTitle }}</title>
        <link>{{ url('/') }}</link>
        <description>{{ $siteDescription }}</description>
        <language>zh-CN</language>
        <lastBuildDate>{{ now()->toRssString() }}</lastBuildDate>
        <atom:link href="{{ route('rss') }}" rel="self" type="application/rss+xml" />
        @foreach($posts as $post)
        <item>
            <title>{{ $post->title }}</title>
            <link>{{ route('blog.show', $post->slug) }}</link>
            <guid isPermaLink="true">{{ route('blog.show', $post->slug) }}</guid>
            <pubDate>{{ $post->published_at->toRssString() }}</pubDate>
            <author>{{ $post->user->name }}</author>
            <category>{{ $post->category->name }}</category>
            <description><![CDATA[{{ $post->excerpt ?: strip_tags(Str::markdown($post->content)) }}]]></description>
        </item>
        @endforeach
    </channel>
</rss>