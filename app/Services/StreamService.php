<?php

namespace App\Services;

use App\Models\Stream;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class StreamService
{
    public function startStream(Stream $stream): bool
    {
        try {
            if ($stream->status === Stream::STATUS_RUNNING) {
                return true;
            }

            $command = explode(' ', $stream->ffmpeg_command);
            $process = new Process($command);
            $process->setOptions(['create_new_console' => true]);
            $process->start();

            $stream->update([
                'status' => Stream::STATUS_RUNNING,
                'process_id' => $process->getPid(),
                'last_error' => null
            ]);

            Log::info("Stream started", [
                'stream_id' => $stream->id,
                'command' => $stream->ffmpeg_command
            ]);

            return true;
        } catch (\Exception $e) {
            $this->handleStreamError($stream, $e->getMessage());
            return false;
        }
    }

    public function stopStream(Stream $stream): bool
    {
        try {
            if ($stream->status !== Stream::STATUS_RUNNING || !$stream->process_id) {
                return true;
            }

            $process = new Process(['taskkill', '/F', '/PID', (string)$stream->process_id]);
            $process->run();

            $stream->update([
                'status' => Stream::STATUS_STOPPED,
                'process_id' => null
            ]);

            Log::info("Stream stopped", ['stream_id' => $stream->id]);
            return true;
        } catch (\Exception $e) {
            $this->handleStreamError($stream, $e->getMessage());
            return false;
        }
    }

    public function restartStream(Stream $stream): bool
    {
        return $this->stopStream($stream) && $this->startStream($stream);
    }

    public function fixStream(Stream $stream): bool
    {
        try {
            if ($stream->status === Stream::STATUS_RUNNING) {
                $this->stopStream($stream);
            }

            $stream->update([
                'status' => Stream::STATUS_STOPPED,
                'process_id' => null,
                'last_error' => null
            ]);

            return $this->startStream($stream);
        } catch (\Exception $e) {
            $this->handleStreamError($stream, $e->getMessage());
            return false;
        }
    }

    private function handleStreamError(Stream $stream, string $error): void
    {
        $stream->update([
            'status' => Stream::STATUS_ERROR,
            'process_id' => null,
            'last_error' => $error
        ]);

        Log::error("Stream error", [
            'stream_id' => $stream->id,
            'error' => $error
        ]);
    }
}