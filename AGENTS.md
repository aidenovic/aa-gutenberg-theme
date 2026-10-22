# AA Gutenberg Theme

- Eigenständiges Theme; Ordner/Text Domain aa-gutenberg-theme, PHP aagt_, AAGT_.
- Keine Änderungen an anderen Themes, WordPress-Core, Infrastruktur oder Datenbanken.
- Aktivierung erstellt/ändert keine Seiten, Logos, Startseiten oder Benutzer.
- Einrichtung bleibt explizit, idempotent und erhält bearbeitete Inhalte.
- Native Gutenberg-Blöcke bevorzugen; keine ACF- oder Parent-Abhängigkeit.
- Backend-Schreibaktionen: manage_options, Nonce, Sanitizing und Escaping.
- Kontaktblock standardmäßig none; nativer Versand nur mit Freigabe + Empfänger.
- Keine echten QA-Mails. Keine Secrets, Historie, Backups oder Laufzeitdaten verpacken.
- Palette/Typografie in theme.json; Designrollen in assets/css/tokens.css.
- Herkunft und GPL erhalten; Dokumentation auf AA-Verhalten abstimmen.
- PHP/JS/JSON und reale Gutenberg-Roundtrips vor Paketbau prüfen.
