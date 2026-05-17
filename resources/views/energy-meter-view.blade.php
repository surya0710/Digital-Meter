@extends('layouts.app')

@section('title', 'Energy Meter')
@section('content')

<style>
    .meter-wrapper { padding: 1.5rem; }

    .page-sidebar, .logo-wrapper{ display: none!important; }

    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
    }
    .page-header h4 {
        font-weight: 700;
        font-size: 1rem;
        margin: 0;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .live-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.7rem;
        color: #4ade80;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        font-weight: 600;
    }
    .live-badge::before {
        content: '';
        width: 7px; height: 7px;
        background: #4ade80;
        border-radius: 50%;
        animation: blink 1.4s infinite;
    }
    @keyframes blink {
        0%,100% { opacity:1; } 50% { opacity:0.3; }
    }

    /* ── 5 top cards ── */
    .top-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }
    @media(max-width:992px){ .top-grid{ grid-template-columns:repeat(3,1fr); } }
    @media(max-width:576px){ .top-grid{ grid-template-columns:repeat(2,1fr); } }

    .mcard {
        background: #1e293b;
        border: 1px solid #1e3a55;
        border-top: 3px solid var(--c);
        border-radius: 12px;
        padding: 1rem 1.1rem 0.9rem;
        position: relative;
        overflow: hidden;
    }
    .mcard-label {
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #64748b;
        margin-bottom: 5px;
    }
    .mcard-value {
        font-size: 1.55rem;
        font-weight: 800;
        color: var(--c);
        font-variant-numeric: tabular-nums;
        line-height: 1;
    }
    .mcard-unit {
        font-size: 0.72rem;
        font-weight: 400;
        color: #475569;
        margin-left: 2px;
    }
    .mcard-icon {
        position: absolute;
        right: 0.9rem; bottom: 0.6rem;
        font-size: 1.4rem;
        color: var(--c);
        opacity: 0.12;
    }

    /* ── 3 phase panels ── */
    .phase-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
    }
    @media(max-width:768px){ .phase-grid{ grid-template-columns:1fr; } }

    .ppanel {
        background: #111827;
        border: 1px solid #1e3a55;
        border-radius: 12px;
        overflow: hidden;
    }
    .ppanel-head {
        background: #1e293b;
        border-bottom: 1px solid #1e3a55;
        padding: 0.6rem 1rem;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #94a3b8;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 7px;
    }
    .ppanel-head i { color: #38bdf8; font-size: 0.75rem; }

    .ptable { width: 100%; border-collapse: collapse; }
    .ptable tr { border-bottom: 1px solid #1a2640; }
    .ptable tr:last-child { border-bottom: none; }
    .ptable td { padding: 0.65rem 1rem; vertical-align: middle; }

    .plabel {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.82rem;
        color: #64748b;
        font-weight: 500;
    }
    .pdot {
        width: 9px; height: 9px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .pval {
        text-align: right;
        font-size: 1.1rem;
        font-weight: 700;
        color: #e2e8f0;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }
    .pval .u { font-size: 0.7rem; font-weight: 400; color: #475569; margin-left: 2px; }
</style>

<div class="meter-wrapper">

    <div class="page-header">
        <h4><i class="fa-solid fa-bolt me-2" style="color:#38bdf8"></i>3-Phase Energy Meter</h4>
        <span class="live-badge">Live</span>
    </div>

    {{-- ── Top 5 Cards ── --}}
    <div class="top-grid">
        <div class="mcard" style="--c:#38bdf8">
            <div class="mcard-label">Active Power</div>
            <div class="mcard-value"><span id="m-kw">0.00</span><span class="mcard-unit">kW</span></div>
            <i class="fa-solid fa-charging-station mcard-icon"></i>
        </div>
        <div class="mcard" style="--c:#4ade80">
            <div class="mcard-label">Energy</div>
            <div class="mcard-value"><span id="m-kwh">0.00</span><span class="mcard-unit">kWh</span></div>
            <i class="fa-solid fa-battery-half mcard-icon"></i>
        </div>
        <div class="mcard" style="--c:#a78bfa">
            <div class="mcard-label">Apparent Energy</div>
            <div class="mcard-value"><span id="m-kvah">0.00</span><span class="mcard-unit">kVAh</span></div>
            <i class="fa-solid fa-bolt mcard-icon"></i>
        </div>
        <div class="mcard" style="--c:#fb923c">
            <div class="mcard-label">Power Factor</div>
            <div class="mcard-value"><span id="m-pf">0.000</span></div>
            <i class="fa-solid fa-wave-square mcard-icon"></i>
        </div>
        <div class="mcard" style="--c:#34d399">
            <div class="mcard-label">Frequency</div>
            <div class="mcard-value"><span id="m-freq">0.00</span><span class="mcard-unit">Hz</span></div>
            <i class="fa-solid fa-signal mcard-icon"></i>
        </div>
    </div>

    {{-- ── 3 Phase Panels ── --}}
    <div class="phase-grid">

        <div class="ppanel">
            <div class="ppanel-head"><i class="fa-solid fa-bolt"></i> Phase Voltages</div>
            <table class="ptable">
                <tr>
                    <td><span class="plabel"><span class="pdot" style="background:#ef4444;box-shadow:0 0 5px #ef444466"></span>VR</span></td>
                    <td class="pval"><span id="m-vr">0.00</span><span class="u">V</span></td>
                </tr>
                <tr>
                    <td><span class="plabel"><span class="pdot" style="background:#eab308;box-shadow:0 0 5px #eab30866"></span>VY</span></td>
                    <td class="pval"><span id="m-vy">0.00</span><span class="u">V</span></td>
                </tr>
                <tr>
                    <td><span class="plabel"><span class="pdot" style="background:#3b82f6;box-shadow:0 0 5px #3b82f666"></span>VB</span></td>
                    <td class="pval"><span id="m-vb">0.00</span><span class="u">V</span></td>
                </tr>
            </table>
        </div>

        <div class="ppanel">
            <div class="ppanel-head"><i class="fa-solid fa-arrow-right-arrow-left"></i> Phase Currents</div>
            <table class="ptable">
                <tr>
                    <td><span class="plabel"><span class="pdot" style="background:#ef4444;box-shadow:0 0 5px #ef444466"></span>IR</span></td>
                    <td class="pval"><span id="m-ir">0.00</span><span class="u">A</span></td>
                </tr>
                <tr>
                    <td><span class="plabel"><span class="pdot" style="background:#eab308;box-shadow:0 0 5px #eab30866"></span>IY</span></td>
                    <td class="pval"><span id="m-iy">0.00</span><span class="u">A</span></td>
                </tr>
                <tr>
                    <td><span class="plabel"><span class="pdot" style="background:#3b82f6;box-shadow:0 0 5px #3b82f666"></span>IB</span></td>
                    <td class="pval"><span id="m-ib">0.00</span><span class="u">A</span></td>
                </tr>
            </table>
        </div>

        <div class="ppanel">
            <div class="ppanel-head"><i class="fa-solid fa-right-left"></i> Line Voltages (P–P)</div>
            <table class="ptable">
                <tr>
                    <td><span class="plabel"><span class="pdot" style="background:#f97316;box-shadow:0 0 5px #f9731666"></span>VRY</span></td>
                    <td class="pval"><span id="m-vry">0.00</span><span class="u">V</span></td>
                </tr>
                <tr>
                    <td><span class="plabel"><span class="pdot" style="background:#a855f7;box-shadow:0 0 5px #a855f766"></span>VYB</span></td>
                    <td class="pval"><span id="m-vyb">0.00</span><span class="u">V</span></td>
                </tr>
                <tr>
                    <td><span class="plabel"><span class="pdot" style="background:#06b6d4;box-shadow:0 0 5px #06b6d466"></span>VRB</span></td>
                    <td class="pval"><span id="m-vrb">0.00</span><span class="u">V</span></td>
                </tr>
            </table>
        </div>

    </div>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof window.Echo === 'undefined') { console.error('Echo not loaded'); return; }

    const set = (id, val, dec = 2) => {
        const el = document.getElementById(id);
        if (el && val != null) el.innerText = parseFloat(val).toFixed(dec);
    };

    window.Echo.channel('device-dashboard').listen('.mqtt.data', (e) => {
        // Find meter data at whatever nesting level Laravel puts it
        const d = [e, e.data, e?.data?.data].find(c => c && typeof c === 'object' && 'vr' in c);
        if (!d) return;

        set('m-kw',   d.kw,   2);
        set('m-kwh',  d.kwh,  2);
        set('m-kvah', d.kvah, 2);
        set('m-pf',   d.pf,   3);
        set('m-freq', d.freq, 2);
        set('m-vr',   d.vr,   2);
        set('m-vy',   d.vy,   2);
        set('m-vb',   d.vb,   2);
        set('m-ir',   d.ir,   2);
        set('m-iy',   d.iy,   2);
        set('m-ib',   d.ib,   2);
        set('m-vry',  d.vry,  2);
        set('m-vyb',  d.vyb,  2);
        set('m-vrb',  d.vrb,  2);
    });
});
</script>
@endpush