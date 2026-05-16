<?php
/**
 * Inline SVG icons (no external assets). Use echo icon_*() in PHP templates.
 * All return safe HTML strings.
 */
function icon_hospital_building(int $w = 48, int $h = 48): string
{
    return '<svg class="svg-icon" width="' . $w . '" height="' . $h . '" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-4h6v4M9 13h2M13 13h2M9 9h2M13 9h2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>';
}

function icon_mobile_phone(int $w = 48, int $h = 48): string
{
    return '<svg class="svg-icon" width="' . $w . '" height="' . $h . '" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><rect x="7" y="2" width="10" height="20" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M10 18h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>';
}

function icon_handshake(int $w = 48, int $h = 48): string
{
    return '<svg class="svg-icon" width="' . $w . '" height="' . $h . '" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M11 12h2a2 2 0 012 2v1l2 2M7 12H5a2 2 0 00-2 2v1l2 2M8 18l-1-1M16 18l1-1M12 8V6M9 8V5M15 8V5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 12c1-2 3-3 4-3s3 1 4 3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>';
}

function icon_check_circle(int $w = 24, int $h = 24): string
{
    return '<svg class="svg-icon" width="' . $w . '" height="' . $h . '" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M9 12l2 2 4-4M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>';
}

function icon_test_tube(int $w = 48, int $h = 48): string
{
    return '<svg class="svg-icon" width="' . $w . '" height="' . $h . '" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M9 2h6M10 2v5l-4 9a3 3 0 002.7 4.3h6.6A3 3 0 0018 16l-4-9V2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>';
}

function icon_syringe(int $w = 48, int $h = 48): string
{
    return '<svg class="svg-icon" width="' . $w . '" height="' . $h . '" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M18 3l3 3-9 9M14 7l4 4M6 18l-3 3M8 16l-2-2M10 14L8 12M12 12l2 2M5 11l8-8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';
}

function icon_building_small(int $w = 48, int $h = 48): string
{
    return icon_hospital_building($w, $h);
}

function icon_soap_hands(int $w = 28, int $h = 28): string
{
    return '<svg class="svg-icon" width="' . $w . '" height="' . $h . '" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M8 11V8a2 2 0 012-2h4a2 2 0 012 2v3M6 11h12v8a2 2 0 01-2 2H8a2 2 0 01-2-2v-8zM10 15h4M10 18h4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>';
}

function icon_mask(int $w = 28, int $h = 28): string
{
    return '<svg class="svg-icon" width="' . $w . '" height="' . $h . '" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M4 10c2 2 5 3 8 3s6-1 8-3v6c-2 2-5 3-8 3s-6-1-8-3V10z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M9 13h6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>';
}

function icon_distance(int $w = 28, int $h = 28): string
{
    return '<svg class="svg-icon" width="' . $w . '" height="' . $h . '" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M4 12h4M16 12h4M10 12h4M8 8l-2 8M16 8l2 8" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>';
}

function icon_clock(int $w = 56, int $h = 56): string
{
    return '<svg class="svg-icon" width="' . $w . '" height="' . $h . '" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/><path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>';
}

function icon_calendar(int $w = 56, int $h = 56): string
{
    return '<svg class="svg-icon" width="' . $w . '" height="' . $h . '" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M16 3v4M8 3v4M3 11h18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>';
}

function icon_clipboard_ok(int $w = 56, int $h = 56): string
{
    return '<svg class="svg-icon" width="' . $w . '" height="' . $h . '" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2M9 5a2 2 0 002 2h2a2 2 0 002-2m-6 9l2 2 4-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';
}

function icon_folder(int $w = 48, int $h = 48): string
{
    return '<svg class="svg-icon" width="' . $w . '" height="' . $h . '" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>';
}

function icon_users(int $w = 48, int $h = 48): string
{
    return '<svg class="svg-icon" width="' . $w . '" height="' . $h . '" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';
}

function icon_shield_admin(int $w = 48, int $h = 48): string
{
    return '<svg class="svg-icon" width="' . $w . '" height="' . $h . '" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M12 3l8 4v6c0 5-3.5 9-8 10-4.5-1-8-5-8-10V7l8-4zM9 12l2 2 4-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';
}

function icon_stethoscope(int $w = 40, int $h = 40): string
{
    return '<svg class="svg-icon" width="' . $w . '" height="' . $h . '" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M6 4v6a6 6 0 0012 0V4M6 4H4M18 4h2M8 21h8M12 17v4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>';
}

function icon_gear(int $w = 40, int $h = 40): string
{
    return '<svg class="svg-icon" width="' . $w . '" height="' . $h . '" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M12 15a3 3 0 100-6 3 3 0 000 6zM19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 01-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.6 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.6a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9c.09.31.13.65.13 1s-.04.69-.13 1a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
}

function icon_star_rating(int $n, string $class = ''): string
{
    $out = '<span class="star-rating ' . htmlspecialchars($class, ENT_QUOTES, 'UTF-8') . '" aria-hidden="true">';
    for ($i = 1; $i <= 5; $i++) {
        $fill = $i <= $n ? 'currentColor' : 'none';
        $stroke = 'currentColor';
        $out .= '<svg width="18" height="18" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 2l3 7h7l-5.5 4 2 7L12 17l-6.5 4 2-7L5 9h7l3-7z" fill="' . $fill . '" stroke="' . $stroke . '" stroke-width="1" stroke-linejoin="round"/></svg>';
    }
    $out .= '</span>';
    return $out;
}
