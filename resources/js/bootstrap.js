import axios from 'axios';
import Echo from 'laravel-echo';

window.axios = axios;

/**
 * Axios default headers
 */
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Laravel Reverb + Echo
 */
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT,
    wssPort: import.meta.env.VITE_REVERB_PORT,
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
});

/**
 * 🛡️ SAFETY PATCH
 * Prevent "socketId undefined" crash
 */
const originalSocketId = window.Echo.socketId;
window.Echo.socketId = function () {
    if (!this.connector || !this.connector.socket) {
        return null;
    }
    return originalSocketId.call(this);
};
