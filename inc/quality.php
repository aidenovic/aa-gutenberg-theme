<?php
/** AA adaptation modified 2026-10-19; derived from ZW Gutenberg Theme by ZEITWERK. GPL-2.0-or-later; see NOTICE.md and LICENSE.txt. */
/** Optional presentation enhancements and plugin-neutral SEO fallback. */
defined('ABSPATH') || exit;

function aagt_sanitize_switch($value) {
    return in_array($value, array(true, 1, '1'), true) ? 1 : 0;
}

function aagt_animations_enabled() {
    return aagt_sanitize_switch(get_option('aagt_animations_enabled', 0)) === 1;
}

function aagt_register_quality_settings() {
    foreach (array('aagt_animations_enabled' => 0, 'aagt_seo_fallback' => 1) as $key => $default) {
        register_setting('aagt_quality_settings', $key, array('type' => 'integer', 'sanitize_callback' => 'aagt_sanitize_switch', 'default' => $default));
    }
}
add_action('admin_init', 'aagt_register_quality_settings');

add_action('admin_menu', function () {
    add_submenu_page('aagt-setup', __('Theme-Einstellungen', 'aa-gutenberg-theme'), __('Einstellungen', 'aa-gutenberg-theme'), 'manage_options', 'aagt-settings', 'aagt_quality_screen');
});

function aagt_quality_screen() {
    if (!current_user_can('manage_options')) { return; }
    echo '<div class="wrap"><h1>' . esc_html__('Theme-Einstellungen', 'aa-gutenberg-theme') . '</h1><form method="post" action="' . esc_url(admin_url('options.php')) . '">';
    settings_fields('aagt_quality_settings');
    echo '<h2>' . esc_html__('Animationen', 'aa-gutenberg-theme') . '</h2><p><input type="hidden" name="aagt_animations_enabled" value="0"><label><input type="checkbox" name="aagt_animations_enabled" value="1"' . checked(aagt_animations_enabled(), true, false) . '> ' . esc_html__('Dezente Scroll-Animationen aktivieren', 'aa-gutenberg-theme') . '</label></p>';
    echo '<p class="description">' . esc_html__('Standardmäßig aus. Einmaliges weiches Einblenden von unten beim Scrollen; die Browser-Einstellung „Bewegung reduzieren“ hat Vorrang. Inhalte bleiben ohne JavaScript sichtbar. Farben und Layout werden nicht verändert.', 'aa-gutenberg-theme') . '</p>';
    echo '<h2>' . esc_html__('Suchmaschinen', 'aa-gutenberg-theme') . '</h2><p><input type="hidden" name="aagt_seo_fallback" value="0"><label><input type="checkbox" name="aagt_seo_fallback" value="1"' . checked(aagt_sanitize_switch(get_option('aagt_seo_fallback', 1)), 1, false) . '> ' . esc_html__('Meta-Beschreibung ohne SEO-Plugin ausgeben', 'aa-gutenberg-theme') . '</label></p>';
    echo '<p class="description">' . esc_html__('Den Seitenauszug als Beschreibung pflegen. Ohne Auszug wird ein kurzer Text aus dem Inhalt verwendet. Erkannte SEO-Plugins haben Vorrang; Titel, Canonical, Robots und Sitemap bleiben bei WordPress beziehungsweise dem Plugin.', 'aa-gutenberg-theme') . '</p>';
    if (aagt_has_seo_plugin()) { echo '<p>' . esc_html__('Ein SEO-Plugin wurde erkannt. Das Theme gibt keine zusätzliche Meta-Beschreibung aus.', 'aa-gutenberg-theme') . '</p>'; }
    submit_button(__('Einstellungen speichern', 'aa-gutenberg-theme'));
    echo '</form><hr><h2>' . esc_html__('Barrierefreiheit und Datenschutz', 'aa-gutenberg-theme') . '</h2><p>' . esc_html__('Tastaturbedienung und reduzierte Bewegung werden unterstützt. Farben, eigene Inhalte und eingebundene Plugins müssen je Projekt geprüft werden. Ein Theme allein bestätigt keine vollständige Barrierefreiheit oder DSGVO-Konformität.', 'aa-gutenberg-theme') . '</p>';
    if (!get_option('blog_public')) { echo '<p>' . esc_html__('Suchmaschinen-Indexierung ist in WordPress derzeit deaktiviert. Für eine Entwicklungsseite ist das vorgesehen; vor einem öffentlichen Start prüfen.', 'aa-gutenberg-theme') . '</p>'; }
    if (!get_privacy_policy_url()) { echo '<p>' . esc_html__('Es ist keine veröffentlichte WordPress-Datenschutzerklärung zugeordnet. Unter Einstellungen → Datenschutz die passende Seite prüfen.', 'aa-gutenberg-theme') . '</p>'; }
    echo '<p>' . esc_html__('Datenschutz- und Impressumslinks, Formularspeicherung, Mailversand, Serverlogs und Löschfristen mit dem Betreiber abstimmen. Die Datei QUALITY.md beschreibt die Prüfung und Grenzen.', 'aa-gutenberg-theme') . '</p></div>';
}

function aagt_has_seo_plugin() {
    $active = defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION') || defined('AIOSEO_VERSION') || defined('SEOPRESS_VERSION') || defined('THE_SEO_FRAMEWORK_VERSION');
    return (bool) apply_filters('aagt_has_seo_plugin', $active);
}

function aagt_meta_description() {
    if (!aagt_sanitize_switch(get_option('aagt_seo_fallback', 1)) || aagt_has_seo_plugin() || !is_singular()) { return ''; }
    $post = get_queried_object();
    if (!$post instanceof WP_Post || post_password_required($post)) { return ''; }
    $text = trim($post->post_excerpt) !== '' ? $post->post_excerpt : strip_shortcodes($post->post_content);
    $text = preg_replace('/\s+/u', ' ', wp_strip_all_tags($text));
    return wp_html_excerpt(trim((string) $text), 160, '…');
}

function aagt_render_meta_description() {
    $description = aagt_meta_description();
    if ($description !== '') { echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n"; }
}
add_action('wp_head', 'aagt_render_meta_description', 20);

add_action('init', function () { add_post_type_support('page', 'excerpt'); });

add_action('wp_enqueue_scripts', function () {
    if (!aagt_animations_enabled()) { return; }
    wp_enqueue_script('aagt-motion', get_template_directory_uri() . '/assets/js/motion.js', array(), AAGT_VERSION, array('in_footer' => true, 'strategy' => 'defer'));
});

// Discover local regular/bold faces from the effective theme settings early in head.
// This keeps project font overrides intact and avoids third-party font requests.
add_filter('wp_preload_resources', function ($resources) {
    foreach (wp_get_global_settings(array('typography', 'fontFamilies')) as $families) {
        foreach ($families as $family) {
            foreach ($family['fontFace'] ?? array() as $face) {
                if (($face['fontStyle'] ?? 'normal') !== 'normal') { continue; }
                foreach ((array) ($face['src'] ?? array()) as $src) {
                    if (!is_string($src) || strpos($src, 'file:./') !== 0) { continue; }
                    $path = substr($src, 7);
                    if (strpos($path, '..') !== false) { continue; }
                    $extension = pathinfo($path, PATHINFO_EXTENSION);
                    $types = array('ttf' => 'font/ttf', 'woff2' => 'font/woff2', 'woff' => 'font/woff');
                    if (!isset($types[$extension])) { continue; }
                    $resources[] = array('href' => get_theme_file_uri($path), 'as' => 'font', 'type' => $types[$extension], 'crossorigin' => 'anonymous');
                    break;
                }
            }
        }
    }
    return $resources;
});
