<div class="stat-card fade-in-up">

    <div class="stat-icon"
         style="{{ isset($color) ? 'background:' . $color . '18;color:' . $color . ';' : '' }}">
        <i class="{{ $icon ?? 'bi bi-bar-chart' }}" style="font-size:1.3rem;"></i>
    </div>

    <div class="stat-content" style="flex:1;min-width:0;">
        <small>{{ $title }}</small>
        <h3 style="{{ isset($color) ? 'color:' . $color . ';' : '' }}">{{ $value }}</h3>
        @if(isset($subtitle))
            <div style="font-size:.7rem;color:var(--text-subtle);margin-top:2px;">{{ $subtitle }}</div>
        @endif
    </div>

</div>