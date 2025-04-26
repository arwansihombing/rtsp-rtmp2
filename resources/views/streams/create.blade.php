@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Tambah Stream Baru</h5>
                        <a href="{{ route('streams.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('streams.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Stream</label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="rtsp_url" class="form-label">RTSP URL</label>
                            <input type="text" 
                                   class="form-control @error('rtsp_url') is-invalid @enderror" 
                                   id="rtsp_url" 
                                   name="rtsp_url" 
                                   value="{{ old('rtsp_url') }}" 
                                   placeholder="rtsp://username:password@ip:port/stream" 
                                   required>
                            @error('rtsp_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="rtmp_url" class="form-label">RTMP URL</label>
                            <input type="text" 
                                   class="form-control @error('rtmp_url') is-invalid @enderror" 
                                   id="rtmp_url" 
                                   name="rtmp_url" 
                                   value="{{ old('rtmp_url') }}" 
                                   placeholder="rtmp://server/app/stream-key" 
                                   required>
                            @error('rtmp_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="resolution" class="form-label">Resolusi</label>
                            <select class="form-select @error('resolution') is-invalid @enderror" 
                                    id="resolution" 
                                    name="resolution" 
                                    required>
                                <option value="">Pilih Resolusi</option>
                                @foreach($resolutions as $key => $value)
                                    <option value="{{ $key }}" {{ old('resolution') == $key ? 'selected' : '' }}>
                                        {{ $key }} ({{ $value }})
                                    </option>
                                @endforeach
                            </select>
                            @error('resolution')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="autostart" 
                                       name="autostart" 
                                       value="1" 
                                       {{ old('autostart') ? 'checked' : '' }}>
                                <label class="form-check-label" for="autostart">Autostart Stream</label>
                            </div>
                            @error('autostart')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Tambah Stream
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection