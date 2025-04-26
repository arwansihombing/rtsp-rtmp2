<?php

return [
    // Jumlah thread yang digunakan FFmpeg
    'threads' => env('FFMPEG_THREADS', 4),

    // Preset encoding (ultrafast, superfast, veryfast, faster, fast, medium, slow, slower, veryslow)
    'preset' => env('FFMPEG_PRESET', 'ultrafast'),

    // Tune encoding untuk optimasi spesifik (film, animation, grain, stillimage, fastdecode, zerolatency)
    'tune' => env('FFMPEG_TUNE', 'zerolatency'),

    // Path log untuk output FFmpeg
    'log_path' => storage_path('logs/ffmpeg.log'),

    // Timeout untuk proses FFmpeg dalam detik (0 = tidak ada timeout)
    'process_timeout' => 0,

    // Pengaturan buffer untuk input RTSP
    'rtsp_buffer_size' => 512,

    // Frame rate default
    'frame_rate' => 20,

    // Pengaturan audio default
    'audio' => [
        'codec' => 'aac',
        'bitrate' => '16k',
        'sample_rate' => 44100,
    ],
];