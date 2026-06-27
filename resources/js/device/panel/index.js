import { readDeviceConfig } from '../read-config.js';
import { createPanelCommands, createHttpClient } from './commands.js';
import { createTimerHelpers, toggleTimerEnabled } from './timers.js';
import { bindPanelForms } from './forms.js';
import { initPanelRealtime } from './realtime.js';

const config = readDeviceConfig();
const { post } = createHttpClient();
const timers = createTimerHelpers(config, { post });
const commands = createPanelCommands(config, timers);

bindPanelForms(config);
initPanelRealtime(config, commands);

Object.assign(window, {
    showDetails: commands.showDetails,
    showTimer: commands.showTimer,
    switchOn: commands.switchOn,
    getRefreshRate: commands.getRefreshRate,
    getVoltageCalibration: commands.getVoltageCalibration,
    setRefreshRate: commands.setRefreshRate,
    setCalibratedVoltage: commands.setCalibratedVoltage,
    setCalibratedCurrent: commands.setCalibratedCurrent,
    fetchMemory: commands.fetchMemory,
    shutdownAll: commands.shutdownAll,
    deleteTimer: commands.deleteTimer,
    saveTimer: commands.saveTimer,
    addTimerRow: commands.addTimerRow,
    toggleTimerEnabled,
});
