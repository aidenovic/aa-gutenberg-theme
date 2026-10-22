<?php
/** Explicit, repeatable starter setup. Activation creates no content. */
defined('ABSPATH') || exit;
add_action('admin_menu', function () {
    add_menu_page(__('AA Gutenberg Theme', 'aa-gutenberg-theme'), __('AA Gutenberg Theme', 'aa-gutenberg-theme'), 'manage_options', 'aagt-setup', 'aagt_setup_screen', 'dashicons-layout', 59);
    add_submenu_page('aagt-setup', __('Einrichten', 'aa-gutenberg-theme'), __('Einrichten', 'aa-gutenberg-theme'), 'manage_options', 'aagt-setup', 'aagt_setup_screen');
    add_submenu_page('aagt-setup', __('Anleitung', 'aa-gutenberg-theme'), __('Anleitung', 'aa-gutenberg-theme'), 'manage_options', 'aagt-help', 'aagt_help_screen');
});
add_action('admin_init', function () {
    register_setting('aagt_contact_settings', 'aagt_contact_email', array('type' => 'string', 'sanitize_callback' => 'sanitize_email', 'default' => ''));
    register_setting('aagt_contact_settings', 'aagt_contact_enabled', array('type' => 'integer', 'sanitize_callback' => 'aagt_sanitize_switch', 'default' => 0));
});
function aagt_create_starter_page() {
    $id = absint(get_option('aagt_starter_page_id', 0));
    if ($id && get_post_type($id) === 'page') { return $id; }
    $id = wp_insert_post(wp_slash(array('post_title' => __('Meine Startseite', 'aa-gutenberg-theme'), 'post_status' => 'draft', 'post_type' => 'page', 'post_content' => aagt_starter_content())), true);
    if (!is_wp_error($id) && $id) { update_option('aagt_starter_page_id', $id, false); }
    return $id;
}
add_action('admin_post_aagt_setup', function () {
    if (!current_user_can('manage_options')) { wp_die(esc_html__('Keine Berechtigung.', 'aa-gutenberg-theme'), '', array('response' => 403)); }
    check_admin_referer('aagt_setup');
    $id = aagt_create_starter_page();
    if (is_wp_error($id)) { wp_die(esc_html($id->get_error_message())); }
    wp_safe_redirect(get_edit_post_link($id, 'raw'));
    exit;
});
function aagt_setup_screen() {
    if (!current_user_can('manage_options')) { return; }
    echo '<div class="wrap"><h1>' . esc_html__('AA Gutenberg Theme', 'aa-gutenberg-theme') . '</h1><p>' . esc_html__('Eine neutrale Grundlage für deine Website. Die Aktivierung erstellt keine Seiten und ändert deine Startseite nicht.', 'aa-gutenberg-theme') . '</p>';
    echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '"><input type="hidden" name="action" value="aagt_setup">';
    wp_nonce_field('aagt_setup');
    submit_button(__('Starterseite als Entwurf anlegen / öffnen', 'aa-gutenberg-theme'));
    echo '</form><p><a class="button" href="' . esc_url(admin_url('site-editor.php')) . '">' . esc_html__('Design im Site Editor bearbeiten', 'aa-gutenberg-theme') . '</a></p><h2>' . esc_html__('Optionales WordPress-Formular', 'aa-gutenberg-theme') . '</h2>';
    echo '<p>' . esc_html__('Standardmäßig ausgeschaltet. Erst Empfänger, Datenschutzhinweise und Mailkonfiguration prüfen, dann Versand freigeben und den Kontaktblock bewusst einfügen.', 'aa-gutenberg-theme') . '</p><form method="post" action="' . esc_url(admin_url('options.php')) . '">';
    settings_fields('aagt_contact_settings');
    echo '<p><label for="aagt-email">' . esc_html__('Empfängeradresse', 'aa-gutenberg-theme') . '</label> <input type="email" id="aagt-email" name="aagt_contact_email" value="' . esc_attr(get_option('aagt_contact_email', '')) . '"></p>';
    echo '<input type="hidden" name="aagt_contact_enabled" value="0"><p><label><input type="checkbox" name="aagt_contact_enabled" value="1"' . checked((bool) get_option('aagt_contact_enabled', false), true, false) . '> ' . esc_html__('Versand des WordPress-Formulars freigeben', 'aa-gutenberg-theme') . '</label></p>';
    submit_button(__('Formulareinstellungen speichern', 'aa-gutenberg-theme'));
    echo '</form></div>';
}
function aagt_help_screen() {
    if (!current_user_can('manage_options')) { return; }
    echo '<div class="wrap"><h1>' . esc_html__('AA Gutenberg Theme: Anleitung', 'aa-gutenberg-theme') . '</h1><ol>';
    foreach (array(
        __('Starterseite als Entwurf anlegen, bearbeiten und erst nach Prüfung veröffentlichen. Erneutes Einrichten öffnet dieselbe Seite und überschreibt keine Inhalte.', 'aa-gutenberg-theme'),
        __('Unter Einstellungen → Lesen eine veröffentlichte Seite ausdrücklich als Startseite zuordnen.', 'aa-gutenberg-theme'),
        __('Header, Footer, Website-Logo, Navigation und globale Stile im Site Editor bearbeiten.', 'aa-gutenberg-theme'),
        __('Neue Abschnitte mit nativen Blöcken oder unter Vorlagen → AA Gutenberg Theme ergänzen.', 'aa-gutenberg-theme'),
        __('Kontaktblock standardmäßig ohne Ausgabe. WordPress-Versand ausdrücklich freigeben. Externe Formularplugins über deren eigenen Block oder Shortcode einfügen.', 'aa-gutenberg-theme'),
        __('Gespeicherte Templates und Stile haben Vorrang vor Dateien. Seiten und Medien bei einem Umzug separat übertragen.', 'aa-gutenberg-theme'),
    ) as $instruction) { echo '<li>' . esc_html($instruction) . '</li>'; }
    echo '</ol><p>' . esc_html__('Weitere Hinweise: BENUTZERHANDBUCH.md, INSTALLATION.md, FORMS.md und MULTILINGUAL.md im Theme.', 'aa-gutenberg-theme') . '</p></div>';
}
