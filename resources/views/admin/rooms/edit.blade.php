@extends('layouts.admin')

@section('title', 'Edit Ruangan | RAPI Sekolah')
@section('page_title', 'Kelola Ruangan')
@section('page_subtitle', 'Edit data ruangan sekolah')

@section('content')

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Edit Ruangan</h5>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('rooms.update', $room->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Ruangan</label>
                        <input type="text"
                               name="room_name"
                               class="form-control @error('room_name') is-invalid @enderror"
                               value="{{ old('room_name', $room->room_name) }}"
                               required>
                        @error('room_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kapasitas</label>
                        <input type="number"
                               name="capacity"
                               class="form-control @error('capacity') is-invalid @enderror"
                               value="{{ old('capacity', $room->capacity) }}"
                               min="1"
                               required>
                        @error('capacity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Lokasi</label>
                        <input type="text"
                               name="location"
                               class="form-control @error('location') is-invalid @enderror"
                               value="{{ old('location', $room->location) }}"
                               required>
                        @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Penanggung Jawab (PIC)</label>

                        <select name="pic_id"
                                id="pic_id"
                                class="form-select @error('pic_id') is-invalid @enderror">

                            <option value="">-- Pilih PIC Ruangan --</option>

                            @foreach($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ old('pic_id', $room->pic_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->fullname }}
                                </option>
                            @endforeach
                        </select>

                        @error('pic_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <small class="text-muted">
                            Pilih PIC yang bertanggung jawab atas ruangan ini.
                        </small>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('rooms.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
