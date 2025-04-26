@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Stream</h5>
                    <a href="{{ route('streams.create') }}" class="btn btn-primary">Tambah Stream</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Deskripsi</th>
                                    <th>RTSP URL</th>
                                    <th>RTMP URL</th>
                                    <th>Resolusi</th>
                                    <th>Status</th>
                                    <th>Autostart</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($streams as $stream)
                                    <tr>
                                        <td>{{ $stream->name }}</td>
                                        <td>{{ Str::limit($stream->description, 30) }}</td>
                                        <td class="text-truncate" style="max-width: 200px;">
                                            {{ $stream->rtsp_url }}
                                        </td>
                                        <td class="text-truncate" style="max-width: 200px;">
                                            {{ $stream->rtmp_url }}
                                        </td>
                                        <td>{{ $stream->resolution }}</td>
                                        <td>
                                            <span class="badge bg-{{ $stream->status === 'running' ? 'success' : ($stream->status === 'error' ? 'danger' : 'secondary') }}">
                                                {{ ucfirst($stream->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input autostart-toggle"
                                                       type="checkbox"
                                                       {{ $stream->autostart ? 'checked' : '' }}
                                                       data-stream-id="{{ $stream->id }}">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @if($stream->status !== 'running')
                                                    <button class="btn btn-success btn-sm start-stream"
                                                            data-stream-id="{{ $stream->id }}">
                                                        <i class="bi bi-play-fill"></i>
                                                    </button>
                                                @else
                                                    <button class="btn btn-danger btn-sm stop-stream"
                                                            data-stream-id="{{ $stream->id }}">
                                                        <i class="bi bi-stop-fill"></i>
                                                    </button>
                                                @endif
                                                <button class="btn btn-warning btn-sm fix-stream"
                                                        data-stream-id="{{ $stream->id }}">
                                                    <i class="bi bi-wrench"></i>
                                                </button>
                                                <form action="{{ route('streams.destroy', $stream) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-danger btn-sm"
                                                            onclick="return confirm('Apakah Anda yakin ingin menghapus stream ini?')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Tidak ada stream yang tersedia</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Start Stream
        document.querySelectorAll('.start-stream').forEach(button => {
            button.addEventListener('click', function() {
                const streamId = this.dataset.streamId;
                fetch(`/streams/${streamId}/start`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menjalankan stream');
                });
            });
        });

        // Stop Stream
        document.querySelectorAll('.stop-stream').forEach(button => {
            button.addEventListener('click', function() {
                const streamId = this.dataset.streamId;
                fetch(`/streams/${streamId}/stop`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghentikan stream');
                });
            });
        });

        // Fix Stream
        document.querySelectorAll('.fix-stream').forEach(button => {
            button.addEventListener('click', function() {
                const streamId = this.dataset.streamId;
                fetch(`/streams/${streamId}/fix`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat memperbaiki stream');
                });
            });
        });

        // Toggle Autostart
        document.querySelectorAll('.autostart-toggle').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const streamId = this.dataset.streamId;
                fetch(`/streams/${streamId}/toggle-autostart`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data.message);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengubah autostart');
                    this.checked = !this.checked; // Revert the toggle
                });
            });
        });
    });
</script>
@endpush
@endsection