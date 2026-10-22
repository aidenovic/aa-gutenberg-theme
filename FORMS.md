# Optionale Formulare

AA Gutenberg Theme 1.1.0 enthält keine Gravity-Forms-Anbindung. Formularplugins
werden unabhängig vom Theme mit ihren eigenen Blöcken oder Shortcodes eingebunden.
Für Contact Form 7 das offizielle Plugin installieren, Empfänger konfigurieren
und dessen Block bzw. Shortcode auf der gewünschten Seite einsetzen.
CF7 ist weder erforderlich noch im Theme enthalten. Es gibt keine Formularimporte.

Der optionale Theme-Block aagt/contact-form unterstützt ausschließlich none/native.
Standard ist none. Frühere oder unbekannte Anbieterwerte bleiben ohne Ausgabe
und ohne Versand; es gibt keinen automatischen Ersatz durch das native Formular.

Für das native Formular unter AA Gutenberg Theme → Einrichten eine gültige
Empfängeradresse und die ausdrückliche Versandfreigabe setzen. Anschließend im
Kontaktblock WordPress-Formular ohne Plugin auswählen. Ohne beide Einstellungen
sind native Ausgabe und direkte POST-Anfragen gesperrt. Die WordPress-Adminadresse
wird nicht als Ersatzempfänger verwendet.

Name, E-Mail, Nachricht und Zustimmung sind Pflicht. Serverseitige Prüfung:
Nonce, Datentypen, Längen, E-Mail und Header-Injection. Honeypot und fünf Anfragen
pro zehn Minuten pro gesalzenem Client-Hash begrenzen einfache Spamversuche.
Hash/Zähler liegen temporär als Transient vor; keine Nachrichten/Einträge werden
durch das Theme gespeichert. Mailplugins und Infrastruktur separat prüfen.

Versand läuft über wp_mail. Die Erfolgsmeldung bestätigt die Annahme durch
WordPress, keinen Postfacheingang. QA fängt alle Mails ab. Vor Freigabe auf dem
Zielsystem Empfänger, Datenschutz, Caching/Nonces und tatsächliche Zustellung prüfen.
Die Freigabe dieses nativen Blocks steuert keine externen Formularplugins.
