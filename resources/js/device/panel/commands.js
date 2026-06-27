export function createHttpClient() {
    const axios = window.axios;

    function post(url, payload) {
        return axios.post(url, payload)
            .then((response) => {
                if (response.data.status === true) {
                    return response.data;
                }

                alert(response.data.error ?? 'Request failed');
                return null;
            })
            .catch((error) => {
                console.error(error);
                alert('Request failed');
                return null;
            });
    }

    return { post };
}

export function createPanelCommands(config, timers) {
    const { post } = createHttpClient();
    let activeDevice = null;

    function expandDeviceDetails(deviceID) {
        const currentDevice = window.$(`#device-${deviceID}`);
        const currentDetails = window.$(`#device-${deviceID}-details`);
        const currentButton = window.$(`#details-${deviceID}`);

        if (activeDevice === deviceID) {
            currentDevice.removeClass('col-12').addClass('col-md-4');
            currentDetails.addClass('hidden');
            currentButton.text('Show Details');
            activeDevice = null;
            return;
        }

        window.$('[id^="device-"]').each(function () {
            if (!this.id.includes('details')) {
                const id = this.id.replace('device-', '');
                window.$(this).removeClass('col-12').addClass('col-md-4');
                window.$(`#device-${id}-details`).addClass('hidden');
                window.$(`#details-${id}`).text('Show Details');
            }
        });

        currentDevice.removeClass('col-md-4').addClass('col-12');
        currentDetails.removeClass('hidden');
        currentButton.text('Hide Details');
        activeDevice = deviceID;

        window.$('html, body').animate({
            scrollTop: currentDevice.offset().top - 20,
        }, 400);
    }

    function showDetails(deviceID) {
        post(config.routes.getCurrentLimit, {
            deviceID: config.deviceId,
            relayID: deviceID,
        }).then((response) => {
            if (response) {
                window.$(`#current-limit-${deviceID}`).text(response.limit);
            }
        });

        expandDeviceDetails(deviceID);
    }

    function showTimer(event, deviceID) {
        event.preventDefault();
        event.stopPropagation();

        post(config.routes.fetchTimer, {
            relayID: deviceID,
            deviceID: config.deviceId,
        }).then((response) => {
            if (!response) {
                return;
            }

            expandDeviceDetails(deviceID);

            window.$(`#current-tab-${deviceID}`).removeClass('active');
            window.$(`#current-${deviceID}`).removeClass('show active');
            window.$(`#timer-tab-${deviceID}`).addClass('active');
            window.$(`#timer-${deviceID}`).addClass('show active');
        });
    }

    function switchOn(button, relayID) {
        post(config.routes.switch, {
            relayID,
            deviceID: config.deviceId,
            status: button.getAttribute('data-status'),
        });
    }

    function getRefreshRate() {
        post(config.routes.getRefreshRate, { deviceID: config.deviceId });
    }

    function getVoltageCalibration() {
        post(config.routes.getVoltageCalibration, { deviceID: config.deviceId });
    }

    function setRefreshRate() {
        const refreshRate = window.$('select[name="refresh_rate"]').val();

        post(config.routes.setRefreshRate, {
            refreshRate,
            deviceID: config.deviceId,
        }).then((response) => {
            if (response) {
                window.location.reload();
            }
        });
    }

    function setCalibratedVoltage() {
        post(config.routes.setCalibratedVoltage, {
            voltage: window.$('#calibrated-voltage').val(),
            deviceID: config.deviceId,
        });
    }

    function setCalibratedCurrent(index) {
        post(config.routes.setCalibratedCurrent, {
            current: window.$(`#calibrated-current-${index}`).val(),
            index,
            deviceID: config.deviceId,
        });
    }

    function fetchMemory() {
        post(config.routes.fetchMemory, { deviceID: config.deviceId });
    }

    function shutdownAll() {
        post(config.routes.shutdownAll, { deviceID: config.deviceId });
    }

    return {
        showDetails,
        showTimer,
        switchOn,
        getRefreshRate,
        getVoltageCalibration,
        setRefreshRate,
        setCalibratedVoltage,
        setCalibratedCurrent,
        fetchMemory,
        shutdownAll,
        deleteTimer: timers.deleteTimer,
        saveTimer: timers.saveTimer,
        addTimerRow: timers.addTimerRow,
    };
}
