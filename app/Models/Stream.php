<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stream extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'rtsp_url',
        'rtmp_url',
        'resolution',
        'status',
        'autostart',
        'process_id',
        'last_error'
    ];

    protected $casts = [
        'autostart' => 'boolean',
        'status' => 'string',
        'process_id' => 'integer',
    ];

    public const RESOLUTIONS = [
        '480p' => '854x480',
        '720p' => '1280x720',
        '1080p' => '1920x1080',
        '1440p' => '2560x1440',
        '4k' => '3840x2160'
    ];

    public const STATUS_STOPPED = 'stopped';
    public const STATUS_RUNNING = 'running';
    public const STATUS_ERROR = 'error';

    public function getResolutionSettingsAttribute(): array
    {
        $settings = config('resolution.' . strtolower($this->resolution));
        if (!$settings) {
            throw new \RuntimeException('Invalid resolution settings');
        }
        return $settings;
    }

    public function getFfmpegCommandAttribute(): string
    {
        $settings = $this->resolution_settings;
        return sprintf(
            'ffmpeg -f lavfi -i anullsrc -rtsp_transport tcp -thread_queue_size 512 -i "%s" '
            . '-vcodec libx264 -preset %s -tune %s -s %s -r 20 -threads %d '
            . '-x264opts "subme=0:me_range=4:rc_lookahead=10:me=dia:no_chroma_me:8x8dct=0:partitions=none" '
            . '-b:v %dk -maxrate %dk -bufsize %dk '
            . '-acodec aac -ar 44100 -b:a 16k -strict experimental -f flv "%s"',
            $this->rtsp_url,
            config('ffmpeg.preset'),
            config('ffmpeg.tune'),
            $settings['resolution'],
            config('ffmpeg.threads'),
            $settings['bitrate'],
            $settings['maxrate'],
            $settings['bufsize'],
            $this->rtmp_url
        );
    }
}