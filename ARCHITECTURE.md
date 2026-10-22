# Architektur

`functions.php` lädt unabhängige PHP-Module. `theme.json` definiert Palette,
Typografie, Layout und `settings.custom.aa`. `templates/` enthält Seite,
Einzelbeitrag, Beitragsübersicht, Archiv, Suche und Fehlerseite; Header/Footer
liegen unter `parts/`. Es gibt bewusst keine fest verdrahtete front-page.html,
damit WordPress die Zuordnung unter Lesen verwendet.

`inc/content.php` erzeugt neutrale native Blockinhalte. `inc/setup.php` bietet
explizite Entwurfserstellung und Backend-Anleitung. Keine Aktivierungshooks,
ACF-Konvertierung, Logo-Imports oder historischen Reparaturen.
`aagt_starter_page_id` identifiziert die angelegte Starterseite; vorhandene
bearbeitete Inhalte werden unverändert geöffnet.

`inc/design.php` und `assets/js/editor-design.js` verbinden globale Einstellungen
mit Designrollen und der ungespeicherten Editorvorschau. `assets/css/tokens.css`
und `layout.css` teilen Frontend und Editor. `inc/quality.php` bietet SEO-Fallback,
Pluginerkennung und optionale Animationen. `assets/js/motion.js` respektiert
reduzierte Bewegung, Tastaturfokus und fehlende JavaScript-Unterstützung.

`inc/i18n.php` liefert Sprach-URLs und den Shortcode `[aa_language_switcher]`
für vorhandenes Polylang oder WPML. Kein Sprachplugin ist erforderlich.

`aagt/contact-form` ist der einzige eigene dynamische Block. `inc/forms.php`
begrenzt die Ausgabe auf den nativen Anbieter, `inc/native-form.php` verarbeitet das optionale WordPress-
Formular. Externe Formularplugins verwalten eigene Blöcke/Shortcodes. Native POST-Aktionen heißen `aagt_contact`.

Kennungen: PHP/Optionen/Hooks `aagt_`, Konstanten `AAGT_`, Blöcke/Patterns `aagt/`,
Assets `aagt-`, eigene CSS-Variablen `--aa-`, WordPress-Custom-Werte `--wp--custom--aa--`.
Einige Formularklassen/-felder und der Sprachshortcode beginnen mit `aa-`/`aa_`.
Alle Kennungen sind von ZW getrennt. Es gibt keine Legacy-Optionsabfrage.

Backend-Schreibaktionen verwenden manage_options und WordPress-Nonces.
Einstellungen laufen über die Settings API; Starterinhalte werden mit wp_slash
an wp_insert_post übergeben. Der Kontaktversand prüft seine eigene Nonce.
