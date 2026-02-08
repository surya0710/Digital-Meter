@extends('layouts.app')

@section('title', 'View Device')
@section('content')
<div class="page-body">
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
                            <button class="btn btn-danger" onclick="showTimer({{ $i }})"><i class="fa-solid fa-clock"></i>
                                Show Timer</button>
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
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="timer-tab-{{ $i }}" data-bs-toggle="tab" href="#timer-{{ $i }}" role="tab" aria-controls="timer" aria-selected="false">Timer List</a>
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
                                <div class="tab-pane fade" id="timer-{{ $i }}" role="tabpanel" aria-labelledby="timer-tab" onclick="showTimer({{ $i }})">
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
    <script>
    $("#switchName").closest('form').on('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(this);
        const url = "{{ route('devices.updateSwitchName', $device->id) }}";

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if(response.status == 'success') {
                    window.location.reload();
                }

                if(response.status == 'error') {
                    alert(response.message);
                }
            }
        })
    });

    $("#voltageProtection").on("submit", function(){
        event.preventDefault();
        const formData = new FormData(this);
        const url = "{{ route('devices.setVoltageProtection', $device->id) }}";

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if(response.status == 'success') {
                    // window.location.reload();
                }

                if(response.status == 'error') {
                    alert(response.message);
                }
            }
        })
    });
    
    $(document).on('submit', '.current-protection-form', function (e) {
        e.preventDefault(); // ⛔ stops page reload

        const form = this;
        const formData = new FormData(form);

        const deviceId = $(form).data('device-id');
        const relay = $(form).data('relay');

        $.ajax({
            url: "{{ route('devices.setCurrentProtection', $device->device_id) }}",
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success(response) {
                if (response.status === 'success') {
                    console.log(`Relay ${relay} updated`);
                } else {
                    console.log(response.message);
                }
            }
        });
    });

    function getRefreshRate(){
        axios.post("{{ route('devices.getRefreshRate') }}", {
                deviceID: "{{ $device->device_id }}",
            })
            .then(response => {
                if (response.data.status === true) {
                    
                } else {
                    alert(response.data.error);
                }
            })
            .catch(error => {
                console.error(error);
                alert('Request failed');
            });
    }

    function getVoltageCalibration(){
        axios.post("{{ route('devices.getVoltageCalibration') }}", {
                deviceID: "{{ $device->device_id }}",
            })
            .then(response => {
                if (response.data.status === true) {
                    
                } else {
                    alert(response.data.error);
                }
            })
            .catch(error => {
                console.error(error);
                alert('Request failed');
            });
    }

    function setRefreshRate(){
        const refreshRate = $('select[name="refresh_rate"]').val();
        axios.post("{{ route('devices.setRefreshRate') }}", {
                refreshRate: refreshRate,
                deviceID: "{{ $device->device_id }}",
            })
            .then(response => {
                if (response.data.status === true) {
                    window.location.reload();
                } else {
                    alert(response.data.error);
                }
            })
            .catch(error => {
                console.error(error);
                alert('Request failed');
            });
    }

    function setCalibratedVoltage(){
        const voltage = $('#calibrated-voltage').val();
        axios.post("{{ route('devices.setCalibratedVoltage') }}", {
                voltage: voltage,
                deviceID: "{{ $device->device_id }}",
            })
            .then(response => {
                if (response.data.status === true) {
                    
                } else {
                    alert(response.data.error);
                }
            })
            .catch(error => {
                console.error(error);
                alert('Request failed');
            });
    }

    function setCalibratedCurrent(index){
        const current = $('#calibrated-current-' + index).val();
        axios.post("{{ route('devices.setCalibratedCurrent') }}", {
                current: current,
                index: index,
                deviceID: "{{ $device->device_id }}",
            })
            .then(response => {
                if (response.data.status === true) {
                    
                } else {
                    alert(response.data.error);
                }
            })
            .catch(error => {
                console.error(error);
                alert('Request failed');
            });
    }

    function fetchMemory(){
        axios.post("{{ route('devices.fetchMemory') }}", {
                deviceID: "{{ $device->device_id }}",
            })
            .then(response => {
                if (response.data.status === true) {
                    
                } else {
                    alert(response.data.error);
                }
            })
            .catch(error => {
                console.error(error);
                alert('Request failed');
            });
    }
    let activeDevice = null;

    function showDetails(deviceID) {

        axios.post("{{ route('devices.getCurrentLimit') }}", {
                deviceID: "{{ $device->device_id }}",
                relayID : deviceID
            })
            .then(response => {
                if (response.data.status === true) {
                    $('#current-limit-' + deviceID).text(response.data.limit);
                } else {
                    alert(response.data.error);
                }
            })
            .catch(error => {
                console.error(error);
                alert('Request failed');
            });

        const currentDevice = $('#device-' + deviceID);
        const currentDetails = $('#device-' + deviceID + '-details');
        const currentButton = $("#details-" + deviceID);

        if (activeDevice === deviceID) {
            currentDevice.removeClass('col-12').addClass('col-md-4');
            currentDetails.addClass('hidden');
            currentButton.text('Show Details');
            activeDevice = null;
            return;
        }

        $('[id^="device-"]').each(function() {
            if (!this.id.includes('details')) {
                const id = this.id.replace('device-', '');
                $(this).removeClass('col-12').addClass('col-md-4');
                $('#device-' + id + '-details').addClass('hidden');
                $("#details-" + id).text('Show Details');
            }
        });

        currentDevice.removeClass('col-md-4').addClass('col-12');
        currentDetails.removeClass('hidden');
        currentButton.text('Hide Details');
        activeDevice = deviceID;

        $('html, body').animate({
            scrollTop: currentDevice.offset().top - 20
        }, 400);
    }

    function showTimer(deviceID) {

        axios.post("{{ route('devices.fetchTimer') }}", {
                relayID: deviceID,
                deviceID: "{{ $device->device_id }}",
            })
            .then(response => {
                if (response.data.status === true) {
                    // First expand the device card
                    showDetails(deviceID);

                    // Deactivate all tabs for THIS device
                    $(`#current-tab-${deviceID}`).removeClass('active');
                    $(`#current-${deviceID}`).removeClass('show active');

                    // Activate TIMER tab
                    $(`#timer-tab-${deviceID}`).addClass('active');
                    $(`#timer-${deviceID}`).addClass('show active');
                } else {
                    alert(response.data.error);
                }
            })
            .catch(error => {
                console.error(error);
                alert('Request failed');
            });
    }

    function switchOn(button, relayID) {

        const status = button.getAttribute('data-status');

        axios.post("{{ route('devices.switch') }}", {
                relayID: relayID,
                deviceID: "{{ $device->device_id }}",
                status: status
            })
            .then(response => {
                if (response.data.status === true) {
                    
                } else {
                    alert(response.data.error);
                }
            })
            .catch(error => {
                console.error(error);
                alert('Request failed');
            });
    }

    function renderDays(mask) {
        const days = [
            { label: 'M',  bit: 1 },
            { label: 'T',  bit: 2 },
            { label: 'W',  bit: 4 },
            { label: 'Th', bit: 8 },
            { label: 'F',  bit: 16 },
            { label: 'S',  bit: 32 },
            { label: 'Su', bit: 64 },
        ];

        return days.map(d => `
            <label class="day" onclick="event.stopPropagation()">
                <input type="checkbox"
                    data-bit="${d.bit}"
                    ${mask & d.bit ? 'checked' : ''}
                    onclick="event.stopPropagation()">
                <span>${d.label}</span>
            </label>
        `).join('');
    }


    function deleteTimer(timerID, relayID) {
        axios.post("{{ route('devices.deleteTimer') }}", {
                timerID: timerID,
                relayID: relayID,
                deviceID: "{{ $device->device_id }}",
            })
            .then(response => {
                if (response.data.status === true) {
                    
                } else {
                    alert(response.data.error);
                }
            })
            .catch(error => {
                console.error(error);
                alert('Request failed');
            });
    }

    function saveTimer(button, deviceId) {

        const row = button.closest('tr');
        if (!row) return;

        /* 🔌 RELAY */
        const relayId = parseInt(row.dataset.relay);

        /* 🗓️ DAYS → BITMASK */
        let daysMask = 0;
        row.querySelectorAll('.day input[type="checkbox"]').forEach(cb => {
            if (cb.checked) {
                daysMask += parseInt(cb.dataset.bit);
            }
        });

        if (daysMask === 0) {
            alert('Please select at least one day');
            return;
        }

        /* ⏰ TIME */
        const startTime = row.querySelector('input[name="start_time"]').value;
        const endTime = row.querySelector('input[name="stop_time"]').value;

        if (!startTime || !endTime) {
            alert('Please select start and end time');
            return;
        }

        /* 🔘 ENABLED */
        const enabledBtn = row.querySelector('button[data-enabled]');
        const enabled = enabledBtn?.dataset.enabled === 'true';

        /* 🚀 SEND */
        axios.post('{{ route("devices.saveTimer") }}', {
                deviceID: deviceId,
                relayID: relayId,
                days: daysMask,
                start_time: startTime,
                end_time: endTime,
                enabled: enabled
            })
            .then(res => {
                if (res.data.status === true) {
                    
                } else {
                    alert(res.data.error || 'Save failed');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Request failed');
            });
    }


    function toggleTimerEnabled(event, button) {
        
        event.preventDefault();
        event.stopPropagation(); // 🔥 THIS is the key

        const current = button.dataset.enabled === 'true';
        const next = !current;

        // update dataset
        button.dataset.enabled = next;

        // update UI
        if (next) {
            button.classList.remove('btn-danger');
            button.classList.add('btn-success');
            button.innerText = 'ON';
        } else {
            button.classList.remove('btn-success');
            button.classList.add('btn-danger');
            button.innerText = 'OFF';
        }
    }

    function addTimerRow( event, relayKey) {
        event.preventDefault();
        event.stopPropagation(); // 🔥 THIS is the key

        const tbody = document.getElementById(`timer-body-${relayKey}`);
        
        if (!tbody) {
            console.warn('Timer table body not found');
            return;
        }

        const newRow = `
            <tr data-relay="${relayKey}">
                <td></td>

                <td>
                    <div class="day-selector">
                        ${renderDays(0)}
                    </div>
                </td>

                <td>
                    <input type="time" name="start_time" class="form-control" value="08:00" onpointerdown="event.stopPropagation()" onclick="event.stopPropagation()">
                </td>

                <td>
                    <input type="time" name="stop_time" class="form-control" value="17:00" onpointerdown="event.stopPropagation()" onclick="event.stopPropagation()">
                </td>

                <td class="text-center">
                    <span class="bg-light circle"></span>
                </td>

                <td>
                    <button class="btn btn-success" data-enabled="true" onpointerdown="event.stopPropagation()" onclick="toggleTimerEnabled(event, this)">ON</button>
                </td>

                <td>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary"
                                onclick="saveTimer(this, '{{ $device->device_id }}')">
                            Save
                        </button>
                        <button class="btn btn-danger"
                                onclick="this.closest('tr').remove()">
                            Delete
                        </button>
                    </div>
                </td>
            </tr>
        `;

        tbody.insertAdjacentHTML('beforeend', newRow);
    }

    function shutdownAll() {
        axios.post("{{ route('devices.shutdownAll') }}", {
                deviceID: "{{ $device->device_id }}",
            })
            .then(response => {
                if (response.data.status === true) {
                    
                } else {
                    alert(response.data.error);
                }
            })
            .catch(error => {
                console.error(error);
                alert('Request failed');
            });
    }
    </script>
    <script>
    const deviceId = "{{ $device->device_id }}";

    document.addEventListener('DOMContentLoaded', function() {

        if (typeof window.Echo === 'undefined') {
            console.error('❌ Echo not loaded');
            return;
        }

        window.Echo
            .channel('device-dashboard')
            .listen('.mqtt.data', (e) => {

                const payload = e;
                if (payload.device_id !== deviceId) return;

                if (!payload.data) {
                    console.warn('⚠️ Invalid payload', e);
                    return;
                }

                const d = payload.data.data;

                if (payload.data.type == 'statusUpdate') {
                    for (let i = 0; i < 8; i++) {

                        let deviceIndex = i;

                        /* 🔘 RELAY STATE */
                        const relayState = d.relays[i]; // 0 / 1
                        const switchBtn = document.getElementById(`switch-${deviceIndex}`);

                        if (switchBtn) {
                            if (relayState === 1) {
                                switchBtn.classList.remove('btn-danger');
                                switchBtn.classList.add('btn-success');
                                switchBtn.innerHTML = `<i class="fa-solid fa-toggle-on"></i> ON`;
                                switchBtn.dataset.status = '1';
                            } else {
                                switchBtn.classList.remove('btn-success');
                                switchBtn.classList.add('btn-danger');
                                switchBtn.innerHTML = `<i class="fa-solid fa-toggle-off"></i> OFF`;
                                switchBtn.dataset.status = '0';
                            }
                        }

                        /* ⚡ CURRENT */
                        const currentEl = document.getElementById(`current-value-${deviceIndex}`);
                        if (currentEl) {
                            currentEl.innerText = d.currents[i];
                        }

                        // /* 🔌 POWER */
                        const powerEL = document.getElementById(`power-value-${deviceIndex}`);
                        if (powerEL) {
                            powerEL.innerText = d.power[i];
                        }

                        /* 🔋 FUSE STATUS */
                        const fuseBtn = document.getElementById(`fuse-${deviceIndex}`);
                        if (fuseBtn) {
                            if (d.fuses[i] === 0) {
                                fuseBtn.classList.remove('btn-success');
                                fuseBtn.classList.add('btn-warning');
                                fuseBtn.innerHTML = `<i class="fa-solid fa-bolt"></i> Fuse Blown`;
                            } else {
                                fuseBtn.classList.remove('btn-warning');
                                fuseBtn.classList.add('btn-success');
                                fuseBtn.innerHTML = `<i class="fa-solid fa-bolt"></i> Fuse OK`;
                            }
                        }

                        const fault = document.getElementById(`fault-${deviceIndex}`);
                        if (fault) {
                            if (d.faults[i] !== 0) {
                                const faultsMap = {
                                    '1': 'Over Current',
                                    '2': 'UnderVoltage (Global)',
                                    '3': 'OverVoltage (Global)',
                                };

                                fault.classList.remove('btn-success');
                                fault.classList.add('btn-warning');
                                fault.innerHTML = `<i class="fa-solid fa-bolt"></i> Current : ${faultsMap[d.faults[i]]}`;
                            } else {
                                fault.classList.remove('btn-warning');
                                fault.classList.add('btn-success');
                                fault.innerHTML = `<i class="fa-solid fa-bolt"></i> Current : Good`;
                            }
                        }
                    }

                    const voltageEL = document.getElementById('voltage-value');
                    if (voltageEL) {
                        voltageEL.innerText = d.voltage + ' V';
                    }

                    const totalamps = document.getElementById('total-amps');
                    if (totalamps) {
                        const totalCurrent = d.currents.reduce((acc, curr) => acc + curr, 0);
                        totalamps.innerText = (Math.round(totalCurrent * 100) / 100).toFixed(2) + ' A';
                    }

                    const totalPowerEL = document.getElementById('total-power');
                    if (totalPowerEL) {
                        const totalPower = d.power.reduce((acc, curr) => acc + curr, 0);
                        totalPowerEL.innerText = (Math.round(totalPower * 100) / 100).toFixed(2) + ' W';
                    }

                    document.getElementById('mode').innerText = d.mode;
                }

                if (payload.data.cmd === 'getRate') {
                    const setRate = e.data.data.rate;

                    const $select = $('select[name="refresh_rate"]');

                    // If already selected, do nothing
                    if ($select.val() === setRate.toString()) return;

                    // Select the value
                    $select.val(setRate.toString()).trigger('change');
                }

                if(payload.data.cmd === 'getMemoryStatus') {
                    const memoryStatus = e.data.data;
                    const message = Object.entries(memoryStatus).map(([key, value]) => `${key} : ${value}`).join('\n');
                    $("#displayStorage").prop('readonly', true).val(message);
                }

                if(payload.data.cmd === 'setVoltageLimits') {
                    const voltageLimits = e.data.data;
                    $("#under-voltage").val(voltageLimits.min);
                    $("#over-voltage").val(voltageLimits.max);
                }

                if(payload.data.cmd === 'setCurrentLimit') {
                    const currentLimit = e.data.data.limit;
                    $("input[name='max_current']").val(currentLimit ?? 0);
                }

                if (payload.data.cmd === 'getVoltageCalibration') {
                    const factor = e.data.data.factor;
                    $("#calibrated-voltage").val(factor);
                }

                if (payload.data.cmd === 'getTimers') {

                    const msgId = e.data.relay;
                    const timers = e.data.data;

                    const timerListEl = document.getElementById(`timer-list-${msgId}`);

                    if (!timerListEl || !Array.isArray(timers)) return;

                    let rows = timers.map(timer => {

                        return `
                            <tr>
                                <td>${timer.id}</td>

                                <td>
                                    <div class="day-selector">
                                        ${renderDays(timer.days)}
                                    </div>
                                </td>

                                <td>
                                    <input type="time"
                                        class="form-control"
                                        value="${timer.onTime}">
                                </td>

                                <td>
                                    <input type="time"
                                        class="form-control"
                                        value="${timer.offTime}">
                                </td>

                                <td class="text-center">
                                    <span class="bg-light circle"></span>
                                </td>

                                <td>
                                    <button class="btn ${timer.enabled ? 'btn-success' : 'btn-danger'}">
                                        ${timer.enabled ? 'ON' : 'OFF'}
                                    </button>
                                </td>

                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-primary">Save</button>
                                        <button class="btn btn-danger" onclick="deleteTimer(${timer.id}, ${msgId})">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    }).join('');

                    timerListEl.innerHTML = `
                        <table class="table table-responsive timer-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Days</th>
                                    <th>Start</th>
                                    <th>Stop</th>
                                    <th>Status</th>
                                    <th>Enabled</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="timer-body-${msgId}">
                                ${rows}
                            </tbody>
                        </table>
                    `;
                }

                if (payload.data.type === 'energyUpdate') {
                    for (let i = 0; i < 8; i++) {
                        const energyEl = document.getElementById(`energy-value-${i}`);
                        if (energyEl) {
                            energyEl.innerText = (Math.round(e.data.data.energy[i] * 100) / 100).toFixed(2);
                        }
                    }

                    const totalEnergyEL = document.getElementById('total-energy');
                    if (totalEnergyEL) {
                        const totalEnergy = e.data.data.energy.reduce((acc, curr) => acc + curr, 0);
                        totalEnergyEL.innerText = (Math.round(totalEnergy * 100) / 100).toFixed(2) + ' KWH';
                    }
                }
            });
    });
    </script>

    @endpush