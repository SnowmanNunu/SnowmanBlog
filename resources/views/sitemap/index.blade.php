{{ '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' }}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ now()->toDateString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ route('guestbook.index') }}</loc>
        <lastmod>{{ now()->toDateString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.5</priority>
    </url>
    @foreach( as )
    <url>
        <loc>{{ route('blog.category', ->slug) }}</loc>
        <lastmod>{{ ->updated_at->toDateString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach
    @foreach( as )
    <url>
        <loc>{{ route('blog.tag', ->slug) }}</loc>
        <lastmod>{{ ->updated_at->toDateString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.5</priority>
    </url>
    @endforeach
    @foreach( as )
    <url>
        <loc>{{ route('blog.show', ->slug) }}</loc>
        <lastmod>{{ ->updated_at->toDateString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach
</urlset>