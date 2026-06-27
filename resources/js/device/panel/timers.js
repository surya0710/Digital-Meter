const DAY_BITS = [
    { label: 'M', bit: 1 },
    { label: 'T', bit: 2 },
    { label: 'W', bit: 4 },
    { label: 'Th', bit: 8 },
    { label: 'F', bit: 16 },
    { label: 'S', bit: 32 },
    { label: 'Su', bit: 64 },
];

export function renderDays(mask) {
    return DAY_BITS.map((day) => `
        <label class="day" onclick="event.stopPropagation()">
            <input type="checkbox"
                data-bit="${day.bit}"
                ${mask & day.bit ? 'checked' : ''}
                onclick="event.stopPropagation()">
            <span>${day.label}</span>
        </label>
    `).join('');
}

export function toggleTimerEnabled(event, button) {
    event.preventDefault();
    event.stopPropagation();

    const next = button.dataset.enabled !== 'true';
    button.dataset.enabled = String(next);

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

export function createTimerHelpers(config, { post }) {
    function deleteTimer(timerID, relayID) {
        post(config.routes.deleteTimer, {
            timerID,
            relayID,
            deviceID: config.deviceId,
        });
    }

    function saveTimer(button) {
        const row = button.closest('tr');
        if (!row) {
            return;
        }

        const relayId = parseInt(row.dataset.relay, 10);
        let daysMask = 0;

        row.querySelectorAll('.day input[type="checkbox"]').forEach((checkbox) => {
            if (checkbox.checked) {
                daysMask += parseInt(checkbox.dataset.bit, 10);
            }
        });

        if (daysMask === 0) {
            alert('Please select at least one day');
            return;
        }

        const startTime = row.querySelector('input[name="start_time"]').value;
        const endTime = row.querySelector('input[name="stop_time"]').value;

        if (!startTime || !endTime) {
            alert('Please select start and end time');
            return;
        }

        const enabledBtn = row.querySelector('button[data-enabled]');
        const enabled = enabledBtn?.dataset.enabled === 'true';

        post(config.routes.saveTimer, {
            deviceID: config.deviceId,
            relayID: relayId,
            days: daysMask,
            start_time: startTime,
            end_time: endTime,
            enabled,
        });
    }

    function addTimerRow(event, relayKey) {
        event.preventDefault();
        event.stopPropagation();

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
                        <button class="btn btn-primary" onclick="saveTimer(this)">Save</button>
                        <button class="btn btn-danger" onclick="this.closest('tr').remove()">Delete</button>
                    </div>
                </td>
            </tr>
        `;

        tbody.insertAdjacentHTML('beforeend', newRow);
    }

    return {
        deleteTimer,
        saveTimer,
        addTimerRow,
    };
}

export function renderTimerTable(relayId, timers, deleteTimer) {
    const timerListEl = document.getElementById(`timer-list-${relayId}`);

    if (!timerListEl || !Array.isArray(timers)) {
        return;
    }

    const rows = timers.map((timer) => `
        <tr>
            <td>${timer.id}</td>
            <td>
                <div class="day-selector">
                    ${renderDays(timer.days)}
                </div>
            </td>
            <td>
                <input type="time" class="form-control" value="${timer.onTime}">
            </td>
            <td>
                <input type="time" class="form-control" value="${timer.offTime}">
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
                    <button class="btn btn-danger" onclick="deleteTimer(${timer.id}, ${relayId})">Delete</button>
                </div>
            </td>
        </tr>
    `).join('');

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
            <tbody id="timer-body-${relayId}">
                ${rows}
            </tbody>
        </table>
    `;
}
