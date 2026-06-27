@extends('layouts.app')

@section('title', 'Energy Meter')
@section('content')
<div class="page-body">
    <div class="container-fluid">
        <div class="energy-meter-dashboard">
            <div class="meter-wrapper">

                <div class="meter-page-header">
                    <h4><i class="fa-solid fa-bolt me-2" style="color:#38bdf8"></i>3-Phase Energy Meter</h4>
                    <span class="live-badge">Live</span>
                </div>

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
        </div>
    </div>
</div>
@endsection

@push('styles')
    @vite(['resources/css/energy-meter.css'])
@endpush

@push('scripts')
    @include('partials.energy-meter-config')
    @vite(['resources/js/device/energy-meter/index.js'])
@endpush
