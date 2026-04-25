<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>新评论通知</title>
</head>
<body style="font-family:system-ui,-apple-system,sans-serif;line-height:1.6;color:#333;max-width:600px;margin:0 auto;padding:20px;">
    <h2 style="color:#2563eb;border-bottom:2px solid #e5e7eb;padding-bottom:10px;">您的文章收到了新评论</h2>

    <table style="width:100%;border-collapse:collapse;margin:20px 0;">
        <tr>
            <td style="padding:8px 0;color:#6b7280;width:80px;">文章</td>
            <td style="padding:8px 0;">
                <a href="{{ route('blog.show', $comment->post->slug) }}" style="color:#2563eb;text-decoration:none;">{{ $comment->post->title }}</a>
            </td>
        </tr>
        <tr>
            <td style="padding:8px 0;color:#6b7280;">昵称</td>
            <td style="padding:8px 0;">{{ $comment->nickname }}</td>
        </tr>
        <tr>
            <td style="padding:8px 0;color:#6b7280;">邮箱</td>
            <td style="padding:8px 0;">{{ $comment->email ?: '未填写' }}</td>
        </tr>
        <tr>
            <td style="padding:8px 0;color:#6b7280;vertical-align:top;">内容</td>
            <td style="padding:8px 0;background:#f9fafb;border-left:4px solid #e5e7eb;padding:12px;border-radius:4px;">
                {{ $comment->content }}
            </td>
        </tr>
    </table>

    <p style="color:#6b7280;font-size:14px;">请登录后台审核该评论。</p>
    <p style="margin-top:20px;">
        <a href="{{ url('/admin') }}" style="display:inline-block;padding:10px 20px;background:#2563eb;color:#fff;text-decoration:none;border-radius:6px;">进入后台</a>
    </p>
</body>
</html>