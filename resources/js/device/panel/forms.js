export function bindPanelForms(config) {
    const $ = window.$;

    $('#switchName').closest('form').on('submit', function (event) {
        event.preventDefault();

        $.ajax({
            url: config.routes.updateSwitchName,
            type: 'POST',
            data: new FormData(this),
            contentType: false,
            processData: false,
            success(response) {
                if (response.status === 'success') {
                    window.location.reload();
                }

                if (response.status === 'error') {
                    alert(response.message);
                }
            },
        });
    });

    $('#voltageProtection').on('submit', function (event) {
        event.preventDefault();

        $.ajax({
            url: config.routes.setVoltageProtection,
            type: 'POST',
            data: new FormData(this),
            contentType: false,
            processData: false,
            success(response) {
                if (response.status === 'error') {
                    alert(response.message);
                }
            },
        });
    });

    $(document).on('submit', '.current-protection-form', function (event) {
        event.preventDefault();

        const relay = $(this).data('relay');

        $.ajax({
            url: config.routes.setCurrentProtection,
            type: 'POST',
            data: new FormData(this),
            contentType: false,
            processData: false,
            success(response) {
                if (response.status === 'success') {
                    console.log(`Relay ${relay} updated`);
                } else {
                    console.log(response.message);
                }
            },
        });
    });

    document.addEventListener('shown.bs.tab', (event) => {
        if (event.target.id.startsWith('timer-tab-')) {
            const relayKey = event.target.id.replace('timer-tab-', '');
            window.showTimer(event, relayKey);
        }
    });
}
