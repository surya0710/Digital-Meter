console.log('🟡 DEVICE DASHBOARD SCRIPT LOADED');

document.addEventListener('DOMContentLoaded', () => {
    console.log('🟢 DOM READY');

    if (!window.Echo) {
        console.error('❌ Echo not loaded');
        return;
    }

    window.Echo
        .channel('device-dashboard')
        .listen('.mqtt.data', (e) => {
            console.log('🔥 MQTT EVENT RECEIVED', e);

            const payload = e.data;

            console.log(payload);

            if (payload.type === 'statusUpdate') {
                const d = payload.data;

                // your relay / current / power logic here
            }

            if (payload.type === 'energyUpdate') {
                const d = payload.data;
                // energy logic
            }

            if (payload.cmd === 'getTimers') {
                const timers = payload.data;
                // timer logic
            }
        });
});
