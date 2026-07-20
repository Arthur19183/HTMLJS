<?php
/**
 * Kopieren Sie diese Datei auf dem Infomaniak-Server nach config.local.php
 * und tragen Sie das Passwort der Mailbox ein.
 *
 * config.local.php darf NICHT ins Git-Repository.
 */
return [
    // Infomaniak SMTP
    'smtp_host' => 'mail.infomaniak.com',
    'smtp_port' => 465,
    'smtp_secure' => 'ssl', // Port 465 = SSL/TLS
    'smtp_user' => 'kontakt@sap-fico-beratung.ch',
    'smtp_pass' => 'HIER_MAILBOX_PASSWORT_EINTRAGEN',

    // Absender und Empfänger (Infomaniak-Mailbox)
    'mail_from' => 'kontakt@sap-fico-beratung.ch',
    'mail_from_name' => 'Website SAP FI/CO Beratung',
    'mail_to' => 'kontakt@sap-fico-beratung.ch',

    // Nach erfolgreichem Versand
    'redirect_success' => 'https://www.sap-fico-beratung.ch/?kontakt=ok#kontakt',
    'redirect_error' => 'https://www.sap-fico-beratung.ch/?kontakt=fehler#kontakt',
];
