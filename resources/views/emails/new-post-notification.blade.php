@component('mail::message')
# {{ $post->title }}

{{ $post->excerpt ?: Str::limit(strip_tags(Str::markdown($post->content)), 200) }}

@component('mail::button', ['url' => route('blog.show', $post->slug)])
阅读全文
@endcomponent

---

如果你不想再收到更新通知，可以点击[退订]({{ route('subscribe.destroy', $subscriber->unsubscribe_token) }})。

{{ config('app.name') }}
@endcomponent
