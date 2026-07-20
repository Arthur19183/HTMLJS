# Infomaniak Kontaktformular – Server-Setup

## 1. Mailbox anlegen
Im Infomaniak Manager eine Adresse erstellen:
`kontakt@sap-fico-beratung.ch`

## 2. Config auf dem Server
Im Website-Root (dort wo `index.html` und `contact.php` liegen):

```bash
cp config.example.php config.local.php
```

In `config.local.php` das Passwort eintragen:
`'smtp_pass' => 'IHR_PASSWORT',`

## 3. Dateien hochladen / git pull
Mindestens:
- `index.html`, `script.js`, `contact.php`
- `config.example.php`, `.htaccess`
- Ordner `lib/PHPMailer/`

`config.local.php` nur auf dem Server, nicht in Git.

## 4. Test
Formular auf https://www.sap-fico-beratung.ch/#kontakt absenden.
Mail sollte in `kontakt@sap-fico-beratung.ch` ankommen.
Antworten geht an die Besucher-Adresse (Reply-To).
