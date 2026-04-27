<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>评论回复通知</title>
</head>
<body style="font-family:system-ui,-apple-system,sans-serif;line-height:1.6;color:#333;max-width:600px;margin:0 auto;padding:20px;">
    <h2 style="color:#2563eb;border-bottom:2px solid #e5e7eb;padding-bottom:10px;">您的评论收到了回复</h2>

    <table style="width:100%;border-collapse:collapse;margin:20px 0;">
        <tr>
            <td style="padding:8px 0;color:#6b7280;width:80px;">文章</td>
            <td style="padding:8px 0;">
                <a href="{{ route('blog.show', $reply->post->slug) }}" style="color:#2563eb;text-decoration:none;">{{ $reply->post->title }}</a>
            </td>
        </tr>
        <tr>
            <td style="padding:8px 0;color:#6b7280;vertical-align:top;">您的评论</td>
            <td style="padding:8px 0;background:#f9fafb;border-left:4px solid #e5e7eb;padding:12px;border-radius:4px;">
                {{ $parentComment->content }}
            </td>
        </tr>
        <tr>
            <td style="padding:8px 0;color:#6b7280;vertical-align:top;">回复内容</td>
            <td style="padding:8px 0;background:#f0f9ff;border-left:4px solid #2563eb;padding:12px;border-radius:4px;">
                <strong>{{ $reply->nickname }}</strong>：{{ $reply->content }}
            </td>
        </tr>
    </table>

    <p style="margin-top:20px;">
        <a href="{{ route('blog.show', $reply->post->slug) }}#comments" style="display:inline-block;padding:10px 20px;background:#2563eb;color:#fff;text-decoration:none;border-radius:6px;">查看回复</a>
    </p>
</body>
</html>