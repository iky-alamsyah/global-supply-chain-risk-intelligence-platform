@extends('layouts.dashboard')

@section('title', 'Country Comparison')

@section('content')

{{-- Page Header --}}
<div class="page-header mb-4">
    <div>
        <h1 class="page-header-title">
            <i class="bi bi-intersect me-2" style="color:#6366F1;font-size:1.2rem;"></i>
            Country Comparison Engine
        </h1>
        <p class="page-header-sub mb-0">
            Select and compare trade risk metrics, climate profiles, currency stability, and maritime infrastructure across countries.
        </p>
    </div>
</div>

<div class="row g-4">

    {{-- Left Selection Panel --}}
    <div class="col-md-3">
        <div class="card h-100">
            <div class="card-header py-3">
                <span class="fw-bold text-enterprise" style="font-size:.85rem;">Select Countries</span>
            </div>
            <div class="card-body p-3 d-flex flex-column" style="max-height: 700px;">
                <form method="GET" action="{{ route('comparison.index') }}" id="compareForm" class="d-flex flex-column h-100">
                    
                    {{-- Container for selected chips --}}
                    <label class="form-label fw-semibold text-enterprise mb-1.5" style="font-size:.78rem;">Selected Countries</label>
                    <div id="selectedChips" class="d-flex flex-wrap gap-1.5 mb-3" style="min-height: 40px; border: 1px dashed rgba(226, 232, 240, 0.1); border-radius: var(--radius-sm); padding: 8px; background: rgba(0,0,0,0.1);">
                        {{-- Tags will be generated dynamically --}}
                    </div>

                    {{-- Search input autocomplete --}}
                    <div class="position-relative mb-4">
                        <label class="form-label fw-semibold text-enterprise" style="font-size:.78rem;">Search Country</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text py-1" style="font-size:.78rem;"><i class="bi bi-search"></i></span>
                            <input type="text" id="autocompleteInput" class="form-control form-control-sm py-1" style="font-size:.78rem;" placeholder="Search country..." autocomplete="off">
                        </div>
                        
                        {{-- Autocomplete Dropdown List --}}
                        <div id="autocompleteDropdown" class="list-group position-absolute w-100 mt-1 shadow" 
                             style="max-height: 280px; overflow-y: auto; z-index: 1050; display: none; background: #0f172a; border: 1px solid rgba(226, 232, 240, 0.12); border-radius: var(--radius-sm);">
                            {{-- Dynamic search list --}}
                        </div>
                    </div>

                    {{-- Hidden inputs container --}}
                    <div id="hiddenInputsContainer"></div>

                    {{-- Compare Action Button --}}
                    <button type="submit" id="btnCompare" class="btn btn-primary btn-sm w-100 py-2" disabled>
                        <i class="bi bi-intersect me-1"></i> Compare Selected
                    </button>
                    
                </form>

                {{-- Recent Compared panel --}}
                <div id="recentComparedPanel" class="mt-4" style="display: none;">
                    <hr style="border-top: 1px solid rgba(226, 232, 240, 0.08);">
                    <label class="form-label fw-semibold text-muted mb-2" style="font-size:.75rem;"><i class="bi bi-history me-1"></i>Recent Comparisons</label>
                    <div id="recentComparedList" class="d-flex flex-column gap-2">
                        {{-- Dynamic recent list --}}
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Right Dashboard Area --}}
    <div class="col-md-9">
        @if($comparedCountries->count() < 2)
            {{-- Empty/Prompt State --}}
            <div class="card d-flex flex-column align-items-center justify-content-center py-5 px-3 text-center h-100">
                <i class="bi bi-intersect d-block mb-3 text-muted" style="font-size:3rem; opacity:.4;"></i>
                <h5 class="fw-bold" style="color:var(--text);">Select at least 2 countries</h5>
                <p class="text-subtle mb-0" style="font-size:.8rem; max-width: 420px;">
                    Use the sidebar on the left to select two or more countries to trigger radar, bar chart, and analytical matrix comparisons.
                </p>
            </div>
        @else
            {{-- Comparison Dashboard --}}
            
            {{-- Chart Panel --}}
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header py-2">
                            <span class="fw-bold text-enterprise" style="font-size:.8rem;"><i class="bi bi-radar me-1"></i>Risk Components Profile (Radar)</span>
                        </div>
                        <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 280px;">
                            <div style="position: relative; height:260px; width:100%;">
                                <canvas id="radarChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header py-2">
                            <span class="fw-bold text-enterprise" style="font-size:.8rem;"><i class="bi bi-bar-chart-fill me-1"></i>Infrastructure & Climate Comparison (Bar)</span>
                        </div>
                        <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 280px;">
                            <div style="position: relative; height:260px; width:100%;">
                                <canvas id="barChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Table Matrix Panel --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <div class="section-icon me-2" style="background:rgba(99,102,241,.08);color:#6366F1;">
                            <i class="bi bi-table"></i>
                        </div>
                        <span class="fw-bold" style="font-size:.9rem;">Comparison Table Matrix</span>
                    </div>
                    <span class="badge" style="background:rgba(99,102,241,.1);color:#6366F1;font-size:.68rem;font-weight:700;border-radius:99px;padding:.25rem .6rem;">
                        Comparing {{ $comparedCountries->count() }} Countries
                    </span>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-enterprise mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th style="min-width: 160px; padding-left: 1.2rem;">Indicator / Metric</th>
                                    @foreach($comparedCountries as $country)
                                        @php
                                            $isHighest = $country->id === $highestRiskCountryId;
                                        @endphp
                                        <th class="text-center {{ $isHighest ? 'table-danger-highlight-header' : '' }}" 
                                            style="min-width: 150px; {{ $isHighest ? 'background:rgba(239,68,68,.05); border-bottom: 2px solid #ef4444;' : '' }}">
                                             <div class="mb-1">
                                                 <x-country-flag :country="$country" size="md" />
                                             </div>
                                             <div class="fw-bold" style="font-size: .88rem; color: var(--text);">
                                                 {{ $country->name }}
                                             </div>
                                            <div class="text-subtle font-monospace" style="font-size: .68rem;">
                                                {{ $country->iso3 }}
                                            </div>
                                            @if($isHighest)
                                                <span class="badge bg-danger mt-1" style="font-size: .55rem; padding: .15rem .35rem;">⚠️ Highest Risk</span>
                                            @endif
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Risk Score --}}
                                <tr>
                                    <td class="fw-bold" style="padding-left: 1.2rem;">Total Risk Score</td>
                                    @foreach($comparedCountries as $country)
                                        @php
                                            $score = $country->riskScore ? (float) $country->riskScore->risk_score : 0.0;
                                            $isHighest = $country->id === $highestRiskCountryId;
                                        @endphp
                                        <td class="text-center font-monospace fw-bold {{ $isHighest ? 'table-danger-highlight' : '' }}" 
                                            style="font-size: .9rem; color: #ef4444; {{ $isHighest ? 'background:rgba(239,68,68,.05);' : '' }}">
                                            {{ number_format($score, 1) }}
                                        </td>
                                    @endforeach
                                </tr>

                                {{-- Risk Level --}}
                                <tr>
                                    <td style="padding-left: 1.2rem;">Risk Level</td>
                                    @foreach($comparedCountries as $country)
                                        @php
                                            $level = $country->riskScore ? $country->riskScore->risk_level : 'LOW';
                                            $isHighest = $country->id === $highestRiskCountryId;
                                        @endphp
                                        <td class="text-center {{ $isHighest ? 'table-danger-highlight' : '' }}"
                                            style="{{ $isHighest ? 'background:rgba(239,68,68,.05);' : '' }}">
                                            @if($level === 'HIGH')
                                                <span class="badge badge-risk-high" style="border-radius:99px;font-size:.65rem;padding:.2rem .5rem;">HIGH</span>
                                            @elseif($level === 'MEDIUM')
                                                <span class="badge badge-risk-medium" style="border-radius:99px;font-size:.65rem;padding:.2rem .5rem;">MEDIUM</span>
                                            @else
                                                <span class="badge badge-risk-low" style="border-radius:99px;font-size:.65rem;padding:.2rem .5rem;">LOW</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>

                                {{-- GDP --}}
                                <tr>
                                    <td style="padding-left: 1.2rem;">GDP (Current USD)</td>
                                    @foreach($comparedCountries as $country)
                                        @php
                                            $latestStat = $country->statistics->sortByDesc('year')->first();
                                            $gdp = $latestStat ? (float) $latestStat->gdp : null;
                                            $isHighest = $country->id === $highestRiskCountryId;
                                        @endphp
                                        <td class="text-center font-monospace {{ $isHighest ? 'table-danger-highlight' : '' }}"
                                            style="{{ $isHighest ? 'background:rgba(239,68,68,.05);' : '' }}">
                                            @if($gdp)
                                                ${{ number_format($gdp / 1e9, 2) }} B
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>

                                {{-- GDP Score --}}
                                <tr>
                                    <td style="padding-left: 1.2rem; color: var(--text-subtle); font-size: .75rem;">GDP Risk Score (20%)</td>
                                    @foreach($comparedCountries as $country)
                                        @php
                                            $isHighest = $country->id === $highestRiskCountryId;
                                            $val = $country->riskScore ? $country->riskScore->gdp_score : 0.0;
                                        @endphp
                                        <td class="text-center font-monospace {{ $isHighest ? 'table-danger-highlight' : '' }}" 
                                            style="font-size: .78rem; {{ $isHighest ? 'background:rgba(239,68,68,.05);' : '' }}">
                                            {{ number_format($val, 1) }}
                                        </td>
                                    @endforeach
                                </tr>

                                {{-- Weather Score --}}
                                <tr>
                                    <td style="padding-left: 1.2rem; color: var(--text-subtle); font-size: .75rem;">Weather Risk Score (30%)</td>
                                    @foreach($comparedCountries as $country)
                                        @php
                                            $isHighest = $country->id === $highestRiskCountryId;
                                            $val = $country->riskScore ? $country->riskScore->weather_score : 0.0;
                                        @endphp
                                        <td class="text-center font-monospace {{ $isHighest ? 'table-danger-highlight' : '' }}" 
                                            style="font-size: .78rem; {{ $isHighest ? 'background:rgba(239,68,68,.05);' : '' }}">
                                            {{ number_format($val, 1) }}
                                        </td>
                                    @endforeach
                                </tr>

                                {{-- Currency Score --}}
                                <tr>
                                    <td style="padding-left: 1.2rem; color: var(--text-subtle); font-size: .75rem;">Currency Risk Score (20%)</td>
                                    @foreach($comparedCountries as $country)
                                        @php
                                            $isHighest = $country->id === $highestRiskCountryId;
                                            $val = $country->riskScore ? $country->riskScore->currency_score : 0.0;
                                        @endphp
                                        <td class="text-center font-monospace {{ $isHighest ? 'table-danger-highlight' : '' }}" 
                                            style="font-size: .78rem; {{ $isHighest ? 'background:rgba(239,68,68,.05);' : '' }}">
                                            {{ number_format($val, 1) }}
                                        </td>
                                    @endforeach
                                </tr>

                                {{-- News Score --}}
                                <tr>
                                    <td style="padding-left: 1.2rem; color: var(--text-subtle); font-size: .75rem;">News Risk Score (20%)</td>
                                    @foreach($comparedCountries as $country)
                                        @php
                                            $isHighest = $country->id === $highestRiskCountryId;
                                            $val = $country->riskScore ? $country->riskScore->news_score : 0.0;
                                        @endphp
                                        <td class="text-center font-monospace {{ $isHighest ? 'table-danger-highlight' : '' }}" 
                                            style="font-size: .78rem; {{ $isHighest ? 'background:rgba(239,68,68,.05);' : '' }}">
                                            {{ number_format($val, 1) }}
                                        </td>
                                    @endforeach
                                </tr>

                                {{-- Exchange Rate --}}
                                <tr>
                                    <td style="padding-left: 1.2rem;">Exchange Rate (vs USD)</td>
                                    @foreach($comparedCountries as $country)
                                        @php
                                            $isHighest = $country->id === $highestRiskCountryId;
                                            $rate = $country->latestCurrency;
                                        @endphp
                                        <td class="text-center font-monospace {{ $isHighest ? 'table-danger-highlight' : '' }}"
                                            style="{{ $isHighest ? 'background:rgba(239,68,68,.05);' : '' }}">
                                            @if($rate)
                                                {{ number_format($rate->exchange_rate, 4) }} 
                                                <span style="font-size: .65rem; color: var(--text-subtle);">{{ $country->currency_code }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>

                                {{-- Temperature --}}
                                <tr>
                                    <td style="padding-left: 1.2rem;">Temperature (°C)</td>
                                    @foreach($comparedCountries as $country)
                                        @php
                                            $isHighest = $country->id === $highestRiskCountryId;
                                            $weather = $country->latestWeather;
                                        @endphp
                                        <td class="text-center font-monospace {{ $isHighest ? 'table-danger-highlight' : '' }}"
                                            style="{{ $isHighest ? 'background:rgba(239,68,68,.05);' : '' }}">
                                            @if($weather)
                                                {{ number_format($weather->temperature, 1) }}°C
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>

                                {{-- Total Port --}}
                                <tr>
                                    <td style="padding-left: 1.2rem;">Total Ports</td>
                                    @foreach($comparedCountries as $country)
                                        @php
                                            $isHighest = $country->id === $highestRiskCountryId;
                                        @endphp
                                        <td class="text-center font-monospace {{ $isHighest ? 'table-danger-highlight' : '' }}"
                                            style="{{ $isHighest ? 'background:rgba(239,68,68,.05);' : '' }}">
                                            {{ $country->ports_count }}
                                        </td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>

</div>

@endsection

@push('scripts')
{{-- Autocomplete and Selection Panel logic --}}
<script>
document.addEventListener("DOMContentLoaded", function () {
    // 1. Data initialization
    const allCountries = @json($allCountries);

    const autocompleteInput = document.getElementById('autocompleteInput');
    const autocompleteDropdown = document.getElementById('autocompleteDropdown');
    const selectedChips = document.getElementById('selectedChips');
    const hiddenInputsContainer = document.getElementById('hiddenInputsContainer');
    const btnCompare = document.getElementById('btnCompare');
    
    // Loaded from query parameters or default to empty
    let selectedCountries = [];
    const urlParams = new URLSearchParams(window.location.search);
    const initialCountryIds = urlParams.getAll('countries[]');

    // Populate initial selected countries from URL parameters
    if (initialCountryIds.length > 0) {
        initialCountryIds.forEach(id => {
            const country = allCountries.find(c => c.id == id);
            if (country && !selectedCountries.some(sc => sc.id === country.id)) {
                selectedCountries.push(country);
            }
        });
        
        // Save current comparison to LocalStorage for "Recent Comparisons"
        if (selectedCountries.length >= 2) {
            saveRecentComparison(selectedCountries);
        }
    }

    // Render initial state
    renderChips();
    renderHiddenInputs();
    updateCompareButton();
    renderRecentComparisons();

    // 2. Autocomplete Filter logic
    autocompleteInput.addEventListener('input', function () {
        const query = autocompleteInput.value.toLowerCase().trim();
        if (query.length === 0) {
            hideDropdown();
            return;
        }

        // Filter countries that:
        // - are not already selected
        // - match query in name, iso2, iso3, or currency_code
        const filtered = allCountries.filter(c => {
            const isSelected = selectedCountries.some(sc => sc.id === c.id);
            if (isSelected) return false;

            return c.name.toLowerCase().includes(query) ||
                   c.iso2.toLowerCase().includes(query) ||
                   c.iso3.toLowerCase().includes(query) ||
                   (c.currency_code && c.currency_code.toLowerCase().includes(query));
        });

        renderDropdown(filtered.slice(0, 8)); // limit to 8 results
    });

    // Close dropdown on click outside
    document.addEventListener('click', function (e) {
        if (!autocompleteInput.contains(e.target) && !autocompleteDropdown.contains(e.target)) {
            hideDropdown();
        }
    });

    function showDropdown() {
        autocompleteDropdown.style.display = 'block';
    }

    function hideDropdown() {
        autocompleteDropdown.style.display = 'none';
        autocompleteDropdown.innerHTML = '';
    }

    function renderDropdown(items) {
        if (items.length === 0) {
            autocompleteDropdown.innerHTML = `
                <div class="px-3 py-2.5 text-center text-muted" style="font-size:.78rem;">
                    No country found
                </div>
            `;
            showDropdown();
            return;
        }

        autocompleteDropdown.innerHTML = items.map(c => {
            const flagUrl = c.iso2 ? `https://flagcdn.com/w320/${c.iso2.toLowerCase()}.png` : 'https://flagcdn.com/w320/un.png';
            return `
                <button type="button" class="list-group-item list-group-item-action border-0 px-3 py-2 text-start d-flex justify-content-between align-items-center dropdown-item-btn" 
                   style="background: transparent; color: var(--text); border-radius: 0;"
                   data-id="${c.id}">
                    <div>
                        <div class="fw-bold d-flex align-items-center" style="font-size:.78rem;">
                            <img src="${flagUrl}" style="width: 20px; height: 13.3px; object-fit: cover; border-radius: 2px; margin-right: 6px; display: inline-block; vertical-align: middle;">
                            <span>${c.name}</span>
                        </div>
                        <div style="font-size:.65rem; color: var(--text-subtle);">${c.region || ''}</div>
                    </div>
                    <span class="badge bg-danger bg-opacity-10 text-danger" style="font-size:.65rem; border:1px solid rgba(239,68,68,.2);">
                        Risk: ${c.risk_score.toFixed(1)}
                    </span>
                </button>
            `;
        }).join('');

        // Add event listeners to list items
        const buttons = autocompleteDropdown.querySelectorAll('.dropdown-item-btn');
        buttons.forEach(btn => {
            btn.addEventListener('click', function () {
                const id = parseInt(btn.getAttribute('data-id'));
                selectCountry(id);
            });
        });

        showDropdown();
    }

    function selectCountry(id) {
        if (selectedCountries.length >= 5) {
            alert("Maximum 5 countries can be compared.");
            hideDropdown();
            autocompleteInput.value = '';
            return;
        }

        const country = allCountries.find(c => c.id === id);
        if (country && !selectedCountries.some(sc => sc.id === country.id)) {
            selectedCountries.push(country);
            renderChips();
            renderHiddenInputs();
            updateCompareButton();
        }

        autocompleteInput.value = '';
        hideDropdown();
        autocompleteInput.focus();
    }

    function removeCountry(id) {
        selectedCountries = selectedCountries.filter(c => c.id !== id);
        renderChips();
        renderHiddenInputs();
        updateCompareButton();
    }

    function renderChips() {
        if (selectedCountries.length === 0) {
            selectedChips.innerHTML = `
                <span class="text-muted w-100 text-center align-self-center" style="font-size:.72rem;">Search country...</span>
            `;
            return;
        }

        selectedChips.innerHTML = selectedCountries.map(c => {
            const flagUrl = c.iso2 ? `https://flagcdn.com/w320/${c.iso2.toLowerCase()}.png` : 'https://flagcdn.com/w320/un.png';
            return `
                <span class="badge d-flex align-items-center gap-1" 
                      style="background: var(--primary-50); color: var(--primary-light); font-size: .75rem; font-weight: 600; padding: .35rem .6rem; border-radius: var(--radius-sm);">
                    <span class="d-flex align-items-center"><img src="${flagUrl}" style="width: 20px; height: 13.3px; object-fit: cover; border-radius: 2px; margin-right: 6px; display: inline-block; vertical-align: middle;">${c.name}</span>
                    <span class="btn-close btn-close-white ms-1" style="font-size:.5rem; cursor:pointer;" data-id="${c.id}"></span>
                </span>
            `;
        }).join('');

        // Add close event listeners
        selectedChips.querySelectorAll('.btn-close').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const id = parseInt(btn.getAttribute('data-id'));
                removeCountry(id);
            });
        });
    }

    function renderHiddenInputs() {
        hiddenInputsContainer.innerHTML = selectedCountries.map(c => `
            <input type="hidden" name="countries[]" value="${c.id}">
        `).join('');
    }

    function updateCompareButton() {
        btnCompare.disabled = selectedCountries.length < 2;
    }

    // 3. Recent Comparisons logic
    function saveRecentComparison(group) {
        try {
            let recents = JSON.parse(localStorage.getItem('gscrip_recent_comparisons')) || [];
            
            const item = {
                ids: group.map(c => c.id),
                names: group.map(c => c.name).join(', ')
            };

            recents = recents.filter(r => {
                if (r.ids.length !== item.ids.length) return true;
                return !r.ids.every(id => item.ids.includes(id));
            });

            recents.unshift(item);
            localStorage.setItem('gscrip_recent_comparisons', JSON.stringify(recents.slice(0, 3)));
        } catch (e) {
            console.error('LocalStorage error:', e);
        }
    }

    function renderRecentComparisons() {
        try {
            const recents = JSON.parse(localStorage.getItem('gscrip_recent_comparisons')) || [];
            const panel = document.getElementById('recentComparedPanel');
            const list = document.getElementById('recentComparedList');

            if (recents.length === 0) {
                panel.style.display = 'none';
                return;
            }

            list.innerHTML = recents.map((r, idx) => `
                <button type="button" class="btn btn-outline-secondary btn-sm text-start py-1.5 px-2.5 w-100 d-flex align-items-center gap-1.5 recent-combo-btn" 
                        data-index="${idx}" style="font-size: .72rem; border-color: rgba(226, 232, 240, 0.08); background: rgba(255, 255, 255, 0.01);">
                    <i class="bi bi-clock-history text-muted"></i>
                    <span class="text-truncate flex-grow-1" style="color: var(--text-muted);">${r.names}</span>
                </button>
            `).join('');

            list.querySelectorAll('.recent-combo-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const idx = parseInt(btn.getAttribute('data-index'));
                    const combo = recents[idx];
                    
                    hiddenInputsContainer.innerHTML = combo.ids.map(id => `
                        <input type="hidden" name="countries[]" value="${id}">
                    `).join('');
                    
                    document.getElementById('compareForm').submit();
                });
            });

            panel.style.display = 'block';
        } catch (e) {
            console.error(e);
        }
    }
});
</script>

@if($comparedCountries->count() >= 2)
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Chart.js Radar Chart
    const radarCtx = document.getElementById('radarChart').getContext('2d');
    const radarDatasets = @json($radarDatasets);

    new Chart(radarCtx, {
        type: 'radar',
        data: {
            labels: ['Total Risk', 'GDP Risk', 'Weather Risk', 'Currency Risk', 'News Risk'],
            datasets: radarDatasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: '#94a3b8', font: { family: 'Inter, sans-serif', size: 10 } }
                }
            },
            scales: {
                r: {
                    angleLines: { color: 'rgba(148, 163, 184, 0.1)' },
                    grid: { color: 'rgba(148, 163, 184, 0.1)' },
                    pointLabels: { color: '#94a3b8', font: { family: 'Inter, sans-serif', size: 9, weight: 'bold' } },
                    ticks: { backdropColor: 'transparent', color: '#64748b', font: { size: 8 } },
                    suggestedMin: 0,
                    suggestedMax: 100
                }
            }
        }
    });

    // Chart.js Bar Chart
    const barCtx = document.getElementById('barChart').getContext('2d');
    const barDatasets = @json($barDatasets);

    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: ['Temperature (°C)', 'Total Ports', 'Risk Index Score'],
            datasets: barDatasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: '#94a3b8', font: { family: 'Inter, sans-serif', size: 10 } }
                }
            },
            scales: {
                x: {
                    grid: { show: false, drawBorder: false },
                    ticks: { color: '#94a3b8', font: { family: 'Inter, sans-serif', size: 9 } }
                },
                y: {
                    grid: { color: 'rgba(148, 163, 184, 0.08)', drawBorder: false },
                    ticks: { color: '#94a3b8', font: { family: 'Inter, sans-serif', size: 9 } }
                }
            }
        }
    });
});
</script>
@endif
@endpush