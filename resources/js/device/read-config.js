export function readDeviceConfig(elementId = 'device-dashboard-config') {
    const element = document.getElementById(elementId);

    if (!element) {
        throw new Error(`Device dashboard config element #${elementId} not found.`);
    }

    return JSON.parse(element.textContent);
}
