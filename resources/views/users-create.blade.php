@extends('layouts.app')

@section('title', 'Add User')
@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6 col-12">
                    <h2>Add New User</h2>
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

                        <form class="form-wizard" id="regForm" action="{{ route('users.create') }}" method="POST">
                            @csrf

                            <div class="tab" style="display:block;">
                                <div class="row g-3">

                                    {{-- NAME --}}
                                    <div class="col-sm-6">
                                        <label class="form-label">Name <span class="text-danger">*</span></label>
                                        <input
                                            type="text"
                                            name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name') }}"
                                            placeholder="Enter your name">
                                        @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- EMAIL --}}
                                    <div class="col-sm-6">
                                        <label class="form-label">Email <span class="text-danger">*</span></label>
                                        <input
                                            type="email"
                                            name="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            value="{{ old('email') }}"
                                            placeholder="admiro@gmail.com">
                                        @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- PHONE --}}
                                    <div class="col-sm-6">
                                        <label class="form-label">Phone <span class="text-danger">*</span></label>
                                        <input
                                            type="text"
                                            name="phone"
                                            class="form-control @error('phone') is-invalid @enderror"
                                            value="{{ old('phone') }}"
                                            placeholder="9999999999">
                                        @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- ROLE --}}
                                    <div class="col-sm-6">
                                        <label class="form-label">Role <span class="text-danger">*</span></label>
                                        <select
                                            name="user_role"
                                            class="form-select @error('user_role') is-invalid @enderror">
                                            <option value="">Select</option>
                                            <option value="admin" {{ old('user_role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                            <option value="guest" {{ old('user_role') === 'guest' ? 'selected' : '' }}>Guest</option>
                                            <option value="user" {{ old('user_role') === 'user' ? 'selected' : '' }}>User</option>
                                        </select>
                                        @error('user_role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-sm-6">
                                        <label class="form-label">Status <span class="text-danger">*</span></label>
                                        <select
                                            name="status"
                                            class="form-select @error('status') is-invalid @enderror">
                                            <option value="">Select</option>
                                            <option value="1" {{ old('status') === '1' ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ old('status') === '0' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-sm-6">
                                        <label class="form-label">Company</label>
                                        <input type="text" name="company" class="form-control" value="{{ old('company') }}">
                                        @error('company')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    {{-- PASSWORD --}}
                                    <div class="col-sm-6">
                                        <label class="form-label">Password <span class="text-danger">*</span></label>
                                        <input
                                            type="password"
                                            name="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            placeholder="Enter password">
                                        @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- CONFIRM PASSWORD --}}
                                    <div class="col-sm-6">
                                        <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                        <input
                                            type="password"
                                            name="password_confirmation"
                                            class="form-control @error('password_confirmation') is-invalid @enderror"
                                            placeholder="Confirm password">
                                        @error('password_confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                </div>
                            </div>

                            {{-- SUBMIT --}}
                            <div class="text-start pt-3">
                                <button class="btn btn-primary" type="submit">Add User</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection