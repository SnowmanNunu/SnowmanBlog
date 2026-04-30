<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class StorageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cloud';

    protected static ?string $navigationLabel = '存储设置';

    protected static ?string $title = '云存储设置';

    protected static ?string $slug = 'storage-settings';

    protected static string $view = 'filament.pages.storage-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'media_disk' => Setting::get('media_disk', config('filesystems.media_disk', 'public')),

            // OSS
            'oss_access_key_id' => Setting::get('oss_access_key_id', ''),
            'oss_access_key_secret' => Setting::get('oss_access_key_secret', ''),
            'oss_bucket' => Setting::get('oss_bucket', ''),
            'oss_endpoint' => Setting::get('oss_endpoint', ''),
            'oss_cdn_domain' => Setting::get('oss_cdn_domain', ''),

            // COS
            'cos_secret_id' => Setting::get('cos_secret_id', ''),
            'cos_secret_key' => Setting::get('cos_secret_key', ''),
            'cos_bucket' => Setting::get('cos_bucket', ''),
            'cos_region' => Setting::get('cos_region', ''),
            'cos_cdn' => Setting::get('cos_cdn', ''),

            // Qiniu
            'qiniu_access_key' => Setting::get('qiniu_access_key', ''),
            'qiniu_secret_key' => Setting::get('qiniu_secret_key', ''),
            'qiniu_bucket' => Setting::get('qiniu_bucket', ''),
            'qiniu_domain' => Setting::get('qiniu_domain', ''),

            // S3
            'aws_access_key_id' => Setting::get('aws_access_key_id', ''),
            'aws_secret_access_key' => Setting::get('aws_secret_access_key', ''),
            'aws_default_region' => Setting::get('aws_default_region', 'us-east-1'),
            'aws_bucket' => Setting::get('aws_bucket', ''),
            'aws_endpoint' => Setting::get('aws_endpoint', ''),
            'aws_url' => Setting::get('aws_url', ''),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Section::make('存储驱动')
                    ->description('选择图片和附件的存储位置')
                    ->schema([
                        Select::make('media_disk')
                            ->label('当前存储驱动')
                            ->options([
                                'public' => '本地服务器 (public)',
                                's3' => 'AWS S3 / 兼容 S3',
                                'oss' => '阿里云 OSS',
                                'cos' => '腾讯云 COS',
                                'qiniu' => '七牛云',
                            ])
                            ->required()
                            ->live()
                            ->helperText('切换后立即生效，新上传的文件将保存到所选存储'),
                    ]),

                Section::make('阿里云 OSS')
                    ->visible(fn (Get $get) => $get('media_disk') === 'oss')
                    ->schema([
                        TextInput::make('oss_access_key_id')
                            ->label('Access Key ID')
                            ->required()
                            ->visible(fn (Get $get) => $get('media_disk') === 'oss'),
                        TextInput::make('oss_access_key_secret')
                            ->label('Access Key Secret')
                            ->password()
                            ->required()
                            ->revealable()
                            ->visible(fn (Get $get) => $get('media_disk') === 'oss'),
                        TextInput::make('oss_bucket')
                            ->label('Bucket 名称')
                            ->required()
                            ->visible(fn (Get $get) => $get('media_disk') === 'oss'),
                        TextInput::make('oss_endpoint')
                            ->label('Endpoint')
                            ->placeholder('oss-cn-hangzhou.aliyuncs.com')
                            ->required()
                            ->visible(fn (Get $get) => $get('media_disk') === 'oss'),
                        TextInput::make('oss_cdn_domain')
                            ->label('CDN 域名')
                            ->placeholder('https://your-cdn.example.com')
                            ->visible(fn (Get $get) => $get('media_disk') === 'oss'),
                    ])
                    ->collapsible(),

                Section::make('腾讯云 COS')
                    ->visible(fn (Get $get) => $get('media_disk') === 'cos')
                    ->schema([
                        TextInput::make('cos_secret_id')
                            ->label('Secret ID')
                            ->required()
                            ->visible(fn (Get $get) => $get('media_disk') === 'cos'),
                        TextInput::make('cos_secret_key')
                            ->label('Secret Key')
                            ->password()
                            ->required()
                            ->revealable()
                            ->visible(fn (Get $get) => $get('media_disk') === 'cos'),
                        TextInput::make('cos_bucket')
                            ->label('Bucket 名称')
                            ->required()
                            ->visible(fn (Get $get) => $get('media_disk') === 'cos'),
                        TextInput::make('cos_region')
                            ->label('Region')
                            ->placeholder('ap-guangzhou')
                            ->required()
                            ->visible(fn (Get $get) => $get('media_disk') === 'cos'),
                        TextInput::make('cos_cdn')
                            ->label('CDN 域名')
                            ->placeholder('https://your-cdn.example.com')
                            ->visible(fn (Get $get) => $get('media_disk') === 'cos'),
                    ])
                    ->collapsible(),

                Section::make('七牛云')
                    ->visible(fn (Get $get) => $get('media_disk') === 'qiniu')
                    ->schema([
                        TextInput::make('qiniu_access_key')
                            ->label('Access Key')
                            ->required()
                            ->visible(fn (Get $get) => $get('media_disk') === 'qiniu'),
                        TextInput::make('qiniu_secret_key')
                            ->label('Secret Key')
                            ->password()
                            ->required()
                            ->revealable()
                            ->visible(fn (Get $get) => $get('media_disk') === 'qiniu'),
                        TextInput::make('qiniu_bucket')
                            ->label('Bucket 名称')
                            ->required()
                            ->visible(fn (Get $get) => $get('media_disk') === 'qiniu'),
                        TextInput::make('qiniu_domain')
                            ->label('CDN 域名')
                            ->placeholder('https://your-domain.qiniudn.com')
                            ->required()
                            ->visible(fn (Get $get) => $get('media_disk') === 'qiniu'),
                    ])
                    ->collapsible(),

                Section::make('AWS S3')
                    ->visible(fn (Get $get) => $get('media_disk') === 's3')
                    ->schema([
                        TextInput::make('aws_access_key_id')
                            ->label('Access Key ID')
                            ->required()
                            ->visible(fn (Get $get) => $get('media_disk') === 's3'),
                        TextInput::make('aws_secret_access_key')
                            ->label('Secret Access Key')
                            ->password()
                            ->required()
                            ->revealable()
                            ->visible(fn (Get $get) => $get('media_disk') === 's3'),
                        TextInput::make('aws_default_region')
                            ->label('Region')
                            ->placeholder('us-east-1')
                            ->required()
                            ->visible(fn (Get $get) => $get('media_disk') === 's3'),
                        TextInput::make('aws_bucket')
                            ->label('Bucket 名称')
                            ->required()
                            ->visible(fn (Get $get) => $get('media_disk') === 's3'),
                        TextInput::make('aws_endpoint')
                            ->label('Endpoint')
                            ->placeholder('https://s3.amazonaws.com')
                            ->visible(fn (Get $get) => $get('media_disk') === 's3'),
                        TextInput::make('aws_url')
                            ->label('自定义 URL')
                            ->placeholder('https://cdn.example.com')
                            ->visible(fn (Get $get) => $get('media_disk') === 's3'),
                    ])
                    ->collapsible(),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Save media disk
        Setting::set('media_disk', $data['media_disk']);

        // Save OSS settings
        if ($data['media_disk'] === 'oss') {
            Setting::set('oss_access_key_id', $data['oss_access_key_id'] ?? '');
            Setting::set('oss_access_key_secret', $data['oss_access_key_secret'] ?? '');
            Setting::set('oss_bucket', $data['oss_bucket'] ?? '');
            Setting::set('oss_endpoint', $data['oss_endpoint'] ?? '');
            Setting::set('oss_cdn_domain', $data['oss_cdn_domain'] ?? '');
        }

        // Save COS settings
        if ($data['media_disk'] === 'cos') {
            Setting::set('cos_secret_id', $data['cos_secret_id'] ?? '');
            Setting::set('cos_secret_key', $data['cos_secret_key'] ?? '');
            Setting::set('cos_bucket', $data['cos_bucket'] ?? '');
            Setting::set('cos_region', $data['cos_region'] ?? '');
            Setting::set('cos_cdn', $data['cos_cdn'] ?? '');
        }

        // Save Qiniu settings
        if ($data['media_disk'] === 'qiniu') {
            Setting::set('qiniu_access_key', $data['qiniu_access_key'] ?? '');
            Setting::set('qiniu_secret_key', $data['qiniu_secret_key'] ?? '');
            Setting::set('qiniu_bucket', $data['qiniu_bucket'] ?? '');
            Setting::set('qiniu_domain', $data['qiniu_domain'] ?? '');
        }

        // Save S3 settings
        if ($data['media_disk'] === 's3') {
            Setting::set('aws_access_key_id', $data['aws_access_key_id'] ?? '');
            Setting::set('aws_secret_access_key', $data['aws_secret_access_key'] ?? '');
            Setting::set('aws_default_region', $data['aws_default_region'] ?? '');
            Setting::set('aws_bucket', $data['aws_bucket'] ?? '');
            Setting::set('aws_endpoint', $data['aws_endpoint'] ?? '');
            Setting::set('aws_url', $data['aws_url'] ?? '');
        }

        // Apply to runtime config immediately
        $this->applyStorageConfig();

        Notification::make()
            ->success()
            ->title('保存成功')
            ->body('存储设置已更新并生效')
            ->send();
    }

    private function applyStorageConfig(): void
    {
        $disk = Setting::get('media_disk', 'public');
        config()->set('filesystems.media_disk', $disk);

        if ($disk === 'oss') {
            config()->set('filesystems.disks.oss.access_id', Setting::get('oss_access_key_id'));
            config()->set('filesystems.disks.oss.access_secret', Setting::get('oss_access_key_secret'));
            config()->set('filesystems.disks.oss.bucket', Setting::get('oss_bucket'));
            config()->set('filesystems.disks.oss.endpoint', Setting::get('oss_endpoint'));
            config()->set('filesystems.disks.oss.cdn_domain', Setting::get('oss_cdn_domain'));
        }

        if ($disk === 'cos') {
            config()->set('filesystems.disks.cos.credentials.secret_id', Setting::get('cos_secret_id'));
            config()->set('filesystems.disks.cos.credentials.secret_key', Setting::get('cos_secret_key'));
            config()->set('filesystems.disks.cos.bucket', Setting::get('cos_bucket'));
            config()->set('filesystems.disks.cos.region', Setting::get('cos_region'));
            config()->set('filesystems.disks.cos.cdn', Setting::get('cos_cdn'));
        }

        if ($disk === 'qiniu') {
            config()->set('filesystems.disks.qiniu.access_key', Setting::get('qiniu_access_key'));
            config()->set('filesystems.disks.qiniu.secret_key', Setting::get('qiniu_secret_key'));
            config()->set('filesystems.disks.qiniu.bucket', Setting::get('qiniu_bucket'));
            config()->set('filesystems.disks.qiniu.domain', Setting::get('qiniu_domain'));
        }

        if ($disk === 's3') {
            config()->set('filesystems.disks.s3.key', Setting::get('aws_access_key_id'));
            config()->set('filesystems.disks.s3.secret', Setting::get('aws_secret_access_key'));
            config()->set('filesystems.disks.s3.region', Setting::get('aws_default_region'));
            config()->set('filesystems.disks.s3.bucket', Setting::get('aws_bucket'));
            config()->set('filesystems.disks.s3.endpoint', Setting::get('aws_endpoint'));
            config()->set('filesystems.disks.s3.url', Setting::get('aws_url'));
        }
    }
}
