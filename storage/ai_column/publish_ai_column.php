<?php
require_once '/www/wwwroot/snowmanblog/vendor/autoload.php';
$app = require_once '/www/wwwroot/snowmanblog/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$dir = '/www/wwwroot/snowmanblog/storage/ai_column';
$progressFile = $dir . '/.progress';
$files = glob($dir . '/ai_col_*.md');
sort($files);

if (empty($files)) {
    echo "NO_ARTICLES\n";
    exit(0);
}

$index = 0;
if (file_exists($progressFile)) {
    $index = (int) file_get_contents($progressFile);
}

if ($index >= count($files)) {
    echo "ALL_PUBLISHED\n";
    exit(0);
}

$file = $files[$index];
$content = file_get_contents($file);

// Extract title from first H1
preg_match('/^# (.+)$/m', $content, $matches);
$title = trim($matches[1] ?? 'Untitled');

// Generate slug
$slugBase = [
    'ai-agent-basics',
    'mcp-protocol-guide',
    'prompt-engineering-advanced',
    'claude-api-production',
    'rag-retrieval-augmented',
    'ai-workflow-orchestration',
    'ai-cost-optimization',
];
$slug = $slugBase[$index] ?? 'ai-column-' . ($index + 1);

// Extract excerpt from first paragraph after title
preg_match('/^# .+\n+(.+?)(?:\n\n|\n#{1,6})/s', $content, $excerptMatch);
$excerpt = trim($excerptMatch[1] ?? '');
$excerpt = strip_tags($excerpt);
if (strlen($excerpt) > 200) {
    $excerpt = mb_substr($excerpt, 0, 200) . '...';
}

// Check if already exists
$existing = App\Models\Post::where('slug', $slug)->first();
if ($existing) {
    echo "EXISTS: {$slug}\n";
    file_put_contents($progressFile, $index + 1);
    exit(0);
}

$post = new App\Models\Post();
$post->category_id = 6;
$post->series_id = 2; // 开源项目
$post->user_id = 1;
$post->title = $title;
$post->slug = $slug;
$post->meta_title = $title;
$post->meta_description = $excerpt;
$post->excerpt = $excerpt;
$post->content = $content;
$post->is_published = 1;
$post->is_pinned = 0;
$post->published_at = now();
$post->save();

// Clear caches
Artisan::call('cache:clear');
Artisan::call('view:clear');

file_put_contents($progressFile, $index + 1);
echo "PUBLISHED: {$title} ({$slug})\n";
