<?php

namespace App\Http\Controllers;

use App\Models\Stream;
use App\Services\StreamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class StreamController extends Controller
{
    private StreamService $streamService;

    public function __construct(StreamService $streamService)
    {
        $this->streamService = $streamService;
    }

    public function index()
    {
        $streams = Stream::latest()->get();
        return view('streams.index', compact('streams'));
    }

    public function create()
    {
        return view('streams.create', [
            'resolutions' => Stream::RESOLUTIONS
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'rtsp_url' => 'required|string|max:255',
            'rtmp_url' => 'required|string|max:255',
            'resolution' => ['required', Rule::in(array_keys(Stream::RESOLUTIONS))],
            'autostart' => 'boolean'
        ]);

        $stream = Stream::create($validated);

        if ($stream->autostart) {
            $this->streamService->startStream($stream);
        }

        return redirect()->route('streams.index')
            ->with('success', 'Stream berhasil dibuat.');
    }

    public function start(Stream $stream)
    {
        if ($this->streamService->startStream($stream)) {
            return response()->json(['message' => 'Stream berhasil dijalankan']);
        }
        return response()->json(['message' => 'Gagal menjalankan stream'], 500);
    }

    public function stop(Stream $stream)
    {
        if ($this->streamService->stopStream($stream)) {
            return response()->json(['message' => 'Stream berhasil dihentikan']);
        }
        return response()->json(['message' => 'Gagal menghentikan stream'], 500);
    }

    public function toggleAutostart(Stream $stream)
    {
        $stream->update(['autostart' => !$stream->autostart]);
        return response()->json([
            'message' => 'Autostart berhasil diperbarui',
            'autostart' => $stream->autostart
        ]);
    }

    public function fix(Stream $stream)
    {
        if ($this->streamService->fixStream($stream)) {
            return response()->json(['message' => 'Stream berhasil diperbaiki']);
        }
        return response()->json(['message' => 'Gagal memperbaiki stream'], 500);
    }

    public function destroy(Stream $stream)
    {
        $this->streamService->stopStream($stream);
        $stream->delete();
        return redirect()->route('streams.index')
            ->with('success', 'Stream berhasil dihapus.');
    }
}