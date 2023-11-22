<?php
// Verbindung zur Datenbank herstellen
$conn = new mysqli('localhost', 'root', '', 'kontakte');

// Verbindung auf Verbindungsfehler überprüfen
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// Überprüfen, ob das Formular per POST gesendet wurde und ob alle erforderlichen POST-Daten vorhanden sind
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['visitor_name']) && isset($_POST['visitor_email']) && isset($_POST['concerned_department']) && isset($_POST['visitor_message'])) {

    // Filtern und Zuweisen der POST-Daten zu Variablen
    $name = filter_var($_POST['visitor_name']);
    $email = filter_var($_POST['visitor_email']);
    $department = filter_var($_POST['concerned_department']);
    $message = filter_var($_POST['visitor_message']);

    // Vorbereiten der SQL-Anweisung zum Einfügen von Daten in die Datenbank
    $sql = $conn->prepare("INSERT INTO kontakte (name, email, grund, nachricht) VALUES (?, ?, ?, ?)");

    // Überprüfen, ob die Vorbereitung der SQL-Anweisung erfolgreich war
    if (!$sql) {
        echo "Error: " . $conn->error;
    } else {
        // Binden der vorbereiteten Anweisung an die Variablen
        $sql->bind_param("ssss", $name, $email, $department, $message);

        // Ausführen der vorbereiteten Anweisung und Ausgabe einer Erfolgsmeldung oder eines Fehlers
        if ($sql->execute()) {
            echo "Neuer Datensatz erfolgreich erstellt!";
        } else {
            echo "Error: " . $sql->error;
        }
        // Schließen der vorbereiteten Anweisung
        $sql->close();
    }
} else {
    // Ausgabe einer Fehlermeldung, falls nicht alle erforderlichen POST-Daten vorhanden sind
    echo '<p>Etwas ist schiefgegangen</p>';
}

// Überprüfen, ob das Formular per POST gesendet wurde
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Setzen der E-Mail-Adresse des Empfängers und des Betreffs der E-Mail
    $recipient = "contact@domain.com"; // Hier die Empfänger-E-Mail-Adresse eintragen
    $email_title = "Betreff der E-Mail"; // Hier den Betreff der E-Mail eintragen

    // Zusammenstellen des E-Mail-Bodys mit den notwendigen Informationen aus den POST-Daten
    $email_body = "<div>"; // Beginn des E-Mail-Body
    $email_body .= "<label><b>Name:</b></label>&nbsp;<span>".$name."</span><br>"; // Hinzufügen des Namens
    $email_body .= "<label><b>Email:</b></label>&nbsp;<span>".$email."</span><br>"; // Hinzufügen der E-Mail
    // Weitere Felder hinzufügen...
    $email_body .= "</div>"; // Ende des E-Mail-Body

    // Definieren der E-Mail-Header
    $headers  = 'MIME-Version: 1.0' . "\r\n"
    .'Content-type: text/html; charset=utf-8' . "\r\n"
    .'From: ' . $email . "\r\n"; // Setzen des Absenders als E-Mail-Adresse

    // Senden der E-Mail und Ausgabe einer Erfolgsmeldung oder eines Fehlers
    if(mail($recipient, $email_title, $email_body, $headers)) {
        echo "<p>Vielen Dank für Ihre Kontaktaufnahme, $name. Sie erhalten innerhalb von 24 Stunden eine Antwort.</p>";
    } else {
        echo '<p>Es tut uns leid, aber die E-Mail wurde nicht erfolgreich übermittelt.</p>';
    }
} else {
    // Ausgabe einer Fehlermeldung, falls das Formular nicht per POST gesendet wurde
    echo '<p>Etwas ist schiefgegangen</p>';
}

// Schließen der Datenbankverbindung
$conn->close();
?>
