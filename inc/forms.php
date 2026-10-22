<?php
/** Optional native provider only; external plugins own their blocks/shortcodes. */
defined('ABSPATH') || exit;
function aagt_render_form($attributes) {
    // Removed/unknown provider values remain inert; never trigger fallback mail.
    return ($attributes['provider'] ?? 'none') === 'native' ? aagt_render_native_form() : '';
}
