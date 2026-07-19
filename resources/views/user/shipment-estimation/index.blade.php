@extends('layouts.dashboard')

@section('title', 'Shipment Route Estimation')

@section('content')

{{-- Page Header --}}
<div class="page-header mb-4">
    <div>
        <h1 class="page-header-title">
            <i class="bi bi-compass me-2" style="color:var(--primary-light);font-size:1.2rem;"></i>
            Shipment Route Estimation
        </h1>
        <p class="page-header-sub mb-0">Simulate international maritime shipping lanes, calculate times, risk profiles, and live weather conditions.</p>
    </div>
</div>

{{-- Top Row: Form Input & Stepper/Animation Controls --}}
<div class="row g-4 mb-4">
    {{-- Form Card --}}
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100 rounded-4 glass-card" style="background: var(--surface);">
            <div class="card-header border-bottom-0 pt-4 px-4 bg-transparent">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-sliders me-1"></i> Simulation Parameters</h5>
            </div>
            <div class="card-body px-4 pb-4">
                <form id="estimationForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Origin Country <span class="text-danger">*</span></label>
                        <select name="origin_country_id" id="originCountry" class="form-select select2-enable" required>
                            <option value="">Search Country...</option>
                            @foreach($countries as $c)
                                <option value="{{ $c['id'] }}" data-iso2="{{ strtolower($c['iso2']) }}">{{ $c['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Destination Country <span class="text-danger">*</span></label>
                        <select name="dest_country_id" id="destCountry" class="form-select select2-enable" required>
                            <option value="">Search Country...</option>
                            @foreach($countries as $c)
                                <option value="{{ $c['id'] }}" data-iso2="{{ strtolower($c['iso2']) }}">{{ $c['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Cargo Type <span class="text-danger">*</span></label>
                        <select name="cargo_type" class="form-select" required>
                            <option value="General Cargo">General Cargo</option>
                            <option value="Container">Container</option>
                            <option value="Bulk Cargo">Bulk Cargo</option>
                            <option value="Liquid Cargo">Liquid Cargo</option>
                            <option value="Vehicle">Vehicle</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Ship Speed <span class="text-danger">*</span></label>
                        <select name="ship_speed" class="form-select" required>
                            <option value="normal">Normal (30 km/h)</option>
                            <option value="slow">Slow (20 km/h)</option>
                            <option value="fast">Fast (40 km/h)</option>
                        </select>
                    </div>

                    <button type="submit" id="estimateBtn" class="btn btn-primary w-100 py-2.5 fw-bold d-flex align-items-center justify-content-center gap-2">
                        <span id="btnText"><i class="bi bi-play-circle-fill"></i> Estimate Shipment</span>
                        <span id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Map Section --}}
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 h-100 rounded-4" style="background: var(--surface); overflow: hidden;">
            <div class="card-header border-bottom-0 pt-4 px-4 bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-map me-1 text-primary"></i> Shipping Route Visualizer</h5>
                <span id="activeSimulationBadge" class="badge bg-success d-none animate-pulse">🚢 Simulating...</span>
            </div>
            <div class="card-body p-0" style="position: relative;">
                {{-- Map Container --}}
                <div id="shipmentMap" style="height: 400px; z-index: 1;"></div>

                {{-- Animation Stats overlay (shown during animation) --}}
                <div id="animationOverlay" class="position-absolute bottom-0 start-0 end-0 p-3 bg-dark bg-opacity-75 text-white d-none" style="z-index: 2; backdrop-filter: blur(4px);">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-3 text-center border-end border-secondary">
                            <small class="text-muted d-block uppercase text-xs">Current Position</small>
                            <span id="overlayCoords" class="fw-semibold font-monospace" style="font-size: .8rem;">—</span>
                        </div>
                        <div class="col-md-3 text-center border-end border-secondary">
                            <small class="text-muted d-block uppercase text-xs">Traveled</small>
                            <span id="overlayTraveled" class="fw-bold" style="font-size: 1rem; color: #10b981;">0 km</span>
                        </div>
                        <div class="col-md-3 text-center border-end border-secondary">
                            <small class="text-muted d-block uppercase text-xs">Remaining</small>
                            <span id="overlayRemaining" class="fw-bold" style="font-size: 1rem; color: #fbbf24;">—</span>
                        </div>
                        <div class="col-md-3 text-center">
                            <small class="text-muted d-block uppercase text-xs">Progress</small>
                            <span id="overlayProgress" class="fw-bold" style="font-size: 1.1rem; color: #3b82f6;">0%</span>
                        </div>
                    </div>
                    <div class="progress mt-2" style="height: 6px; border-radius: 99px; background: rgba(255,255,255,.1);">
                        <div id="overlayProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Metric Dashboard Row --}}
<div id="metricsRow" class="row g-3 mb-4 d-none">
    {{-- Distance --}}
    <div class="col-md-3">
        <div class="card shadow-sm border-0 p-3 d-flex flex-row align-items-center gap-3 metric-card-hover" style="border-radius: var(--radius-md); background: var(--surface);">
            <div class="d-flex align-items-center justify-content-center text-primary" style="width: 44px; height: 44px; border-radius: 10px; background: rgba(59,130,246,.1);">
                <i class="bi bi-rulers" style="font-size: 1.25rem;"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold text-uppercase" style="font-size: .68rem; letter-spacing: .05em;">Total Distance</div>
                <h4 id="metricDistance" class="fw-bold text-dark mb-0" style="font-size: 1.2rem;">—</h4>
            </div>
        </div>
    </div>

    {{-- Estimated Time --}}
    <div class="col-md-3">
        <div class="card shadow-sm border-0 p-3 d-flex flex-row align-items-center gap-3 metric-card-hover" style="border-radius: var(--radius-md); background: var(--surface);">
            <div class="d-flex align-items-center justify-content-center text-success" style="width: 44px; height: 44px; border-radius: 10px; background: rgba(16,185,129,.1);">
                <i class="bi bi-clock-history" style="font-size: 1.25rem;"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold text-uppercase" style="font-size: .68rem; letter-spacing: .05em;">Estimated Time</div>
                <h4 id="metricTime" class="fw-bold text-dark mb-0" style="font-size: 1.2rem;">—</h4>
            </div>
        </div>
    </div>

    {{-- Risk Score --}}
    <div class="col-md-3">
        <div class="card shadow-sm border-0 p-3 d-flex flex-row align-items-center gap-3 metric-card-hover" style="border-radius: var(--radius-md); background: var(--surface);">
            <div id="metricRiskIcon" class="d-flex align-items-center justify-content-center text-warning" style="width: 44px; height: 44px; border-radius: 10px; background: rgba(245,158,11,.1);">
                <i class="bi bi-shield-exclamation" style="font-size: 1.25rem;"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold text-uppercase" style="font-size: .68rem; letter-spacing: .05em;">Route Risk Profile</div>
                <h4 id="metricRisk" class="fw-bold text-dark mb-0" style="font-size: 1.2rem;">—</h4>
            </div>
        </div>
    </div>

    {{-- Weather Status --}}
    <div class="col-md-3">
        <div class="card shadow-sm border-0 p-3 d-flex flex-row align-items-center gap-3 metric-card-hover" style="border-radius: var(--radius-md); background: var(--surface);">
            <div id="metricWeatherIcon" class="d-flex align-items-center justify-content-center text-info" style="width: 44px; height: 44px; border-radius: 10px; background: rgba(6,182,212,.1);">
                <i class="bi bi-cloud-sun" style="font-size: 1.25rem;"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold text-uppercase" style="font-size: .68rem; letter-spacing: .05em;">Weather Outlook</div>
                <h4 id="metricWeather" class="fw-bold text-dark mb-0" style="font-size: 1.2rem;">—</h4>
            </div>
        </div>
    </div>
</div>

{{-- Disruption Alert Banner --}}
<div id="disruptionAlertBanner" class="alert alert-danger border-0 shadow-sm rounded-4 p-3 mb-4 d-none" style="background:var(--danger-bg); color:var(--danger); border-left:4px solid var(--danger)!important;">
    <div class="d-flex align-items-center gap-2">
        <i class="bi bi-exclamation-triangle-fill" style="font-size: 1.2rem;"></i>
        <strong style="font-size: .92rem;">Shipment may experience disruption based on recent international news.</strong>
    </div>
</div>

{{-- Stepper Progress Tracker & Timeline Panel --}}
<div id="stepperPanel" class="card shadow-sm border-0 rounded-4 mb-4 d-none" style="background: var(--surface);">
    <div class="card-body p-4">
        {{-- Progress Tracker Steps --}}
        <h6 class="fw-bold text-secondary text-uppercase mb-3" style="font-size: .75rem; letter-spacing: .05em;">Shipment Journey Stepper</h6>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 text-center shipment-stepper">
            <div class="step-item active" id="step1">
                <div class="step-circle"><i class="bi bi-building"></i></div>
                <div class="step-label">Origin Port</div>
            </div>
            <i class="bi bi-chevron-right text-muted d-none d-md-block"></i>
            <div class="step-item" id="step2">
                <div class="step-circle"><i class="bi bi-box-seam"></i></div>
                <div class="step-label">Loading Cargo</div>
            </div>
            <i class="bi bi-chevron-right text-muted d-none d-md-block"></i>
            <div class="step-item" id="step3">
                <div class="step-circle"><i class="bi bi-ship"></i></div>
                <div class="step-label">Ocean Shipping</div>
            </div>
            <i class="bi bi-chevron-right text-muted d-none d-md-block"></i>
            <div class="step-item" id="step4">
                <div class="step-circle"><i class="bi bi-file-earmark-check"></i></div>
                <div class="step-label">Custom Clearance</div>
            </div>
            <i class="bi bi-chevron-right text-muted d-none d-md-block"></i>
            <div class="step-item" id="step5">
                <div class="step-circle"><i class="bi bi-geo-alt"></i></div>
                <div class="step-label">Destination Port</div>
            </div>
            <i class="bi bi-chevron-right text-muted d-none d-md-block"></i>
            <div class="step-item" id="step6">
                <div class="step-circle"><i class="bi bi-check-circle-fill"></i></div>
                <div class="step-label">Delivered</div>
            </div>
        </div>

        <hr class="my-4">

        {{-- Horizontal Timeline --}}
        <h6 class="fw-bold text-secondary text-uppercase mb-3" style="font-size: .75rem; letter-spacing: .05em;">Voyage Milestones</h6>
        <div class="horizontal-timeline d-flex justify-content-between text-center">
            <div class="timeline-node active" id="node1">
                <div class="node-dot"></div>
                <div class="node-label">Origin</div>
            </div>
            <div class="timeline-node" id="node2">
                <div class="node-dot"></div>
                <div class="node-label">Departure</div>
            </div>
            <div class="timeline-node" id="node3">
                <div class="node-dot"></div>
                <div class="node-label">Ocean Route</div>
            </div>
            <div class="timeline-node" id="node4">
                <div class="node-dot"></div>
                <div class="node-label">Arrival</div>
            </div>
            <div class="timeline-node" id="node5">
                <div class="node-dot"></div>
                <div class="node-label">Completed</div>
            </div>
        </div>
    </div>
</div>

{{-- Results Row: Summary, Weather, Risk & News Panels --}}
<div id="resultsRow" class="row g-4 d-none">
    {{-- Shipment Summary --}}
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 rounded-4 h-100" style="background: var(--surface);">
            <div class="card-header border-bottom-0 pt-4 px-4 bg-transparent">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-file-earmark-spreadsheet me-1"></i> Shipment Summary</h5>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="d-flex flex-column gap-2.5">
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Origin Country</span>
                        <span id="summaryOriginCountry" class="fw-semibold text-dark small">—</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Destination Country</span>
                        <span id="summaryDestCountry" class="fw-semibold text-dark small">—</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Origin Port</span>
                        <span id="summaryOriginPort" class="fw-semibold text-primary small">—</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Destination Port</span>
                        <span id="summaryDestPort" class="fw-semibold text-danger small">—</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Cargo Type</span>
                        <span id="summaryCargo" class="fw-semibold text-dark small">—</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Ship Speed</span>
                        <span id="summarySpeed" class="fw-semibold text-dark small">—</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Distance</span>
                        <span id="summaryDistance" class="fw-bold text-dark small">—</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Estimated Time</span>
                        <span id="summaryTime" class="fw-bold text-dark small">—</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted small">Departure</span>
                        <span id="summaryDeparture" class="fw-semibold text-secondary small">—</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted small">Estimated Arrival</span>
                        <span id="summaryArrival" class="fw-semibold text-success small">—</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Weather & Risk Panels --}}
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 rounded-4 h-100" style="background: var(--surface);">
            <div class="card-header border-bottom-0 pt-4 px-4 bg-transparent">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-shield-check me-1"></i> Weather & Risk Engine</h5>
            </div>
            <div class="card-body px-4 pb-4">
                {{-- Weather Info --}}
                <h6 class="fw-bold text-secondary text-uppercase mb-3" style="font-size: .75rem; letter-spacing: .05em;">Weather Forecast</h6>
                
                <div class="p-3 rounded-3 mb-3" style="background: var(--surface-alt);">
                    <div class="fw-semibold text-dark mb-2" style="font-size: .8rem;">Origin Port Weather</div>
                    <div class="row g-1 text-center">
                        <div class="col-4 border-end">
                            <small class="text-muted text-xs d-block">Temp</small>
                            <span id="weatherOrigTemp" class="fw-bold font-monospace text-dark small">—</span>
                        </div>
                        <div class="col-4 border-end">
                            <small class="text-muted text-xs d-block">Wind</small>
                            <span id="weatherOrigWind" class="fw-bold font-monospace text-dark small">—</span>
                        </div>
                        <div class="col-4">
                            <small class="text-muted text-xs d-block">Condition</small>
                            <span id="weatherOrigCond" class="fw-bold text-dark text-truncate d-block small" style="font-size:.7rem;">—</span>
                        </div>
                    </div>
                </div>

                <div class="p-3 rounded-3 mb-4" style="background: var(--surface-alt);">
                    <div class="fw-semibold text-dark mb-2" style="font-size: .8rem;">Destination Port Weather</div>
                    <div class="row g-1 text-center">
                        <div class="col-4 border-end">
                            <small class="text-muted text-xs d-block">Temp</small>
                            <span id="weatherDestTemp" class="fw-bold font-monospace text-dark small">—</span>
                        </div>
                        <div class="col-4 border-end">
                            <small class="text-muted text-xs d-block">Wind</small>
                            <span id="weatherDestWind" class="fw-bold font-monospace text-dark small">—</span>
                        </div>
                        <div class="col-4">
                            <small class="text-muted text-xs d-block">Condition</small>
                            <span id="weatherDestCond" class="fw-bold text-dark text-truncate d-block small" style="font-size:.7rem;">—</span>
                        </div>
                    </div>
                </div>

                {{-- Risk Info --}}
                <h6 class="fw-bold text-secondary text-uppercase mb-3" style="font-size: .75rem; letter-spacing: .05em;">Risk Scores</h6>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="small text-muted">Origin Risk Score</span>
                    <span id="riskOrigScore" class="badge px-3 py-1.5" style="border-radius: 99px;">—</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="small text-muted">Destination Risk Score</span>
                    <span id="riskDestScore" class="badge px-3 py-1.5" style="border-radius: 99px;">—</span>
                </div>
            </div>
        </div>
    </div>

    {{-- News Feed --}}
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 rounded-4 h-100" style="background: var(--surface);">
            <div class="card-header border-bottom-0 pt-4 px-4 bg-transparent">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-newspaper me-1"></i> Disruption Intelligence</h5>
            </div>
            <div class="card-body px-4 pb-4">
                <div id="newsContainer" class="d-flex flex-column gap-3">
                    {{-- news items will be appended here dynamically --}}
                    <div class="py-5 text-center text-muted">
                        No news articles retrieved.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Skeleton Loading placeholder --}}
<div id="skeletonLoading" class="row g-4 mt-1 d-none">
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 rounded-4 p-4">
            <div class="placeholder-glow">
                <span class="placeholder col-6 mb-3"></span>
                <span class="placeholder col-12 mb-2"></span>
                <span class="placeholder col-8 mb-2"></span>
                <span class="placeholder col-10 mb-2"></span>
                <span class="placeholder col-6"></span>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 rounded-4 p-4">
            <div class="placeholder-glow">
                <span class="placeholder col-6 mb-3"></span>
                <span class="placeholder col-12 mb-2"></span>
                <span class="placeholder col-9 mb-2"></span>
                <span class="placeholder col-11 mb-2"></span>
                <span class="placeholder col-7"></span>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 rounded-4 p-4">
            <div class="placeholder-glow">
                <span class="placeholder col-6 mb-3"></span>
                <span class="placeholder col-12 mb-2"></span>
                <span class="placeholder col-8 mb-2"></span>
                <span class="placeholder col-10 mb-2"></span>
                <span class="placeholder col-5"></span>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
{{-- Select2 CSS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<style>
/* Modern shipment estimation styling */
.glass-card {
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
}
.metric-card-hover {
    transition: all 0.3s ease;
}
.metric-card-hover:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-md) !important;
}
.text-xs {
    font-size: .65rem;
}
.uppercase {
    text-transform: uppercase;
    letter-spacing: .05em;
}

/* Stepper styles */
.shipment-stepper .step-item {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    min-width: 80px;
}
.shipment-stepper .step-circle {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: var(--surface-alt);
    border: 2.5px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-subtle);
    font-size: 1rem;
    transition: all 0.3s ease;
    z-index: 2;
}
.shipment-stepper .step-label {
    margin-top: 8px;
    font-size: 0.72rem;
    font-weight: 600;
    color: var(--text-subtle);
}
.shipment-stepper .step-item.active .step-circle {
    background: var(--primary-50);
    border-color: var(--primary-light);
    color: var(--primary-light);
    box-shadow: 0 0 10px rgba(59,130,246,0.3);
}
.shipment-stepper .step-item.active .step-label {
    color: var(--text-dark);
}
.shipment-stepper .step-item.completed .step-circle {
    background: var(--success);
    border-color: var(--success);
    color: white;
}
.shipment-stepper .step-item.completed .step-label {
    color: var(--success);
}

/* Timeline styles */
.horizontal-timeline {
    position: relative;
    padding: 10px 0;
}
.horizontal-timeline::before {
    content: '';
    position: absolute;
    top: 22px;
    left: 4%;
    right: 4%;
    height: 3px;
    background: var(--border);
    z-index: 1;
}
.timeline-node {
    position: relative;
    z-index: 2;
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
}
.timeline-node .node-dot {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: var(--surface);
    border: 3px solid var(--border);
    transition: all 0.3s ease;
}
.timeline-node .node-label {
    margin-top: 8px;
    font-size: 0.7rem;
    font-weight: 500;
    color: var(--text-subtle);
}
.timeline-node.active .node-dot {
    background: var(--primary-light);
    border-color: var(--primary-light);
    transform: scale(1.2);
}
.timeline-node.active .node-label {
    color: var(--primary-light);
    font-weight: 700;
}
.timeline-node.completed .node-dot {
    background: var(--success);
    border-color: var(--success);
}

/* Select2 visual alignment with bootstrap 5 input-groups */
.select2-container--bootstrap-5 .select2-selection {
    height: 40px;
    display: flex;
    align-items: center;
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
}
.select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
    color: var(--text-dark);
    font-size: .85rem;
}
</style>
@endpush

@push('scripts')
{{-- Load jQuery, Select2, and SweetAlert2 CDNs --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    function formatCountryState(state) {
        if (!state.id) {
            return state.text;
        }
        const iso2 = $(state.element).data('iso2');
        if (!iso2) {
            return state.text;
        }
        const flagUrl = `https://flagcdn.com/w320/${iso2}.png`;
        const $state = $(
            `<span class="d-flex align-items-center"><img src="${flagUrl}" style="width: 20px; height: 13.3px; object-fit: cover; border-radius: 2px; margin-right: 8px; display: inline-block; vertical-align: middle;"><span>${state.text}</span></span>`
        );
        return $state;
    }

    // 1. Initialize searchable Select2 dropdowns
    $('.select2-enable').select2({
        theme: 'bootstrap-5',
        width: '100%',
        templateResult: formatCountryState,
        templateSelection: formatCountryState
    });

    // 2. Initialize Leaflet Map
    const map = L.map('shipmentMap').setView([10, 115], 3);
    
    // Add beautiful dark-mode cartographic tile layer
    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 20
    }).addTo(map);

    let routePolyline = null;
    let boatMarker = null;
    let originMarker = null;
    let destMarker = null;
    let animationFrameId = null;

    // Custom Map Markers
    const originIcon = L.divIcon({
        className: 'custom-div-icon',
        html: '<div style="background-color:#10b981; width:12px; height:12px; border-radius:50%; border:2px solid white; box-shadow:0 0 8px #10b981;"></div>',
        iconSize: [12, 12],
        iconAnchor: [6, 6]
    });

    const destIcon = L.divIcon({
        className: 'custom-div-icon',
        html: '<div style="background-color:#dc2626; width:12px; height:12px; border-radius:50%; border:2px solid white; box-shadow:0 0 8px #dc2626;"></div>',
        iconSize: [12, 12],
        iconAnchor: [6, 6]
    });

    const boatIcon = L.divIcon({
        className: 'custom-div-icon',
        html: '<div style="font-size:20px; text-shadow:0 0 6px rgba(255,255,255,0.7); display:flex; align-items:center; justify-content:center;">🚢</div>',
        iconSize: [24, 24],
        iconAnchor: [12, 12]
    });

    // 3. Handle Simulation Submission
    const form = document.getElementById("estimationForm");
    const estimateBtn = document.getElementById("estimateBtn");
    const btnText = document.getElementById("btnText");
    const btnSpinner = document.getElementById("btnSpinner");
    const skeleton = document.getElementById("skeletonLoading");
    const metrics = document.getElementById("metricsRow");
    const stepper = document.getElementById("stepperPanel");
    const results = document.getElementById("resultsRow");
    const alertBanner = document.getElementById("disruptionAlertBanner");

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        // Validate distinct origin/dest
        const originId = document.getElementById("originCountry").value;
        const destId = document.getElementById("destCountry").value;
        if (originId === destId) {
            Swal.fire({
                title: 'Validation Error',
                text: 'Negara tujuan harus berbeda dengan negara asal.',
                icon: 'warning',
                confirmButtonColor: '#3b82f6'
            });
            return;
        }

        // Enter loading state
        estimateBtn.disabled = true;
        btnText.classList.add("d-none");
        btnSpinner.classList.remove("d-none");
        skeleton.classList.remove("d-none");
        metrics.classList.add("d-none");
        stepper.classList.add("d-none");
        results.classList.add("d-none");
        alertBanner.classList.add("d-none");

        // Clear existing map layers & animation
        if (routePolyline) map.removeLayer(routePolyline);
        if (boatMarker) map.removeLayer(boatMarker);
        if (originMarker) map.removeLayer(originMarker);
        if (destMarker) map.removeLayer(destMarker);
        if (animationFrameId) {
            cancelAnimationFrame(animationFrameId);
            animationFrameId = null;
        }
        document.getElementById("animationOverlay").classList.add("d-none");
        document.getElementById("activeSimulationBadge").classList.add("d-none");

        // Request simulation
        const formData = new FormData(form);
        fetch("{{ route('shipment-estimation.estimate') }}", {
            method: "POST",
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(res => {
            if (!res.ok) {
                return res.json().then(err => { throw new Error(err.error || 'Server error'); });
            }
            return res.json();
        })
        .then(res => {
            if (res.success && res.data) {
                renderSimulation(res.data);
            } else {
                throw new Error(res.error || 'Estimation failed');
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire({
                title: 'Unable to retrieve shipment estimation.',
                text: err.message || 'Please try again later.',
                icon: 'error',
                confirmButtonColor: '#ef4444'
            });
        })
        .finally(() => {
            // Exit loading state
            estimateBtn.disabled = false;
            btnText.classList.remove("d-none");
            btnSpinner.classList.add("d-none");
            skeleton.classList.add("d-none");
        });
    });

    // 4. Render and Animate the Simulation Route
    function renderSimulation(data) {
        // Show panels
        metrics.classList.remove("d-none");
        stepper.classList.remove("d-none");
        results.classList.remove("d-none");
        
        // Show alert banner if disruption news
        if (data.has_disruption_news) {
            alertBanner.classList.remove("d-none");
        }

        // Set metrics
        document.getElementById("metricDistance").textContent = `${data.distance_km} km`;
        document.getElementById("metricTime").textContent = data.duration;
        document.getElementById("metricRisk").textContent = `${data.dest_risk.level} (${data.dest_risk.score})`;
        document.getElementById("metricWeather").textContent = data.dest_weather.condition;

        // Set risk class styles
        const riskCard = document.getElementById("metricRiskIcon");
        riskCard.className = "d-flex align-items-center justify-content-center";
        if (data.dest_risk.level === 'HIGH') {
            riskCard.classList.add("text-danger");
            riskCard.style.backgroundColor = "rgba(220,38,38,.1)";
        } else if (data.dest_risk.level === 'MEDIUM') {
            riskCard.classList.add("text-warning");
            riskCard.style.backgroundColor = "rgba(245,158,11,.1)";
        } else {
            riskCard.classList.add("text-success");
            riskCard.style.backgroundColor = "rgba(16,185,129,.1)";
        }

        // Set weather class styles
        const weatherCard = document.getElementById("metricWeatherIcon");
        weatherCard.className = "d-flex align-items-center justify-content-center";
        if (data.potential_delay) {
            weatherCard.classList.add("text-danger");
            weatherCard.style.backgroundColor = "rgba(220,38,38,.1)";
        } else {
            weatherCard.classList.add("text-info");
            weatherCard.style.backgroundColor = "rgba(6,182,212,.1)";
        }

        // Set Summary
        const originIso = $('#originCountry option:selected').data('iso2');
        const destIso = $('#destCountry option:selected').data('iso2');
        const originFlagUrl = originIso ? `https://flagcdn.com/w320/${originIso.toLowerCase()}.png` : 'https://flagcdn.com/w320/un.png';
        const destFlagUrl = destIso ? `https://flagcdn.com/w320/${destIso.toLowerCase()}.png` : 'https://flagcdn.com/w320/un.png';

        document.getElementById("summaryOriginCountry").innerHTML = `
            <div class="d-flex align-items-center">
                <img src="${originFlagUrl}" style="width: 24px; height: 16px; object-fit: cover; border-radius: 2px; margin-right: 6px;">
                <span>${data.origin_port.country}</span>
            </div>
        `;
        document.getElementById("summaryDestCountry").innerHTML = `
            <div class="d-flex align-items-center">
                <img src="${destFlagUrl}" style="width: 24px; height: 16px; object-fit: cover; border-radius: 2px; margin-right: 6px;">
                <span>${data.dest_port.country}</span>
            </div>
        `;
        document.getElementById("summaryOriginPort").textContent = `${data.origin_port.name} (${data.origin_port.code})`;
        document.getElementById("summaryDestPort").textContent = `${data.dest_port.name} (${data.dest_port.code})`;
        document.getElementById("summaryCargo").textContent = data.cargo_type;
        document.getElementById("summarySpeed").textContent = `${data.speed_kmh} km/h`;
        document.getElementById("summaryDistance").textContent = `${data.distance_km} km`;
        document.getElementById("summaryTime").textContent = data.duration;
        document.getElementById("summaryDeparture").textContent = data.departure;
        document.getElementById("summaryArrival").textContent = data.arrival;

        // Set Weather details
        document.getElementById("weatherOrigTemp").textContent = `${data.origin_weather.temperature}°C`;
        document.getElementById("weatherOrigWind").textContent = `${data.origin_weather.wind_speed} km/h`;
        document.getElementById("weatherOrigCond").textContent = data.origin_weather.condition;

        document.getElementById("weatherDestTemp").textContent = `${data.dest_weather.temperature}°C`;
        document.getElementById("weatherDestWind").textContent = `${data.dest_weather.wind_speed} km/h`;
        document.getElementById("weatherDestCond").textContent = data.dest_weather.condition;

        // Set Risk badges
        const rOrig = document.getElementById("riskOrigScore");
        rOrig.textContent = `${data.origin_risk.level} (${data.origin_risk.score})`;
        rOrig.className = "badge px-3 py-1.5 " + getRiskBadgeClass(data.origin_risk.level);

        const rDest = document.getElementById("riskDestScore");
        rDest.textContent = `${data.dest_risk.level} (${data.dest_risk.score})`;
        rDest.className = "badge px-3 py-1.5 " + getRiskBadgeClass(data.dest_risk.level);

        // Set News feed
        const newsContainer = document.getElementById("newsContainer");
        newsContainer.innerHTML = '';
        if (data.news && data.news.length > 0) {
            data.news.forEach(n => {
                const item = document.createElement("div");
                item.className = "border-bottom pb-2";
                item.innerHTML = `
                    <a href="${n.url}" target="_blank" class="fw-semibold text-dark text-decoration-none small d-block mb-1 text-hover-primary" style="line-height:1.35;">
                        ${n.title}
                    </a>
                    <div class="d-flex justify-content-between text-xs text-muted">
                        <span>Source: ${n.source}</span>
                        <span>${new Date(n.published_at).toLocaleDateString()}</span>
                    </div>
                `;
                newsContainer.appendChild(item);
            });
        } else {
            newsContainer.innerHTML = '<div class="py-4 text-center text-muted small">No related disruption news found.</div>';
        }

        // Draw Map Routes
        const pathCoords = data.waypoints.map(w => [w.lat, w.lng]);
        
        originMarker = L.marker(pathCoords[0], { icon: originIcon }).addTo(map)
            .bindPopup(`<strong>${data.origin_port.name}</strong><br>${data.origin_port.country}<br>Lat: ${data.origin_port.lat}<br>Lng: ${data.origin_port.lng}`);
        
        destMarker = L.marker(pathCoords[pathCoords.length - 1], { icon: destIcon }).addTo(map)
            .bindPopup(`<strong>${data.dest_port.name}</strong><br>${data.dest_port.country}<br>Lat: ${data.dest_port.lat}<br>Lng: ${data.dest_port.lng}`);

        // Polyline path drawing
        routePolyline = L.polyline(pathCoords, {
            color: '#3b82f6',
            weight: 3.5,
            opacity: 0.8,
            dashArray: '8, 8'
        }).addTo(map);

        // Zoom map to fit path
        map.fitBounds(routePolyline.getBounds(), { padding: [40, 40] });

        // Interpolate waypoints coordinates list for smooth animation
        const interpolated = interpolatePoints(data.waypoints, 60);

        // Add moving boat marker
        boatMarker = L.marker(pathCoords[0], { icon: boatIcon }).addTo(map);

        // Display overlays
        document.getElementById("animationOverlay").classList.remove("d-none");
        document.getElementById("activeSimulationBadge").classList.remove("d-none");

        // Stepper animation speed maps to ship speed setting
        let speedVal = 70; // normal
        const speedSetting = document.querySelector('select[name="ship_speed"]').value;
        if (speedSetting === 'fast') speedVal = 35;
        else if (speedSetting === 'slow') speedVal = 130;

        animateShipment(interpolated, data.distance_km, speedVal);
    }

    // Helpers
    function getRiskBadgeClass(level) {
        if (level === 'HIGH') return 'bg-danger';
        if (level === 'MEDIUM') return 'bg-warning text-dark';
        return 'bg-success';
    }

    // Interpolate waypoint points to get smooth coordinate steps
    function interpolatePoints(points, stepsPerSegment = 60) {
        let interpolated = [];
        for (let i = 0; i < points.length - 1; i++) {
            let p1 = points[i];
            let p2 = points[i+1];
            for (let step = 0; step < stepsPerSegment; step++) {
                let pct = step / stepsPerSegment;
                interpolated.push([
                    p1.lat + (p2.lat - p1.lat) * pct,
                    p1.lng + (p2.lng - p1.lng) * pct
                ]);
            }
        }
        interpolated.push([points[points.length - 1].lat, points[points.length - 1].lng]);
        return interpolated;
    }

    // Animate boat marker along points
    function animateShipment(coords, totalDistance, speedVal) {
        let idx = 0;
        
        // Reset steppers
        document.querySelectorAll(".step-item").forEach(el => el.className = "step-item");
        document.querySelectorAll(".timeline-node").forEach(el => el.className = "timeline-node");
        
        document.getElementById("step1").classList.add("active");
        document.getElementById("node1").classList.add("active");

        function tick() {
            if (idx < coords.length) {
                const currentCoord = coords[idx];
                boatMarker.setLatLng(currentCoord);

                // Calculations
                const pct = (idx / (coords.length - 1)) * 100;
                const traveled = (pct * totalDistance) / 100;
                const remaining = totalDistance - traveled;

                // Update overlay parameters
                document.getElementById("overlayCoords").textContent = `${currentCoord[0].toFixed(4)}, ${currentCoord[1].toFixed(4)}`;
                document.getElementById("overlayTraveled").textContent = `${traveled.toFixed(1)} km`;
                document.getElementById("overlayRemaining").textContent = `${remaining.toFixed(1)} km`;
                document.getElementById("overlayProgress").textContent = `${Math.round(pct)}%`;
                document.getElementById("overlayProgressBar").style.width = `${pct}%`;

                // Update Stepper Stages based on progress
                updateJourneyStages(pct);

                idx++;
                // Set animation timer loop
                animationFrameId = setTimeout(tick, speedVal);
            } else {
                // Shipment successfully finished!
                clearTimeout(animationFrameId);
                animationFrameId = null;

                document.getElementById("activeSimulationBadge").classList.add("d-none");
                
                // Complete last stepper nodes
                document.getElementById("step6").className = "step-item completed";
                document.getElementById("node5").className = "timeline-node completed";

                Swal.fire({
                    title: 'Shipment Successfully Arrived',
                    text: 'Kargo pengiriman laut Anda telah berlabuh di pelabuhan tujuan dengan selamat.',
                    icon: 'success',
                    confirmButtonColor: '#10b981'
                });
            }
        }

        tick();
    }

    function updateJourneyStages(pct) {
        // Reset stepper classes
        const s1 = document.getElementById("step1");
        const s2 = document.getElementById("step2");
        const s3 = document.getElementById("step3");
        const s4 = document.getElementById("step4");
        const s5 = document.getElementById("step5");
        const s6 = document.getElementById("step6");

        const n1 = document.getElementById("node1");
        const n2 = document.getElementById("node2");
        const n3 = document.getElementById("node3");
        const n4 = document.getElementById("node4");
        const n5 = document.getElementById("node5");

        if (pct >= 0 && pct < 15) {
            s1.className = "step-item active";
            s2.className = "step-item";
            s3.className = "step-item";
            s4.className = "step-item";
            s5.className = "step-item";
            s6.className = "step-item";

            n1.className = "timeline-node active";
            n2.className = "timeline-node";
            n3.className = "timeline-node";
            n4.className = "timeline-node";
            n5.className = "timeline-node";
        } 
        else if (pct >= 15 && pct < 35) {
            s1.className = "step-item completed";
            s2.className = "step-item active";
            s3.className = "step-item";
            s4.className = "step-item";
            s5.className = "step-item";
            s6.className = "step-item";

            n1.className = "timeline-node completed";
            n2.className = "timeline-node active";
            n3.className = "timeline-node";
            n4.className = "timeline-node";
            n5.className = "timeline-node";
        }
        else if (pct >= 35 && pct < 75) {
            s1.className = "step-item completed";
            s2.className = "step-item completed";
            s3.className = "step-item active";
            s4.className = "step-item";
            s5.className = "step-item";
            s6.className = "step-item";

            n1.className = "timeline-node completed";
            n2.className = "timeline-node completed";
            n3.className = "timeline-node active";
            n4.className = "timeline-node";
            n5.className = "timeline-node";
        }
        else if (pct >= 75 && pct < 90) {
            s1.className = "step-item completed";
            s2.className = "step-item completed";
            s3.className = "step-item completed";
            s4.className = "step-item active";
            s5.className = "step-item";
            s6.className = "step-item";

            n1.className = "timeline-node completed";
            n2.className = "timeline-node completed";
            n3.className = "timeline-node completed";
            n4.className = "timeline-node active";
            n5.className = "timeline-node";
        }
        else if (pct >= 90 && pct < 100) {
            s1.className = "step-item completed";
            s2.className = "step-item completed";
            s3.className = "step-item completed";
            s4.className = "step-item completed";
            s5.className = "step-item active";
            s6.className = "step-item";

            n1.className = "timeline-node completed";
            n2.className = "timeline-node completed";
            n3.className = "timeline-node completed";
            n4.className = "timeline-node completed";
            n5.className = "timeline-node active";
        }
    }
});
</script>
@endpush
