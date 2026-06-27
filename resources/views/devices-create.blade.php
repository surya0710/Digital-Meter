@extends('layouts.app')

@section('title', 'Add Device')
@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6 col-12">
                    <h2>Add New Device</h2>
                </div>
                <div class="col-sm-6 col-12 d-flex justify-content-end">
                    <a class="btn btn-primary" href="{{ route('devices.list') }}">Back</a>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body basic-form">

                        @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                        @elseif (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <form action="{{ route('devices.create') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="form-label">User <span class="text-danger">*</span></label>
                                    <select name="user_id" class="form-select @error('user_id') is-invalid @enderror">
                                        <option value="">Select</option>
                                        @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-sm-6">
                                    <label class="form-label">Device ID <span class="text-danger">*</span></label>
                                    <input type="text" name="device_id" class="form-control @error('device_id') is-invalid @enderror" value="{{ old('device_id') }}">
                                    @error('device_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-sm-6">
                                    <label class="form-label">Device Name</label>
                                    <input type="text" name="device_name" class="form-control @error('device_name') is-invalid @enderror" value="{{ old('device_name') }}">
                                    @error('device_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-sm-6">
                                    <label class="form-label">Device Type</label>
                                    <select name="device_type" class="form-select @error('device_type') is-invalid @enderror">
                                        <option value="panel" {{ old('device_type', 'panel') === 'panel' ? 'selected' : '' }}>Smart Panel</option>
                                        <option value="energy_meter" {{ old('device_type') === 'energy_meter' ? 'selected' : '' }}>Energy Meter</option>
                                    </select>
                                    @error('device_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-sm-6">
                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-select @error('status') is-invalid @enderror">
                                        <option value="">Select</option>
                                        <option value="1" {{ old('status') === '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status') === '0' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="text-start pt-3">
                                <button class="btn btn-primary" type="submit">Add Device</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
