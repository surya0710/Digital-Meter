@extends('layouts.app')

@section('title', 'Dashboard')
@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6 col-12">
                    <h2>Devices List</h2>
                </div>
                <div class="col-sm-6 col-12 d-flex justify-content-end">
                    <a class="btn btn-primary" href="{{ route('devices.createform') }}">+ Add New</a>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
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
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($devices as $device)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $device->user->name }}</td>
                                        <td>{{  $device->user->company }}</td>
                                        <td>{{ $device->device_id }}</td>
                                        <td>{{ $device->is_active == 1 ? 'Active' : 'Inactive' }}</td>
                                        <td>
                                            <a href="{{ route('devices.view', $device->id) }}" class="btn btn-primary">View Device</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection