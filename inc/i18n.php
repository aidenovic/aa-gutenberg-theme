<?php
/** AA adaptation modified 2026-10-19; derived from ZW Gutenberg Theme by ZEITWERK. GPL-2.0-or-later; see NOTICE.md and LICENSE.txt. */
/** Theme interface translation and optional language-plugin integration. */
defined('ABSPATH') || exit;

function aagt_home_url($anchor = '') {
    $url = home_url('/');
    if (function_exists('pll_home_url')) {
        $url = pll_home_url();
    } elseif (defined('ICL_SITEPRESS_VERSION')) {
        $url = apply_filters('wpml_home_url', $url);
    }
    return $anchor !== '' ? $url . '#' . rawurlencode($anchor) : $url;
}

function aagt_language_switcher() {
    // Plugin language lists rely on a frontend query; do not call them from
    // the editor/REST preview or before WordPress has resolved that query.
    if (is_admin() || !did_action('wp')) { return ''; }
    $languages = array();
    if (function_exists('pll_the_languages')) {
        $languages = pll_the_languages(array('raw' => 1, 'hide_if_no_translation' => 1));
    } elseif (defined('ICL_SITEPRESS_VERSION')) {
        $languages = apply_filters('wpml_active_languages', null, array('skip_missing' => 1));
    }
    if (!is_array($languages) || count($languages) < 2) { return ''; }
    $html = '<nav class="aa-language-switcher" aria-label="' . esc_attr__('Sprache wählen', 'aa-gutenberg-theme') . '"><ul>';
    foreach ($languages as $language) {
        if (empty($language['url'])) { continue; }
        $active = !empty($language['current_lang']) || !empty($language['active']);
        $code = $language['locale'] ?? $language['default_locale'] ?? $language['language_code'] ?? $language['slug'] ?? '';
        $label = $language['native_name'] ?? $language['name'] ?? $code;
        $html .= '<li><a href="' . esc_url($language['url']) . '" hreflang="' . esc_attr(str_replace('_', '-', $code)) . '" lang="' . esc_attr(str_replace('_', '-', $code)) . '"' . ($active ? ' aria-current="true"' : '') . '>' . esc_html($label) . '</a></li>';
    }
    return $html . '</ul></nav>';
}
add_shortcode('aa_language_switcher', 'aagt_language_switcher');

// WordPress core/Gutenberg owns language attributes and RTL classes. Additional
// plugins own translated pages, language switching and translated form content.
add_action('init', function () {
    wp_set_script_translations('aagt-contact-form-editor-script', 'aa-gutenberg-theme', AAGT_PATH . '/languages');
}, 20);
