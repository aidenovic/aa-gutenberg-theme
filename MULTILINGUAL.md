# Mehrsprachigkeit

Die deutsche Theme-Oberfläche verwendet gettext mit Text Domain
`aa-gutenberg-theme`. Unter `languages/` liegen POT, englischer PO-/MO-Katalog
und der JSON-Katalog für den Kontaktblock. Deutsch verwendet die Quelltexte.
PHP- und JavaScript-Übersetzungen wurden mit en_US geprüft.

Die Sprache der WordPress-Oberfläche und die Sprache der Seiteninhalte sind
getrennte Einstellungen. Texte, Navigation, rechtliche Hinweise und Formulare
über das gewählte Sprachplugin übersetzen; Theme-Kataloge übersetzen nicht
nachträglich gespeicherte Seiteninhalte.

Vorhandenes Polylang oder WPML wird für Home-URLs und optionalen Shortcode
`[aa_language_switcher]` unterstützt. Den Shortcode bei Bedarf als Shortcode-Block
im Header einsetzen. Ohne passendes Plugin oder weniger als zwei sichtbare
Sprachen erscheint nichts. Ein Sprachplugin wird nicht installiert.
Die Integrationen wurden hier nicht mit einer konfigurierten mehrsprachigen
Website geprüft. HTML-Sprachattribute und RTL-Grundverhalten verwaltet WordPress.

Kataloge bei Codeänderungen erneut mit WP-CLI i18n make-pot/make-json und msgfmt
erstellen. Die JSON-Datei muss den Hash des relativen Editor-Scriptpfads behalten.
