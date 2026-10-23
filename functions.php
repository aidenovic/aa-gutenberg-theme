<?php
/** AA Gutenberg Theme bootstrap. GPL-2.0-or-later; see NOTICE.md. */
defined('ABSPATH') || exit;
define('AAGT_VERSION', '1.1.1');
define('AAGT_PATH', get_template_directory());
// Gemeinsame Theme-Module zentral laden.
foreach (array('i18n', 'content', 'design', 'native-form', 'forms', 'setup', 'quality') as $module) {
    require_once AAGT_PATH . '/inc/' . $module . '.php';
}
add_action('after_setup_theme', function () {
    load_theme_textdomain('aa-gutenberg-theme', AAGT_PATH . '/languages');
    foreach (array('wp-block-styles', 'editor-styles', 'responsive-embeds', 'post-thumbnails') as $support) { add_theme_support($support); }
    add_theme_support('custom-logo', array('flex-width' => true, 'flex-height' => true));
    add_editor_style(array('assets/css/tokens.css', 'assets/css/layout.css', 'assets/css/forms.css'));
});
add_action('init', function () {
    register_block_pattern_category('aagt', array('label' => __('AA Gutenberg Theme', 'aa-gutenberg-theme')));
    register_block_type(AAGT_PATH . '/blocks/contact-form', array('render_callback' => 'aagt_render_form'));
});
add_action('enqueue_block_assets', function () {
    wp_enqueue_style('aagt-layout', get_template_directory_uri() . '/assets/css/layout.css', array('aagt-tokens', 'aagt-global-design'), AAGT_VERSION);
    wp_enqueue_style('aagt-forms', get_template_directory_uri() . '/assets/css/forms.css', array('aagt-layout'), AAGT_VERSION);
}, 101);
