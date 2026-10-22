<?php
/** Neutral content made of editable core blocks. */
defined('ABSPATH') || exit;
function aagt_starter_content() {
    $heading = esc_html__('Platz für deine Ideen', 'aa-gutenberg-theme');
    $intro = esc_html__('Dies ist deine bearbeitbare Grundlage. Ersetze diese Platzhalter durch deine eigenen Inhalte.', 'aa-gutenberg-theme');
    $title = esc_html__('Dein nächster Abschnitt', 'aa-gutenberg-theme');
    $copy = esc_html__('Ergänze Texte, Bilder und weitere Gutenberg-Blöcke. Farben und Typografie kannst du im Site Editor unter Stile anpassen.', 'aa-gutenberg-theme');
    return '<!-- wp:group {"className":"aagt-section","layout":{"type":"constrained"}} --><div class="wp-block-group aagt-section">'
        . '<!-- wp:heading {"level":1} --><h1 class="wp-block-heading">' . $heading . '</h1><!-- /wp:heading -->'
        . '<!-- wp:paragraph --><p>' . $intro . '</p><!-- /wp:paragraph --></div><!-- /wp:group -->'
        . '<!-- wp:group {"className":"aagt-section","layout":{"type":"constrained"}} --><div class="wp-block-group aagt-section">'
        . '<!-- wp:heading --><h2 class="wp-block-heading">' . $title . '</h2><!-- /wp:heading -->'
        . '<!-- wp:paragraph --><p>' . $copy . '</p><!-- /wp:paragraph --></div><!-- /wp:group -->';
}
