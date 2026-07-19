@props([
    'country' => null,
    'size' => 'sm', // sm, md, lg
    'class' => ''
])

@php
    $flagUrl = null;
    $countryName = 'Country';

    if ($country) {
        if (is_array($country)) {
            $flagUrl = $country['flag_png'] ?? $country['flag_svg'] ?? null;
            $countryName = $country['name'] ?? 'Country';
            $iso2 = $country['iso2'] ?? null;
            if (!$flagUrl && $iso2) {
                $code = strtolower($iso2);
                $flagUrl = "https://flagcdn.com/w320/{$code}.png";
            }
        } elseif (is_object($country)) {
            $flagUrl = $country->flag_png ?? $country->flag_svg ?? null;
            $countryName = $country->name ?? 'Country';
            $iso2 = $country->iso2 ?? null;
            if (!$flagUrl && $iso2) {
                $code = strtolower($iso2);
                $flagUrl = "https://flagcdn.com/w320/{$code}.png";
            }
        }
    }

    // Default placeholder if no flag URL is resolved
    if (!$flagUrl) {
        $flagUrl = "https://flagcdn.com/w320/un.png";
    }

    // Determine dimensions based on size
    $dims = match ($size) {
        'lg' => ['width' => '48px', 'height' => '32px'],
        'md' => ['width' => '32px', 'height' => '22px'],
        default => ['width' => '24px', 'height' => '16px'],
    };
@endphp

<img src="{{ $flagUrl }}" 
     alt="{{ $countryName }} Flag" 
     class="country-flag-img {{ $class }}" 
     style="width: {{ $dims['width'] }}; height: {{ $dims['height'] }}; object-fit: cover; border-radius: 3px; border: 1px solid rgba(0,0,0,0.1); display: inline-block; vertical-align: middle; margin-right: 8px;"
     loading="lazy">
