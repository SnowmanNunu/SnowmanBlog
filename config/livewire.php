<?php

return [
    'class_namespace' => 'App\\Livewire',
    'view_path' => resource_path('views/livewire'),
    'layout' => 'components.layouts.app',
    'lazy_placeholder' => null,
    'temporary_file_upload' => [
        'disk' => null,
        'rules' => 'file|mimes:png,jpg,jpeg,gif,webp|max:10240',
        'directory' => null,
        'middleware' => null,
        'preview_mimes' => [
            'png', 'gif', 'bmp', 'svg', 'wmf',
            'jpg', 'jpeg', 'webp',
        ],
        'max_upload_time' => 5,
    ],
    'render_on_redirect' => false,
    'legacy_model_binding' => false,
];