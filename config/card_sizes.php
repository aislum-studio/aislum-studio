<?php
/**
 * Card Sizes Configuration
 *
 * All dimensions are in millimetres (mm).
 * Bleed is the extra margin added on each side for print-ready exports.
 * Canvas dimensions are the pixel equivalents used by the editor at 96dpi screen res.
 * Export dimensions are at 300dpi for print quality.
 */

define('CARD_SIZES', [

    'standard_us' => [
        'label'          => 'Standard US (3.5" × 2")',
        'width_mm'       => 88.9,
        'height_mm'      => 50.8,
        'bleed_mm'       => 3,
        'canvas_w'       => 600,   // editor preview px (maintains aspect ratio)
        'canvas_h'       => 343,
        'export_w_px'    => 1050,  // 3.5" × 300dpi
        'export_h_px'    => 600,   // 2"   × 300dpi
        'print_w_px'     => 1086,  // with 3mm bleed each side ≈ +36px
        'print_h_px'     => 636,
    ],

    'european' => [
        'label'          => 'European (85mm × 55mm)',
        'width_mm'       => 85,
        'height_mm'      => 55,
        'bleed_mm'       => 3,
        'canvas_w'       => 600,
        'canvas_h'       => 388,
        'export_w_px'    => 1004,  // 85mm × 300dpi / 25.4
        'export_h_px'    => 650,
        'print_w_px'     => 1039,
        'print_h_px'     => 685,
    ],

    'square' => [
        'label'          => 'Square (2.5" × 2.5")',
        'width_mm'       => 63.5,
        'height_mm'      => 63.5,
        'bleed_mm'       => 3,
        'canvas_w'       => 400,
        'canvas_h'       => 400,
        'export_w_px'    => 750,   // 2.5" × 300dpi
        'export_h_px'    => 750,
        'print_w_px'     => 786,
        'print_h_px'     => 786,
    ],

    'mini' => [
        'label'          => 'Mini (2.75" × 1.75")',
        'width_mm'       => 69.85,
        'height_mm'      => 44.45,
        'bleed_mm'       => 3,
        'canvas_w'       => 550,
        'canvas_h'       => 350,
        'export_w_px'    => 825,
        'export_h_px'    => 525,
        'print_w_px'     => 861,
        'print_h_px'     => 561,
    ],

]);

/**
 * Returns a single size config by key, or null if not found.
 *
 * @param string $key  e.g. 'standard_us'
 * @return array|null
 */
function getCardSize($key) {
    return CARD_SIZES[$key] ?? null;
}

/**
 * Returns all size keys and their labels for dropdowns.
 *
 * @return array  ['standard_us' => 'Standard US (3.5" × 2")', ...]
 */
function getCardSizeOptions() {
    return array_map(fn($s) => $s['label'], CARD_SIZES);
}
