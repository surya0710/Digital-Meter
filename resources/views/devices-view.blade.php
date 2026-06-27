@extends('layouts.app')

@section('title', 'View Device')
@section('content')
<div class="page-body panel-dashboard">
    <div class="container-fluid">
        <div class="d-flex gap-1 pt-2" style="justify-content: end;">
            <button class="btn btn-primary" onclick="fetchMemory()" data-bs-toggle="modal" data-bs-target="#storageModal"><i class="fa-solid fa-floppy-disk"></i></button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="fas fa-cog"></i></button>
            <div class="btn btn-success"><span id="mode"></span></div>
            <button class="btn btn-danger" onclick="shutdownAll()" id="reset-voltage"><i class="fas fa-power-off"></i> Shutdown All</button>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-sm-6 col-12">
            <h2>Device</h2>
        </div>
        <div class="col-md-6">
            <div class="d-flex gap-3">
                <strong><label>Voltage: <span id="voltage-value">0.00 V</span></label></strong>
                <strong><label>Total Amps: <span id="total-amps">0.00 A</span></label></strong>
                <strong><label>Total Power: <span id="total-power">0.00 (W)</span></label></strong>
                <strong><label>Total Energy: <span id="total-energy">0.00 (KWH)</span></label></strong>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row g-3">
            @for($i = 0; $i <= 7; $i++) 
            @php
                $switchField = 'switch' . $i;
            @endphp
            <div class="col-md-4" id="device-{{ $i }}">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-space-between">
                            <h3><span id="switch-name-{{ $i }}"> {{ $device->switchNames->$switchField ?? 'Switch ' . ($i + 1) }}</span></h3>
                            <button class="btn btn-success" id="details-{{ $i }}" onclick="showDetails({{ $i }})">Show
                                Details</button>
                        </div>
                        <div class="flex py-3">
                            <button class="btn btn-danger" data-status="0" id="switch-{{ $i }}" onclick="switchOn(this, {{ $i }})"><i class="fa-solid fa-toggle-off"></i> OFF</button>
                            <button class="btn btn-danger" onclick="showTimer(event, {{ $i }})"><i class="fa-solid fa-clock"></i> Show Timer</button>
                        </div>
                        <div class="row py-3">
                            <button class="btn btn-success rounded" id="fuse-{{ $i }}"><i class="fa-solid fa-bolt"></i>
                                Fuse
                                OK</button>
                            <button class="btn btn-success mt-2" id="fault-{{ $i }}"> Current: Normal</button>
                        </div>
                        <div class="row px-3 py-2">
                            <label><i class="fa-solid fa-bolt"></i> Current (A)</label>
                            <h4 id="current-value-{{ $i }}">0.0</h4>
                        </div>
                        <hr>
                        <div class="row px-3 py-2">
                            <label><i class="fa-solid fa-battery"></i> Energy (KWH)</label>
                            <h4 id="energy-value-{{ $i }}">0.0</h4>
                        </div>
                        <hr>
                        <div class="row px-3 py-2">
                            <label><i class="fa-solid fa-charging-station"></i> Power (W)</label>
                            <h4 id="power-value-{{ $i }}">0.0</h4>
                        </div>
                        <hr>
                        <div class="row px-3 py-2">
                            <label><i class="fa-solid fa-charging-station"></i> Runtime (hh:mm:ss)</label>
                            <h4 id="runtime-value-{{ $i }}">00:00:00</h4>
                        </div>
                        <div class="hidden mt-3" id="device-{{ $i }}-details">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="current-tab-{{ $i }}" data-bs-toggle="tab" href="#current-{{ $i }}" role="tab" aria-controls="current" aria-selected="true">Current Settings</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="energy-tab-{{ $i }}" data-bs-toggle="tab" href="#energy-{{ $i }}" role="tab" aria-controls="energy" aria-selected="false">Energy Management</a>
                                </li>
                                <li class="nav-item" role="presentation" onpointerdown="event.stopPropagation()">
                                    <a class="nav-link" id="timer-tab-{{ $i }}" data-bs-toggle="tab" href="#timer-{{ $i }}" role="tab">Timer List </a>
                                </li>
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="current-{{ $i }}" role="tabpanel" aria-labelledby="current-tab">
                                    <div class="container p-3">
                                        <div class="row">
                                            <label><i class="fa-solid fa-bolt"></i> Current Settings <span class="bg-light circle"></span></label>
                                            <form class="form-group current-protection-form" id="currentProtection{{ $i }}" data-device-id="{{ $device->device_id }}" data-relay="{{ $i }}">
                                                @csrf

                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <input type="number" class="form-control" name="max_current" min="0.01" max="5" step="0.01" value="0.00" required>
                                                        <input type="hidden" name="relay" value="{{ $i }}">
                                                    </div>

                                                    <div class="col-md-4">
                                                        <button type="submit" class="btn btn-primary">Save</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="energy-{{ $i }}" role="tabpanel"
                                    aria-labelledby="energy-tab">
                                    <div class="container p-3">
                                        <div class="row">
                                            <label><i class="fa-solid fa-battery"></i> Energy Management</label>
                                            <div class="col-md-4">
                                                <button type="submit" class="btn btn-primary"><i class="fas fa-redo"></i> Reset Energy</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="timer-{{ $i }}" role="tabpanel" aria-labelledby="timer-tab" onclick="event.stopPropagation()">
                                    <div class="container p-3">
                                        <div class="d-flex justify-content-space-between">
                                            <label><i class="fa-solid fa-list"></i> TImer List</label>
                                            <button type="button" class="btn btn-success" onclick="addTimerRow(event,'{{ $i }}')">+ Add Timer </button>
                                        </div>
                                        <div class="row mt-2 " id="timer-list-{{ $i }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        @endfor
    </div>
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">

        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Device Settings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <ul class="nav nav-tabs" id="device-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="switch-name-tab" data-bs-toggle="tab" href="#switch-name" role="tab" aria-controls="switch-name" aria-selected="true">Switch Name</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="refresh-rate-tab" onclick="getRefreshRate()" data-bs-toggle="tab" href="#refresh-rate" role="tab" aria-controls="refresh-rate" aria-selected="false">Refresh Rate</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="voltage-calibration-tab" onclick="getVoltageCalibration()" data-bs-toggle="tab" href="#voltage-calibration" role="tab" aria-controls="voltage-calibration" aria-selected="false"><i class="fas fa-cog"></i> Voltage</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="current-calibration-tab" data-bs-toggle="tab" href="#current-calibration" role="tab" aria-controls="current-calibration" aria-selected="false">Current C</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="device-tabContent">
                        <div class="tab-pane fade show active" id="switch-name" role="tabpanel" aria-labelledby="switch-name-tab">
                            <form action="" method="post" id="switchName">
                                @csrf
                                <div class="modal-body">
                                    @for ($i = 0; $i <= 7; $i++)
                                    @php $switchName = 'switch' . $i; @endphp
                                        <div class="col-sm-12 mb-2">
                                            <label class="form-label">Switch {{ $i }}</label>
                                            <input type="text" name="switch{{ $i }}" class="form-control" placeholder="Enter Switch Name" required value="{{ $device->switchNames->$switchName }}" />
                                        </div>
                                    @endfor
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="refresh-rate" role="tabpanel" aria-labelledby="refresh-rate-tab">
                            <div class="modal-body">
                                <div class="col-md-12">
                                    <label class="form-label">Refresh Rate</label>
                                    <select name="refresh_rate" class="form-control">
                                        <option value="5">5</option>
                                        <option value="10">10</option>
                                        <option value="15">15</option>
                                        <option value="20">20</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="setRefreshRate()" class="btn btn-primary">Set Refresh Rate</button>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="voltage-calibration" role="tabpanel" aria-labelledby="voltage-calibration-tab">
                            <div class="modal-body">
                                <div class="col-md-12 mt-2">
                                    <label class="form-label">Calibrate Voltage</label>
                                    <div class="d-flex gap-2">
                                        <input type="number" class="form-control" id="calibrated-voltage" placeholder="Calibrate Voltage">
                                        <button type="button" onclick="setCalibratedVoltage()" class="btn btn-primary">Calibrate</button>
                                    </div>
                                </div>
                                <form action="" method="post" id="voltageProtection">
                                    <div class="row">
                                        <div class="col-md-6 mt-2">
                                            <label class="form-label">Under Voltage</label>
                                            <div class="d-flex gap-2">
                                                <input type="number" name="underVoltage" class="form-control" id="under-voltage" placeholder="Under Voltage">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mt-2">
                                            <label class="form-label">Over Voltage</label>
                                            <div class="d-flex gap-2">
                                                <input type="number" name="overVoltage" class="form-control" id="over-voltage" placeholder="Over Voltage">
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary mt-2">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="current-calibration" role="tabpanel" aria-labelledby="current-calibration-tab">
                            <div class="modal-body">
                                @for ($i = 0; $i <= 7; $i++)
                                <div class="d-flex gap-2 mt-2">
                                    <input type="number" class="form-control" id="calibrated-current-{{ $i }}" placeholder="Calibrate Current">
                                    <button type="button" onclick="setCalibratedCurrent({{ $i }})" class="btn btn-primary">Calibrate</button>
                                </div>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="storageModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Storage</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <label class="form-label">Available Storage</label>
                        <textarea id="displayStorage" class="form-control" readonly rows="10"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('partials.device-panel-config')
    @vite(['resources/js/device/panel/index.js'])
@endpush