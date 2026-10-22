<?php
/** AA adaptation modified 2026-10-19; derived from ZW Gutenberg Theme by ZEITWERK. GPL-2.0-or-later; see NOTICE.md and LICENSE.txt. */
/** Small WordPress contact form; no form plugin or entry database required. */
defined('ABSPATH') || exit;

function aagt_send_native_form($input) {
    if (!aagt_native_form_enabled()) { return 'disabled'; }
    foreach (array('_aa_nonce', 'aa_name', 'aa_email', 'aa_message', 'aa_consent', 'aa_website') as $field) {
        if (isset($input[$field]) && !is_string($input[$field])) { return 'invalid'; }
    }
    if (!wp_verify_nonce($input['_aa_nonce'] ?? '', 'aagt_contact')) { return 'expired'; }
    if (!empty($input['aa_website'])) { return 'sent'; }
    $name = sanitize_text_field(wp_unslash($input['aa_name'] ?? ''));
    $raw_email = wp_unslash($input['aa_email'] ?? '');
    $email = sanitize_email($raw_email);
    $message = sanitize_textarea_field(wp_unslash($input['aa_message'] ?? ''));
    if ($name === '' || strlen($name) > 200 || preg_match('/[\r\n]/', $raw_email) || !is_email($email) || $message === '' || strlen($message) > 10000 || ($input['aa_consent'] ?? '') !== '1') {
        return 'invalid';
    }
    // Store only a salted IP hash and a count, never the submitted contact data.
    $key = 'aagt_contact_' . hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? 'local') . wp_salt('nonce'));
    $count = (int) get_transient($key);
    if ($count >= 5) { return 'rate-limit'; }
    set_transient($key, $count + 1, 10 * MINUTE_IN_SECONDS);
    $recipient = get_option('aagt_contact_email', '');
    if (!is_email($recipient)) { return 'mail-error'; }
    /* translators: %s: website title. */
    $subject = sprintf(__('Neue Kontaktanfrage – %s', 'aa-gutenberg-theme'), sanitize_text_field(get_bloginfo('name')));
    $body = __('Name', 'aa-gutenberg-theme') . ': ' . $name . "\n"
        . __('E-Mail', 'aa-gutenberg-theme') . ': ' . $email . "\n\n" . $message;
    return wp_mail($recipient, $subject, $body, array('Content-Type: text/plain; charset=UTF-8', 'Reply-To: ' . $email)) ? 'sent' : 'mail-error';
}

function aagt_native_form_enabled() {
    return (bool) get_option('aagt_contact_enabled', false) && is_email(get_option('aagt_contact_email', ''));
}

function aagt_render_native_form() {
    if (!aagt_native_form_enabled()) { return ''; }
    static $instance = 0;
    $prefix = 'aagt-contact-' . ++$instance . '-';
    $status = isset($_GET['aa_contact']) && is_string($_GET['aa_contact']) ? sanitize_key(wp_unslash($_GET['aa_contact'])) : '';
    $messages = array(
        'sent' => __('Vielen Dank. Ihre Anfrage wurde zum Versand angenommen.', 'aa-gutenberg-theme'),
        'invalid' => __('Bitte Name, gültige E-Mail, Nachricht und Zustimmung vollständig angeben.', 'aa-gutenberg-theme'),
        'expired' => __('Das Formular ist abgelaufen. Bitte die Seite neu laden und erneut versuchen.', 'aa-gutenberg-theme'),
        'rate-limit' => __('Bitte warten Sie einige Minuten, bevor Sie erneut eine Anfrage senden.', 'aa-gutenberg-theme'),
        'mail-error' => __('Die Anfrage konnte nicht versendet werden. Bitte später erneut versuchen.', 'aa-gutenberg-theme'),
    );
    ob_start();
    echo '<div ' . get_block_wrapper_attributes(array('class' => 'aagt-native-form')) . '>';
    if (isset($messages[$status])) {
        echo '<p class="aa-contact-status" role="' . ($status === 'sent' ? 'status' : 'alert') . '">' . esc_html($messages[$status]) . '</p>';
    }
    echo '<form class="contact-form aa-native-contact" method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
    echo '<input type="hidden" name="action" value="aagt_contact">';
    echo '<input type="hidden" name="_aa_nonce" value="' . esc_attr(wp_create_nonce('aagt_contact')) . '">';
    wp_referer_field();
    echo '<div class="aa-contact-honeypot" aria-hidden="true"><label for="' . esc_attr($prefix . 'website') . '">Website</label><input id="' . esc_attr($prefix . 'website') . '" name="aa_website" tabindex="-1" autocomplete="off" type="text"></div>';
    foreach (array('name' => __('Name', 'aa-gutenberg-theme'), 'email' => __('E-Mail', 'aa-gutenberg-theme')) as $field => $label) {
        echo '<div class="form-field"><label for="' . esc_attr($prefix . $field) . '">' . esc_html($label) . ' *</label><input class="contact-input" id="' . esc_attr($prefix . $field) . '" name="aa_' . esc_attr($field) . '" type="' . ($field === 'email' ? 'email' : 'text') . '" autocomplete="' . ($field === 'email' ? 'email' : 'name') . '" maxlength="200" required></div>';
    }
    echo '<div class="form-field"><label for="' . esc_attr($prefix . 'message') . '">' . esc_html__('Nachricht', 'aa-gutenberg-theme') . ' *</label><textarea class="contact-input" id="' . esc_attr($prefix . 'message') . '" name="aa_message" rows="6" maxlength="10000" required></textarea></div>';
    echo '<div class="privacy-check"><input id="' . esc_attr($prefix . 'consent') . '" name="aa_consent" type="checkbox" value="1" required><label for="' . esc_attr($prefix . 'consent') . '">' . esc_html__('Ich stimme der Verarbeitung meiner Angaben zur Bearbeitung meiner Anfrage zu.', 'aa-gutenberg-theme');
    $privacy = get_privacy_policy_url();
    if ($privacy) { echo ' <a href="' . esc_url($privacy) . '">' . esc_html__('Datenschutzerklärung', 'aa-gutenberg-theme') . '</a>'; }
    echo '</label></div><button class="cta" type="submit">' . esc_html__('Anfrage senden', 'aa-gutenberg-theme') . '</button></form></div>';
    return ob_get_clean();
}

function aagt_handle_native_form() {
    $status = aagt_send_native_form($_POST);
    $referer = wp_get_referer() ?: aagt_home_url();
    $url = remove_query_arg('aa_contact', $referer);
    $url = preg_replace('/#.*$/', '', $url);
    wp_safe_redirect(add_query_arg('aa_contact', $status, $url) . '#kontakt', 303);
    exit;
}
add_action('admin_post_aagt_contact', 'aagt_handle_native_form');
add_action('admin_post_nopriv_aagt_contact', 'aagt_handle_native_form');
