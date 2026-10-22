# Prüfstand und Grenzen

Geprüft am 19.10.2026 mit PHP 8.3, Node 24, lokalem Chrome und WordPress 7.1.3.
Das Theme wurde nicht auf der bestehenden Website aktiviert. Der WordPress-Test
lädt AA nur im CLI-Prozess, sperrt sämtliche SQL-Schreibbefehle und hält Theme-
Caches im Prozessspeicher. Bestehende Plugins werden dabei übersprungen.

Bestanden:

- PHP-Syntax aller Theme-Dateien; JavaScript-Syntax und JSON-Struktur.
- Eigenständiges Block-Theme, eigene Kennungen, Blockregistrierung, Version.
- Reale WordPress-Blockausgabe, Global Styles und Button-Designwerte.
- 14 PHP- und Gutenberg-JavaScript-Fixtures: Templates, Vorlagenteile, Patterns,
  Starter und Kontaktblock; keine ungültigen Blöcke, stabiles Parse/Save-Verhalten.
- Frontend-Fixture mit echten WordPress-Header-/Footer-/Inhaltsausgaben bei
  375/768/1440 Pixeln: H1 sichtbar, kein horizontaler Überlauf, Tastaturfokus.
- Editorvorschau der Buttonwerte und Sprache im Editor-Iframe mit simuliertem
  Daten-Store; Animationen bei reduzierter Bewegung deaktiviert.
- Native Formularsperre, Nonce, Array-Eingaben, Pflichtfelder, Honeypot,
  Header-Injection und gültige Anfrage mit vollständig abgefangenem wp_mail.
- Wiederholte Einrichtung öffnet vorhandene Seite ohne Änderungen.
- Passwortgeschützte Inhalte erscheinen nicht in der öffentlichen
  Meta-Beschreibung; Regressionstest schlägt ohne Schutz fehl.
- Englischer PHP-/JS-Katalog, Lizenz-/Paketprüfung und Quellbaum-Hashvergleich.

Noch auf der Zielinstallation zu prüfen:

- Vollständiger eingeloggter Site-/Seiteneditor, tatsächliches Speichern,
  erstmalige Entwurfserstellung, Themeaktivierung und Updateablauf.
- Live-Global-Styles über den echten Editor-Store; QA prüfte die Bridge isoliert.
- Externe Formularplugins sowie konfigurierte WPML-/Polylang-Projekte.
- Native Mailzustellung, tatsächliche Cache-/SMTP-Konfiguration und Spamschutz
  unter Last. Fünf-Anfragen-Zähler ist keine umfassende Missbrauchssicherung.
- Weitere WordPress-/PHP-Versionen, andere Browser, RTL, assistive Technologien
  sowie eigene Inhalte, Medien und Navigationsziele.

Keine neue Installation, Datenbank, Domain oder Veröffentlichung war beauftragt.
Es wurde keine vollständige WCAG-/Datenschutz-Konformität bestätigt.
