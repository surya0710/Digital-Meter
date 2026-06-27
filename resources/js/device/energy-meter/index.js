import { readDeviceConfig } from '../read-config.js';

const FIELD_MAP = [
    ['m-kw', 'kw', 2],
    ['m-kwh', 'kwh', 2],
    ['m-kvah', 'kvah', 2],
    ['m-pf', 'pf', 3],
    ['m-freq', 'freq', 2],
    ['m-vr', 'vr', 2],
    ['m-vy', 'vy', 2],
    ['m-vb', 'vb', 2],
    ['m-ir', 'ir', 2],
    ['m-iy', 'iy', 2],
    ['m-ib', 'ib', 2],
    ['m-vry', 'vry', 2],
    ['m-vyb', 'vyb', 2],
    ['m-vrb', 'vrb', 2],
];

function setValue(elementId, value, decimals = 2) {
    const element = document.getElementById(elementId);

    if (element && value != null) {
        element.innerText = parseFloat(value).toFixed(decimals);
    }
}

function extractMeterPayload(event) {
    return [event, event.data, event?.data?.data].find(
        (candidate) => candidate && typeof candidate === 'object' && 'vr' in candidate
    );
}

document.addEventListener('DOMContentLoaded', () => {
    if (typeof window.Echo === 'undefined') {
        console.error('Echo not loaded');
        return;
    }

    const config = readDeviceConfig();

    window.Echo.private('device-dashboard').listen('.mqtt.data', (event) => {
        if (event.device_id && event.device_id !== config.deviceId) {
            return;
        }

        const payload = extractMeterPayload(event);

        if (!payload) {
            return;
        }

        FIELD_MAP.forEach(([elementId, key, decimals]) => {
            setValue(elementId, payload[key], decimals);
        });
    });
});
