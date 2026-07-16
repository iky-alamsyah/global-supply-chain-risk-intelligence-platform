@extends('layouts.dashboard')

@section('title', 'Currency Intelligence')

@section('content')

{{-- Page Header --}}
<div class="page-header mb-4">
    <div>
        <h1 class="page-header-title">
            <i class="bi bi-currency-exchange me-2" style="color:#16A34A;font-size:1.2rem;"></i>
            Currency Intelligence
        </h1>
        <p class="page-header-sub mb-0">
            Real-time currency exchange rate monitoring and volatility tracking for global trade settlements.
        </p>
    </div>
</div>

{{-- Market Insights Analytics Accordion --}}
<div class="accordion mb-4" id="analyticsAccordion">
    <div class="accordion-item" style="border-radius:var(--radius-md); overflow:hidden; border:1px solid rgba(226, 232, 240, 0.08); background:#0f172a;">
        <h2 class="accordion-header" id="headingAnalytics">
            <button class="accordion-button collapsed fw-bold d-flex align-items-center gap-2" 
                    type="button" 
                    data-bs-toggle="collapse" 
                    data-bs-target="#collapseAnalytics" 
                    aria-expanded="false" 
                    aria-controls="collapseAnalytics"
                    style="background:rgba(22, 163, 74, 0.1); color:#16A34A; font-size:.85rem; border:0; outline:none; box-shadow:none;">
                <i class="bi bi-graph-up-arrow"></i> Currency Market Insights & Volatility Analytics
            </button>
        </h2>
        <div id="collapseAnalytics" class="accordion-collapse collapse" aria-labelledby="headingAnalytics" data-bs-parent="#analyticsAccordion">
            <div class="accordion-body" style="background:#0f172a; border-top: 1px solid rgba(226, 232, 240, 0.08);">
                <div class="row g-3">
                    
                    {{-- Top Appreciation --}}
                    <div class="col-md-4">
                        <div class="card h-100 border-0" style="background:rgba(255,255,255,.02); border-radius:var(--radius-sm);">
                            <div class="card-header py-2" style="background:rgba(255,255,255,.03); border-bottom:0;">
                                <span class="fw-bold text-success" style="font-size:.78rem;"><i class="bi bi-arrow-up-circle-fill me-1"></i>Top Appreciations</span>
                            </div>
                            <div class="card-body p-2">
                                <ul class="list-unstyled mb-0" style="font-size:.72rem;">
                                    @forelse($appreciation->where('change_percentage', '>', 0)->take(5) as $rate)
                                        <li class="d-flex justify-content-between py-1.5 border-bottom border-secondary border-opacity-10">
                                            <span style="color:var(--text);">{{ $rate->country->name }} ({{ $rate->target_currency }})</span>
                                            <span class="text-success fw-bold">▲ +{{ number_format($rate->change_percentage, 4) }}%</span>
                                        </li>
                                    @empty
                                        <li class="text-muted text-center py-3">No significant appreciation detected</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Top Depreciation --}}
                    <div class="col-md-4">
                        <div class="card h-100 border-0" style="background:rgba(255,255,255,.02); border-radius:var(--radius-sm);">
                            <div class="card-header py-2" style="background:rgba(255,255,255,.03); border-bottom:0;">
                                <span class="fw-bold text-danger" style="font-size:.78rem;"><i class="bi bi-arrow-down-circle-fill me-1"></i>Top Depreciations</span>
                            </div>
                            <div class="card-body p-2">
                                <ul class="list-unstyled mb-0" style="font-size:.72rem;">
                                    @forelse($depreciation->where('change_percentage', '<', 0)->take(5) as $rate)
                                        <li class="d-flex justify-content-between py-1.5 border-bottom border-secondary border-opacity-10">
                                            <span style="color:var(--text);">{{ $rate->country->name }} ({{ $rate->target_currency }})</span>
                                            <span class="text-danger fw-bold">▼ {{ number_format($rate->change_percentage, 4) }}%</span>
                                        </li>
                                    @empty
                                        <li class="text-muted text-center py-3">No significant depreciation detected</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Highest Currency Risk --}}
                    <div class="col-md-4">
                        <div class="card h-100 border-0" style="background:rgba(255,255,255,.02); border-radius:var(--radius-sm);">
                            <div class="card-header py-2" style="background:rgba(255,255,255,.03); border-bottom:0;">
                                <span class="fw-bold text-warning" style="font-size:.78rem;"><i class="bi bi-exclamation-triangle-fill me-1"></i>Highest Currency Risk</span>
                            </div>
                            <div class="card-body p-2">
                                <ul class="list-unstyled mb-0" style="font-size:.72rem;">
                                    @forelse($highestRisk->take(5) as $rate)
                                        <li class="d-flex justify-content-between py-1.5 border-bottom border-secondary border-opacity-10">
                                            <span style="color:var(--text);">{{ $rate->country->name }} ({{ $rate->target_currency }})</span>
                                            <span class="fw-bold" style="color:#ef4444;">Score: {{ number_format($rate->currency_risk_score, 1) }}</span>
                                        </li>
                                    @empty
                                        <li class="text-muted text-center py-3">No currency risk data</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filter & Sort Bar --}}
<form method="GET" class="filter-bar mb-4">
    <div class="row g-2 align-items-end">

        <div class="col-md-3">
            <label class="form-label fw-semibold">Search Country</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text"
                       name="search"
                       class="form-control"
                       placeholder="Country name..."
                       value="{{ request('search') }}">
                @if(request('search'))
                    <a href="{{ request()->url() }}" class="input-group-text" style="cursor:pointer;color:var(--text-muted);">
                        <i class="bi bi-x"></i>
                    </a>
                @endif
            </div>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-semibold">Filter by Region</label>
            <select name="region" class="form-select">
                <option value="">All Regions</option>
                @foreach($regions as $region)
                    <option value="{{ $region }}" @selected(request('region') == $region)>
                        {{ $region }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label fw-semibold">Currency Code</label>
            <select name="currency_code" class="form-select">
                <option value="">All Currencies</option>
                @foreach($currencyCodes as $code)
                    <option value="{{ $code }}" @selected(request('currency_code') == $code)>
                        {{ $code }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label fw-semibold">Sort by</label>
            <select name="sort" class="form-select">
                <option value="">Default (Country Name)</option>
                <option value="rate_asc" @selected(request('sort') == 'rate_asc')>Rate: Low to High</option>
                <option value="rate_desc" @selected(request('sort') == 'rate_desc')>Rate: High to Low</option>
                <option value="risk_desc" @selected(request('sort') == 'risk_desc')>Risk: High to Low</option>
                <option value="risk_asc" @selected(request('sort') == 'risk_asc')>Risk: Low to High</option>
            </select>
        </div>

        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-fill">
                <i class="bi bi-funnel-fill"></i> Apply
            </button>
            @if(request()->hasAny(['search','region','currency_code','sort']))
                <a href="{{ route('currency.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </div>

    </div>
</form>

{{-- Currency Table --}}
<div class="card">
    <div class="card-header">
        <div class="section-icon" style="background:rgba(22,163,74,.08);color:#16A34A;">
            <i class="bi bi-currency-exchange"></i>
        </div>
        <span class="fw-bold" style="font-size:.9rem;">Exchange Rate Feeds</span>
        <span class="ms-2 badge" style="background:rgba(22,163,74,.1);color:#16A34A;font-size:.68rem;font-weight:700;border-radius:99px;padding:.25rem .6rem;">
            {{ $countries->total() }} countries
        </span>
        @if(request()->hasAny(['search','region','currency_code','sort']))
            <span class="ms-1 badge" style="background:rgba(22,163,74,.1);color:#16A34A;font-size:.68rem;font-weight:700;border-radius:99px;padding:.25rem .6rem;">
                <i class="bi bi-funnel-fill me-1"></i>Filtered
            </span>
        @endif
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-enterprise mb-0">
                <thead>
                    <tr>
                        <th>Country</th>
                        <th>Currency Code</th>
                        <th>Exchange Rate (vs USD)</th>
                        <th>Change %</th>
                        <th>Currency Risk Score</th>
                        <th>Last Updated</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($countries as $country)
                        @php
                            $rate = $country->latestCurrency;
                        @endphp
                        <tr>
                            <td style="vertical-align:middle;">
                                <div class="d-flex align-items-center gap-2">
                                    <div style="font-size:1.1rem;">🌍</div>
                                    <div>
                                        <a href="{{ route('countries.show', $country) }}" style="font-weight:700;color:var(--text);text-decoration:none;" class="hover-link">
                                            {{ $country->name }}
                                        </a>
                                        <div style="font-size:.7rem;color:var(--text-subtle);">{{ $country->region }} · {{ $country->iso3 }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="vertical-align:middle;">
                                <span class="badge" style="background:var(--primary-50);color:var(--primary-light);font-size:.78rem;font-weight:700;padding:.3rem .6rem;border-radius:var(--radius-sm);">
                                    {{ $country->currency_code ?? '—' }}
                                </span>
                            </td>
                            <td style="font-weight:700;font-size:.85rem;font-family:monospace;vertical-align:middle;">
                                @if($rate)
                                    {{ number_format($rate->exchange_rate, 4) }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td style="vertical-align:middle;">
                                @if($rate)
                                    @if($rate->change_percentage === null)
                                        <span class="text-muted" style="font-weight:600;font-size:.78rem;">▬ N/A</span>
                                    @elseif($rate->change_percentage > 0)
                                        <span style="color:#16A34A;font-weight:700;font-size:.78rem;">
                                            ▲ +{{ number_format($rate->change_percentage, 4) }}%
                                        </span>
                                    @elseif($rate->change_percentage < 0)
                                        <span style="color:#DC2626;font-weight:700;font-size:.78rem;">
                                            ▼ {{ number_format($rate->change_percentage, 4) }}%
                                        </span>
                                    @else
                                        <span style="color:#94a3b8;font-weight:700;font-size:.78rem;">
                                            ▬ 0.0000%
                                        </span>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td style="vertical-align:middle;">
                                @if($rate)
                                    @php 
                                        $rs = $rate->currency_risk_score ?? 0.0; 
                                        $level = 'LOW';
                                        $badgeClass = 'badge-risk-low';
                                        $style = '';
                                        
                                        if ($rs >= 76) {
                                            $level = 'CRITICAL';
                                            $style = 'background:#7F1D1D;color:#FEE2E2;';
                                        } elseif ($rs >= 51) {
                                            $level = 'HIGH';
                                            $badgeClass = 'badge-risk-high';
                                        } elseif ($rs >= 26) {
                                            $level = 'MEDIUM';
                                            $badgeClass = 'badge-risk-medium';
                                        }
                                    @endphp
                                    <span class="badge {{ $style ? '' : $badgeClass }}" 
                                          style="border-radius:99px;font-size:.68rem;padding:.25rem .55rem;{{ $style }}"
                                          title="Volatility, inflation, economics and sentiment risk factors.">
                                        {{ $level }} ({{ number_format($rs, 1) }})
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td style="font-size:.72rem;color:var(--text-subtle);vertical-align:middle;">
                                @if($rate && $rate->rate_time)
                                    {{ \Carbon\Carbon::parse($rate->rate_time)->diffForHumans() }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-5 text-center text-muted" style="font-size:.82rem;">
                                <i class="bi bi-currency-dollar d-block mb-2" style="font-size:2rem;opacity:.3;"></i>
                                <strong>Unavailable</strong>
                                <p class="mb-0 text-subtle" style="font-size:.72rem;">No currency feeds are currently available for selection.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Pagination Links --}}
<div class="mt-4 d-flex justify-content-center">
    {{ $countries->links('pagination::bootstrap-5') }}
</div>

@endsection