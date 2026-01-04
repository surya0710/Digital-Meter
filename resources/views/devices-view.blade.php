@extends('layouts.app')

@section('title', 'Dashboard')
@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6 col-12">
                    <h2>Device ({{ $device->device_id }})</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row g-3">
            <div class="col-md-4" id="device-1">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-space-between">
                            <h3>Switch 1</h3>
                            <button class="btn btn-success" onclick="showDetails(1)">Show Details</button>
                        </div>
                        <div class="flex py-3">
                            <button class="btn btn-danger"><i class="fa-solid fa-toggle-off"></i> OFF</button>
                            <button class="btn btn-danger"><i class="fa-solid fa-clock"></i> Timer OFF</button>
                        </div>
                        <div class="row py-3">
                            <button class="btn btn-warning rounded"><i class="fa-solid fa-bolt"></i> Fuse Blown</button>
                            <button class="btn btn-success mt-2"><i class="fa-solid fa-warning"></i> Current: Normal</button>
                        </div>
                        <div class="row px-3 py-2">
                            <label><i class="fa-solid fa-bolt"></i> Current (A)</label>
                            <h4>0.0</h4>
                        </div>
                        <hr>
                        <div class="row px-3 py-2">
                            <label><i class="fa-solid fa-battery"></i> Energy (KWH)</label>
                            <h4>0.0</h4>
                        </div>
                        <hr>
                        <div class="row px-3 py-2">
                            <label><i class="fa-solid fa-charging-station"></i> Power (W)</label>
                            <h4>0.0</h4>
                        </div>
                        <div class="hidden mt-3" id="device-1-details">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="voltage-tab" data-bs-toggle="tab" href="#voltage" role="tab" aria-controls="voltage" aria-selected="true">Voltage Settings</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="current-tab" data-bs-toggle="tab" href="#current" role="tab" aria-controls="current" aria-selected="false">Current Settings</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="energy-tab" data-bs-toggle="tab" href="#energy" role="tab" aria-controls="energy" aria-selected="false">Energy Management</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="timer-tab" data-bs-toggle="tab" href="#timer" role="tab" aria-controls="timer" aria-selected="false">Timer List</a>
                                </li>
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="voltage" role="tabpanel" aria-labelledby="voltage-tab">
                                    <div class="container p-3">
                                        <div class="row">
                                            <label><i class="fa-solid fa-gears"></i> Voltage Settings</label>
                                            <form class="form-group">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <input type="number" class="form-control" name="min-voltage" placeholder="Min Voltage">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="number" class="form-control" name="max-voltage" placeholder="Max Voltage">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <button type="submit" class="btn btn-primary">Save</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="current" role="tabpanel" aria-labelledby="current-tab">
                                    <div class="container p-3">
                                        <div class="row">
                                            <label><i class="fa-solid fa-bolt"></i> Current Settings <span class="bg-light circle"></span></label>
                                            <form class="form-group">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <input type="number" class="form-control" name="max-current" placeholder="Max Current">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <button type="submit" class="btn btn-primary">Save</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="energy" role="tabpanel" aria-labelledby="energy-tab">
                                    <div class="container p-3">
                                        <div class="row">
                                            <label><i class="fa-solid fa-battery"></i> Energy Management</label>
                                            <div class="col-md-4">
                                                <button type="submit" class="btn btn-primary"><i class="fas fa-redo"></i> Reset Energy</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="timer" role="tabpanel" aria-labelledby="timer-tab">
                                    <div class="container p-3">
                                        <div class="d-flex justify-content-space-between">
                                            <label><i class="fa-solid fa-list"></i> TImer List</label>
                                            <a href="#" class="btn btn-success">+ Add Timer</a>
                                        </div>
                                        <div class="row mt-2">
                                            <table class="table table-responsive">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">ID</th>
                                                        <th scope="col">Days</th>
                                                        <th scope="col">Start</th>
                                                        <th scope="col">Stop</th>
                                                        <th scope="col">Status</th>
                                                        <th scope="col">Enabled</th>
                                                        <th scope="col">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>T70951</td>
                                                        <td>
                                                            <div class="day-selector">
                                                                <label class="day">
                                                                    <input type="checkbox" name="days[]" value="Mon">
                                                                    <span>M</span>
                                                                </label>

                                                                <label class="day">
                                                                    <input type="checkbox" name="days[]" value="Tue">
                                                                    <span>T</span>
                                                                </label>

                                                                <label class="day">
                                                                    <input type="checkbox" name="days[]" value="Wed">
                                                                    <span>W</span>
                                                                </label>

                                                                <label class="day">
                                                                    <input type="checkbox" name="days[]" value="Thu">
                                                                    <span>Th</span>
                                                                </label>

                                                                <label class="day">
                                                                    <input type="checkbox" name="days[]" value="Fri">
                                                                    <span>F</span>
                                                                </label>

                                                                <label class="day">
                                                                    <input type="checkbox" name="days[]" value="Sat">
                                                                    <span>S</span>
                                                                </label>

                                                                <label class="day">
                                                                    <input type="checkbox" name="days[]" value="Sun">
                                                                    <span>Su</span>
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <input type="time" name="start_time" class="form-control" value="{{ old('start_time', '08:00:00') }}">
                                                        </td>
                                                        <td>
                                                            <input type="time" name="stop_time" class="form-control" value="{{ old('start_time', '17:00:00') }}">
                                                        </td>
                                                        <td class="text-center"><span class="bg-light circle"></span></td>
                                                        <td><button class="btn btn-danger">OFF</button></td>
                                                        <td>
                                                            <div class="d-flex gap-2">
                                                                <button class="btn btn-primary">Save</button>
                                                                <button class="btn btn-danger">Delete</button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4" id="device-2">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-space-between">
                            <h3>Switch 2</h3>
                            <button class="btn btn-success" onclick="showDetails(2)">Show Details</button>
                        </div>
                        <div class="flex py-3">
                            <button class="btn btn-danger"><i class="fa-solid fa-toggle-off"></i> OFF</button>
                            <button class="btn btn-danger"><i class="fa-solid fa-clock"></i> Timer OFF</button>
                        </div>
                        <div class="row py-3">
                            <button class="btn btn-warning rounded"><i class="fa-solid fa-bolt"></i> Fuse Blown</button>
                            <button class="btn btn-success mt-2"><i class="fa-solid fa-warning"></i> Current: Normal</button>
                        </div>
                        <div class="row px-3 py-2">
                            <label><i class="fa-solid fa-bolt"></i> Current (A)</label>
                            <h4>0.0</h4>
                        </div>
                        <hr>
                        <div class="row px-3 py-2">
                            <label><i class="fa-solid fa-battery"></i> Energy (KWH)</label>
                            <h4>0.0</h4>
                        </div>
                        <hr>
                        <div class="row px-3 py-2">
                            <label><i class="fa-solid fa-charging-station"></i> Power (W)</label>
                            <h4>0.0</h4>
                        </div>
                        <div class="hidden mt-3">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="voltage-tab" data-bs-toggle="tab" href="#voltage" role="tab" aria-controls="voltage" aria-selected="true">Voltage Settings</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="current-tab" data-bs-toggle="tab" href="#current" role="tab" aria-controls="current" aria-selected="false">Current Settings</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="energy-tab" data-bs-toggle="tab" href="#energy" role="tab" aria-controls="energy" aria-selected="false">Energy Management</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="timer-tab" data-bs-toggle="tab" href="#timer" role="tab" aria-controls="timer" aria-selected="false">Timer List</a>
                                </li>
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="voltage" role="tabpanel" aria-labelledby="voltage-tab">
                                    <div class="container p-3">
                                        <div class="row">
                                            <label><i class="fa-solid fa-gears"></i> Voltage Settings</label>
                                            <form class="form-group">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <input type="number" class="form-control" name="min-voltage" placeholder="Min Voltage">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="number" class="form-control" name="max-voltage" placeholder="Max Voltage">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <button type="submit" class="btn btn-primary">Save</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="current" role="tabpanel" aria-labelledby="current-tab">
                                    <div class="container p-3">
                                        <div class="row">
                                            <label><i class="fa-solid fa-bolt"></i> Current Settings <span class="bg-light circle"></span></label>
                                            <form class="form-group">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <input type="number" class="form-control" name="max-current" placeholder="Max Current">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <button type="submit" class="btn btn-primary">Save</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="energy" role="tabpanel" aria-labelledby="energy-tab">
                                    <div class="container p-3">
                                        <div class="row">
                                            <label><i class="fa-solid fa-battery"></i> Energy Management</label>
                                            <div class="col-md-4">
                                                <button type="submit" class="btn btn-primary"><i class="fas fa-redo"></i> Reset Energy</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="timer" role="tabpanel" aria-labelledby="timer-tab">
                                    <div class="container p-3">
                                        <div class="d-flex justify-content-space-between">
                                            <label><i class="fa-solid fa-list"></i> TImer List</label>
                                            <a href="#" class="btn btn-success">+ Add Timer</a>
                                        </div>
                                        <div class="row mt-2">
                                            <table class="table table-responsive">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">ID</th>
                                                        <th scope="col">Days</th>
                                                        <th scope="col">Start</th>
                                                        <th scope="col">Stop</th>
                                                        <th scope="col">Status</th>
                                                        <th scope="col">Enabled</th>
                                                        <th scope="col">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>T70951</td>
                                                        <td>
                                                            <div class="day-selector">
                                                                <label class="day">
                                                                    <input type="checkbox" name="days[]" value="Mon">
                                                                    <span>M</span>
                                                                </label>

                                                                <label class="day">
                                                                    <input type="checkbox" name="days[]" value="Tue">
                                                                    <span>T</span>
                                                                </label>

                                                                <label class="day">
                                                                    <input type="checkbox" name="days[]" value="Wed">
                                                                    <span>W</span>
                                                                </label>

                                                                <label class="day">
                                                                    <input type="checkbox" name="days[]" value="Thu">
                                                                    <span>Th</span>
                                                                </label>

                                                                <label class="day">
                                                                    <input type="checkbox" name="days[]" value="Fri">
                                                                    <span>F</span>
                                                                </label>

                                                                <label class="day">
                                                                    <input type="checkbox" name="days[]" value="Sat">
                                                                    <span>S</span>
                                                                </label>

                                                                <label class="day">
                                                                    <input type="checkbox" name="days[]" value="Sun">
                                                                    <span>Su</span>
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <input type="time" name="start_time" class="form-control" value="{{ old('start_time', '08:00:00') }}">
                                                        </td>
                                                        <td>
                                                            <input type="time" name="stop_time" class="form-control" value="{{ old('start_time', '17:00:00') }}">
                                                        </td>
                                                        <td class="text-center"><span class="bg-light circle"></span></td>
                                                        <td><button class="btn btn-danger">OFF</button></td>
                                                        <td>
                                                            <div class="d-flex gap-2">
                                                                <button class="btn btn-primary">Save</button>
                                                                <button class="btn btn-danger">Delete</button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4" id="device-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-space-between">
                            <h3>Switch 3</h3>
                            <button class="btn btn-success" onclick="showDetails(3)">Show Details</button>
                        </div>
                        <div class="flex py-3">
                            <button class="btn btn-danger"><i class="fa-solid fa-toggle-off"></i> OFF</button>
                            <button class="btn btn-danger"><i class="fa-solid fa-clock"></i> Timer OFF</button>
                        </div>
                        <div class="row py-3">
                            <button class="btn btn-warning rounded"><i class="fa-solid fa-bolt"></i> Fuse Blown</button>
                            <button class="btn btn-success mt-2"><i class="fa-solid fa-warning"></i> Current: Normal</button>
                        </div>
                        <div class="row px-3 py-2">
                            <label><i class="fa-solid fa-bolt"></i> Current (A)</label>
                            <h4>0.0</h4>
                        </div>
                        <hr>
                        <div class="row px-3 py-2">
                            <label><i class="fa-solid fa-battery"></i> Energy (KWH)</label>
                            <h4>0.0</h4>
                        </div>
                        <hr>
                        <div class="row px-3 py-2">
                            <label><i class="fa-solid fa-charging-station"></i> Power (W)</label>
                            <h4>0.0</h4>
                        </div>
                        <div class="hidden mt-3">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="voltage-tab" data-bs-toggle="tab" href="#voltage" role="tab" aria-controls="voltage" aria-selected="true">Voltage Settings</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="current-tab" data-bs-toggle="tab" href="#current" role="tab" aria-controls="current" aria-selected="false">Current Settings</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="energy-tab" data-bs-toggle="tab" href="#energy" role="tab" aria-controls="energy" aria-selected="false">Energy Management</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="timer-tab" data-bs-toggle="tab" href="#timer" role="tab" aria-controls="timer" aria-selected="false">Timer List</a>
                                </li>
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="voltage" role="tabpanel" aria-labelledby="voltage-tab">
                                    <div class="container p-3">
                                        <div class="row">
                                            <label><i class="fa-solid fa-gears"></i> Voltage Settings</label>
                                            <form class="form-group">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <input type="number" class="form-control" name="min-voltage" placeholder="Min Voltage">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="number" class="form-control" name="max-voltage" placeholder="Max Voltage">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <button type="submit" class="btn btn-primary">Save</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="current" role="tabpanel" aria-labelledby="current-tab">
                                    <div class="container p-3">
                                        <div class="row">
                                            <label><i class="fa-solid fa-bolt"></i> Current Settings <span class="bg-light circle"></span></label>
                                            <form class="form-group">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <input type="number" class="form-control" name="max-current" placeholder="Max Current">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <button type="submit" class="btn btn-primary">Save</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="energy" role="tabpanel" aria-labelledby="energy-tab">
                                    <div class="container p-3">
                                        <div class="row">
                                            <label><i class="fa-solid fa-battery"></i> Energy Management</label>
                                            <div class="col-md-4">
                                                <button type="submit" class="btn btn-primary"><i class="fas fa-redo"></i> Reset Energy</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="timer" role="tabpanel" aria-labelledby="timer-tab">
                                    <div class="container p-3">
                                        <div class="d-flex justify-content-space-between">
                                            <label><i class="fa-solid fa-list"></i> TImer List</label>
                                            <a href="#" class="btn btn-success">+ Add Timer</a>
                                        </div>
                                        <div class="row mt-2">
                                            <table class="table table-responsive">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">ID</th>
                                                        <th scope="col">Days</th>
                                                        <th scope="col">Start</th>
                                                        <th scope="col">Stop</th>
                                                        <th scope="col">Status</th>
                                                        <th scope="col">Enabled</th>
                                                        <th scope="col">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>T70951</td>
                                                        <td>
                                                            <div class="day-selector">
                                                                <label class="day">
                                                                    <input type="checkbox" name="days[]" value="Mon">
                                                                    <span>M</span>
                                                                </label>

                                                                <label class="day">
                                                                    <input type="checkbox" name="days[]" value="Tue">
                                                                    <span>T</span>
                                                                </label>

                                                                <label class="day">
                                                                    <input type="checkbox" name="days[]" value="Wed">
                                                                    <span>W</span>
                                                                </label>

                                                                <label class="day">
                                                                    <input type="checkbox" name="days[]" value="Thu">
                                                                    <span>Th</span>
                                                                </label>

                                                                <label class="day">
                                                                    <input type="checkbox" name="days[]" value="Fri">
                                                                    <span>F</span>
                                                                </label>

                                                                <label class="day">
                                                                    <input type="checkbox" name="days[]" value="Sat">
                                                                    <span>S</span>
                                                                </label>

                                                                <label class="day">
                                                                    <input type="checkbox" name="days[]" value="Sun">
                                                                    <span>Su</span>
                                                                </label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <input type="time" name="start_time" class="form-control" value="{{ old('start_time', '08:00:00') }}">
                                                        </td>
                                                        <td>
                                                            <input type="time" name="stop_time" class="form-control" value="{{ old('start_time', '17:00:00') }}">
                                                        </td>
                                                        <td class="text-center"><span class="bg-light circle"></span></td>
                                                        <td><button class="btn btn-danger">OFF</button></td>
                                                        <td>
                                                            <div class="d-flex gap-2">
                                                                <button class="btn btn-primary">Save</button>
                                                                <button class="btn btn-danger">Delete</button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endsection
        @push('scripts')
        <script>
        let activeDevice = null;

        function showDetails(deviceID) {

            const currentDevice  = $('#device-' + deviceID);
            const currentDetails = $('#device-' + deviceID + '-details');
            const currentButton  = currentDevice.find('button.btn-success');

            // 🔁 If clicking same device → collapse it
            if (activeDevice === deviceID) {

                currentDevice
                    .removeClass('col-12')
                    .addClass('col-md-4');

                currentDetails.addClass('hidden');
                currentButton.text('Show Details');

                activeDevice = null;
                return;
            }

            // ⬇ STEP 1: Collapse ALL devices first (CRITICAL FIX)
            $('[id^="device-"]').each(function () {
                if (!this.id.includes('details')) {
                    const devId = this.id.replace('device-', '');
                    const btn   = $(this).find('button.btn-success');

                    $(this)
                        .removeClass('col-12')
                        .addClass('col-md-4');

                    $('#device-' + devId + '-details').addClass('hidden');
                    btn.text('Show Details');
                }
            });

            // ⬆ STEP 2: Expand selected device
            currentDevice
                .removeClass('col-md-4')
                .addClass('col-12');

            currentDetails.removeClass('hidden');
            currentButton.text('Hide Details');

            activeDevice = deviceID;

            // Optional smooth scroll
            $('html, body').animate({
                scrollTop: currentDevice.offset().top - 20
            }, 400);
        }
        </script>
        @endpush