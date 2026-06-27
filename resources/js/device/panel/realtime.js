import { renderTimerTable } from './timers.js';

const FAULT_LABELS = {
    1: 'Over Current',
    2: 'UnderVoltage (Global)',
    3: 'OverVoltage (Global)',
};

export function initPanelRealtime(config, commands) {
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof window.Echo === 'undefined') {
            console.error('Echo not loaded');
            return;
        }

        window.Echo
            .private('device-dashboard')
            .listen('.mqtt.data', (event) => {
                if (event.device_id !== config.deviceId) {
                    return;
                }

                if (!event.data) {
                    console.warn('Invalid MQTT payload', event);
                    return;
                }

                handlePayload(event, commands);
            });
    });
}

function handlePayload(event, commands) {
    const payload = event.data;
    const data = payload.data;

    if (payload.type === 'statusUpdate') {
        updateRelayStatus(data);
        updateTotals(data);
        setText('mode', data.mode);
    }

    if (payload.cmd === 'getRate') {
        const setRate = event.data.data.rate;
        const $select = window.$('select[name="refresh_rate"]');

        if ($select.val() === setRate.toString()) {
            return;
        }

        $select.val(setRate.toString()).trigger('change');
    }

    if (payload.cmd === 'getMemoryStatus') {
        const message = Object.entries(event.data.data)
            .map(([key, value]) => `${key} : ${value}`)
            .join('\n');

        window.$('#displayStorage').prop('readonly', true).val(message);
    }

    if (payload.cmd === 'setVoltageLimits') {
        window.$('#under-voltage').val(event.data.data.min);
        window.$('#over-voltage').val(event.data.data.max);
    }

    if (payload.cmd === 'setCurrentLimit') {
        window.$("input[name='max_current']").val(event.data.data.limit ?? 0);
    }

    if (payload.cmd === 'getVoltageCalibration') {
        window.$('#calibrated-voltage').val(event.data.data.factor);
    }

    if (payload.cmd === 'getTimers') {
        renderTimerTable(event.data.relay, event.data.data, commands.deleteTimer);
    }

    if (payload.type === 'energyUpdate') {
        updateEnergyReadings(event.data.data.energy);
    }
}

function updateRelayStatus(data) {
    for (let index = 0; index < 8; index++) {
        updateSwitchButton(index, data.relays[index]);
        setText(`current-value-${index}`, data.currents[index]);
        setText(`power-value-${index}`, data.power[index]);
        updateFuseButton(index, data.fuses[index]);
        updateFaultButton(index, data.faults[index]);
    }

    setText('voltage-value', `${data.voltage} V`);
}

function updateSwitchButton(index, relayState) {
    const switchBtn = document.getElementById(`switch-${index}`);

    if (!switchBtn) {
        return;
    }

    if (relayState === 1) {
        switchBtn.classList.remove('btn-danger');
        switchBtn.classList.add('btn-success');
        switchBtn.innerHTML = '<i class="fa-solid fa-toggle-on"></i> ON';
        switchBtn.dataset.status = '1';
    } else {
        switchBtn.classList.remove('btn-success');
        switchBtn.classList.add('btn-danger');
        switchBtn.innerHTML = '<i class="fa-solid fa-toggle-off"></i> OFF';
        switchBtn.dataset.status = '0';
    }
}

function updateFuseButton(index, fuseState) {
    const fuseBtn = document.getElementById(`fuse-${index}`);

    if (!fuseBtn) {
        return;
    }

    if (fuseState === 0) {
        fuseBtn.classList.remove('btn-success');
        fuseBtn.classList.add('btn-warning');
        fuseBtn.innerHTML = '<i class="fa-solid fa-bolt"></i> Fuse Blown';
    } else {
        fuseBtn.classList.remove('btn-warning');
        fuseBtn.classList.add('btn-success');
        fuseBtn.innerHTML = '<i class="fa-solid fa-bolt"></i> Fuse OK';
    }
}

function updateFaultButton(index, faultState) {
    const faultBtn = document.getElementById(`fault-${index}`);

    if (!faultBtn) {
        return;
    }

    if (faultState !== 0) {
        faultBtn.classList.remove('btn-success');
        faultBtn.classList.add('btn-warning');
        faultBtn.innerHTML = `<i class="fa-solid fa-bolt"></i> Current : ${FAULT_LABELS[faultState] ?? faultState}`;
    } else {
        faultBtn.classList.remove('btn-warning');
        faultBtn.classList.add('btn-success');
        faultBtn.innerHTML = '<i class="fa-solid fa-bolt"></i> Current : Good';
    }
}

function updateTotals(data) {
    const totalAmps = document.getElementById('total-amps');

    if (totalAmps) {
        const totalCurrent = data.currents.reduce((acc, current) => acc + current, 0);
        totalAmps.innerText = `${(Math.round(totalCurrent * 100) / 100).toFixed(2)} A`;
    }

    const totalPower = document.getElementById('total-power');

    if (totalPower) {
        const powerSum = data.power.reduce((acc, current) => acc + current, 0);
        totalPower.innerText = `${(Math.round(powerSum * 100) / 100).toFixed(2)} W`;
    }
}

function updateEnergyReadings(energyValues) {
    for (let index = 0; index < 8; index++) {
        const energyEl = document.getElementById(`energy-value-${index}`);

        if (energyEl) {
            energyEl.innerText = (Math.round(energyValues[index] * 100) / 100).toFixed(2);
        }
    }

    const totalEnergy = document.getElementById('total-energy');

    if (totalEnergy) {
        const sum = energyValues.reduce((acc, current) => acc + current, 0);
        totalEnergy.innerText = `${(Math.round(sum * 100) / 100).toFixed(2)} KWH`;
    }
}

function setText(elementId, value) {
    const element = document.getElementById(elementId);

    if (element && value != null) {
        element.innerText = value;
    }
}
