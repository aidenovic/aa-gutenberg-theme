<?php
/** AA adaptation modified 2026-10-19; derived from ZW Gutenberg Theme by ZEITWERK. GPL-2.0-or-later; see NOTICE.md and LICENSE.txt. */
/** Connect the theme's design tokens to WordPress Global Styles and Site Logo. */
defined('ABSPATH') || exit;

function aagt_style_value($value, $property, $fallback) {
    if (!is_string($value) || $value === '' || preg_match('/[{};<>]/', $value)) { return $fallback; }
    $value = preg_replace('/var:preset\|([a-z0-9-]+)\|([a-z0-9-]+)/i', 'var(--wp--preset--$1--$2)', $value);
    $safe = safecss_filter_attr($property . ':' . $value);
    return strpos($safe, $property . ':') === 0 ? trim(substr($safe, strlen($property) + 1)) : $fallback;
}

function aagt_button_design_vars($styles) {
    $button = array_replace_recursive($styles['elements']['button'] ?? array(), $styles['blocks']['core/button'] ?? array());
    $radius = $button['border']['radius'] ?? '';
    if (is_array($radius)) {
        $corners = array();
        foreach (array('topLeft', 'topRight', 'bottomRight', 'bottomLeft') as $corner) {
            $corners[] = aagt_style_value($radius[$corner] ?? '', 'border-radius', '0');
        }
        $radius = implode(' ', $corners);
    }
    $vars = array(
        '--aa-button-background' => aagt_style_value($button['color']['background'] ?? '', 'background-color', 'var(--aa-primary)'),
        '--aa-button-color' => aagt_style_value($button['color']['text'] ?? '', 'color', 'var(--aa-white)'),
        '--aa-button-radius' => aagt_style_value($radius, 'border-radius', 'var(--wp--custom--aa--button-radius, 0)'),
        '--aa-button-weight' => aagt_style_value($button['typography']['fontWeight'] ?? '', 'font-weight', '400'),
        '--aa-button-line-height' => aagt_style_value($button['typography']['lineHeight'] ?? '', 'line-height', '1.2'),
        '--aa-button-letter-spacing' => aagt_style_value($button['typography']['letterSpacing'] ?? '', 'letter-spacing', 'var(--aa-button-tracking)'),
        '--aa-button-text-transform' => aagt_style_value($button['typography']['textTransform'] ?? '', 'text-transform', 'uppercase'),
    );
    $padding = $button['spacing']['padding'] ?? array();
    foreach (array('top' => '15px', 'right' => '25px', 'bottom' => '15px', 'left' => '25px') as $side => $fallback) {
        $vars['--aa-button-padding-' . $side] = aagt_style_value(is_string($padding) ? $padding : ($padding[$side] ?? ''), 'padding', $fallback);
    }
    return $vars;
}

function aagt_design_css() {
    // Read the merged theme + child theme + saved user styles, rather than a fixed font.
    $styles = wp_get_global_styles();
    $theme = WP_Theme_JSON_Resolver::get_theme_data()->get_raw_data()['styles'] ?? array();
    $user = WP_Theme_JSON_Resolver::get_user_data()->get_raw_data()['styles'] ?? array();
    $body = $styles['typography'] ?? array();
    $heading = $styles['elements']['heading']['typography'] ?? array();
    $heading = array_merge($heading, $styles['blocks']['core/heading']['typography'] ?? array());
    $user_heading = $user['elements']['heading']['typography'] ?? array();
    $user_heading = array_merge($user_heading, $user['blocks']['core/heading']['typography'] ?? array());
    $user_h1 = array_merge($theme['elements']['h1']['typography'] ?? array(), $user['elements']['h1']['typography'] ?? array());
    $user_h2 = array_merge($theme['elements']['h2']['typography'] ?? array(), $user['elements']['h2']['typography'] ?? array());
    $button = array_merge($styles['elements']['button']['typography'] ?? array(), $styles['blocks']['core/button']['typography'] ?? array());
    $vars = array(
        '--aa-font-family' => aagt_style_value($body['fontFamily'] ?? '', 'font-family', 'var(--wp--preset--font-family--system)'),
        '--aa-heading-font-family' => aagt_style_value($user_h2['fontFamily'] ?? $heading['fontFamily'] ?? $body['fontFamily'] ?? '', 'font-family', 'var(--aa-font-family)'),
        '--aa-display-font-family' => aagt_style_value($user_h1['fontFamily'] ?? $heading['fontFamily'] ?? '', 'font-family', 'var(--aa-heading-font-family)'),
        '--aa-button-font-family' => aagt_style_value($button['fontFamily'] ?? $body['fontFamily'] ?? '', 'font-family', 'var(--aa-font-family)'),
        '--aa-button-size' => aagt_style_value($button['fontSize'] ?? '', 'font-size', 'var(--aa-small-size)'),
        '--aa-navigation-size' => aagt_style_value($styles['blocks']['core/navigation']['typography']['fontSize'] ?? '', 'font-size', 'var(--aa-small-size)'),
        '--aa-body-size' => aagt_style_value($body['fontSize'] ?? '', 'font-size', 'var(--wp--preset--font-size--body)'),
        '--aa-heading-size' => aagt_style_value($user_h2['fontSize'] ?? $user_heading['fontSize'] ?? '', 'font-size', 'var(--wp--preset--font-size--heading)'),
        '--aa-display-size' => aagt_style_value($user_h1['fontSize'] ?? $user_heading['fontSize'] ?? '', 'font-size', 'var(--wp--preset--font-size--display)'),
        '--aa-feature-size' => aagt_style_value($user_h2['fontSize'] ?? $user_heading['fontSize'] ?? '', 'font-size', 'var(--wp--preset--font-size--feature)'),
        '--aa-large-heading-size' => aagt_style_value($user_h2['fontSize'] ?? $user_heading['fontSize'] ?? '', 'font-size', 'var(--wp--preset--font-size--display)'),
        '--aa-body-line-height' => aagt_style_value($body['lineHeight'] ?? '', 'line-height', '1.3158'),
        '--aa-heading-line-height' => aagt_style_value($heading['lineHeight'] ?? '', 'line-height', '1.14286'),
        '--aa-display-line-height' => aagt_style_value($user_h1['lineHeight'] ?? $user_heading['lineHeight'] ?? '', 'line-height', '1'),
        '--aa-heading-weight' => aagt_style_value($heading['fontWeight'] ?? '', 'font-weight', '700'),
    );
    // Core's generic gray button defaults must not replace this theme's palette.
    // Only explicit parent/child definitions and saved user overrides change it.
    $vars = array_merge($vars, aagt_button_design_vars(array_replace_recursive($theme, $user)));
    $css = ':root:root,.editor-styles-wrapper.editor-styles-wrapper{';
    foreach ($vars as $name => $value) { $css .= $name . ':' . $value . ';'; }
    return $css . '}';
}

// enqueue_block_assets also runs inside the editor canvas on supported WordPress versions.
add_action('enqueue_block_assets', function () {
    wp_enqueue_style('aagt-tokens', get_template_directory_uri() . '/assets/css/tokens.css', array(), AAGT_VERSION);
    wp_register_style('aagt-global-design', false, array('aagt-tokens'), AAGT_VERSION);
    wp_enqueue_style('aagt-global-design');
    wp_add_inline_style('aagt-global-design', aagt_design_css());
}, 100);

function aagt_enqueue_editor_design() {
    $post = WP_Theme_JSON_Resolver::get_user_data_from_wp_global_styles(wp_get_theme(), false);
    $config = array(
        'id' => (int) ($post['ID'] ?? 0),
        'language' => get_bloginfo('language'),
        'defaults' => wp_get_global_styles(array(), array('origin' => 'base')),
        'theme' => WP_Theme_JSON_Resolver::get_theme_data()->get_raw_data()['styles'] ?? array(),
    );
    wp_enqueue_script('aagt-editor-design', get_template_directory_uri() . '/assets/js/editor-design.js', array('wp-data', 'wp-core-data'), AAGT_VERSION, true);
    wp_add_inline_script('aagt-editor-design', 'window.aagtDesign = ' . wp_json_encode($config, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';', 'before');
}
add_action('enqueue_block_editor_assets', 'aagt_enqueue_editor_design');

