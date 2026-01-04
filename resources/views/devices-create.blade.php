@extends('layouts.app')

@section('title', 'Dashboard')
@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6 col-12">
                    <h2>Add New Device</h2>
                </div>
                <div class="col-sm-6 col-12 d-flex justify-content-end">
                    <a class="btn btn-primary" href="{{ route('users.list') }}">Back</a>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body basic-form">

                        {{-- SUCCESS / ERROR FLASH --}}
                        @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                        @elseif (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                        @endif

                        <form class="form-wizard" id="regForm" action="{{ route('devices.create') }}" method="POST">
                            @csrf
                            <div class="tab" style="display:block;">
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <label class="form-label">User <span class="text-danger">*</span></label>
                                        <select name="user_id" class="form-select">
                                            <option value="">Select</option>
                                            @foreach ($users as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-sm-6">
                                        <label class="form-label">Device ID <span class="text-danger">*</span></label>
                                        <input type="text" name="device_id" class="form-control" value="{{ old('device_id') }}">
                                    </div>

                                    <div class="col-sm-12">
                                        <label class="form-label">Status <span class="text-danger">*</span></label>
                                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                                            <option value="">Select</option>
                                            <option value="1" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
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
    @endsection