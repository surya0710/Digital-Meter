@extends('layouts.app')

@section('title', 'Devices')
@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6 col-12">
                    <h2>Devices List</h2>
                </div>
                @if(auth()->user()->isAdmin())
                <div class="col-sm-6 col-12 d-flex justify-content-end">
                    <a class="btn btn-primary" href="{{ route('devices.createform') }}">+ Add New</a>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="container-fluid">
        @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-responsive">
                                <thead>
                                    <tr>
                                        <th>SNO.</th>
                                        <th>User Name</th>
                                        <th>Company Name</th>
                                        <th>Device ID</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($devices as $device)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $device->user->name }}</td>
                                        <td>{{ $device->user->company ?? '—' }}</td>
                                        <td>{{ $device->device_id }}</td>
                                        <td>{{ $device->device_type?->label() ?? 'Smart Panel' }}</td>
                                        <td>{{ $device->isActive() ? 'Active' : 'Inactive' }}</td>
                                        <td>
                                            <a href="{{ route('devices.view', $device->id) }}" class="btn btn-primary">View Device</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No devices found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{ $devices->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
