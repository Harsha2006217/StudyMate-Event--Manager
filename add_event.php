<?php
/**
 * Pagina: Evenement toevoegen
 * 
 * Dit PHP-bestand zorgt ervoor dat een ingelogde gebruiker een nieuw evenement kan toevoegen aan het StudyMate Event Manager systeem.
 * De gebruiker kan een titel, datum, tijd, categorie en eventueel een herinnering instellen.
 * Alle invoer wordt gecontroleerd en veilig opgeslagen in de database.
 */

// Laad de functies uit het bestand 'functions.php' zodat we deze kunnen gebruiken op deze pagina
require_once 'functions.php';

// Controleer of de gebruiker is ingelogd. Als de gebruiker niet is ingelogd, wordt hij/zij doorgestuurd naar de loginpagina.
// Dit voorkomt dat niet-gemachtigde personen evenementen kunnen toevoegen.
requireLogin(); // Functie die controleert of de gebruiker is ingelogd

// Definieer de categorieën waaruit de gebruiker kan kiezen bij het aanmaken van een evenement.
// De array bevat de interne waarde (key) en de zichtbare naam (value) voor elke categorie.
$categories = [
    'school' => 'School',    // Categorie voor schoolgerelateerde evenementen
    'sociaal' => 'Sociaal',  // Categorie voor sociale evenementen
    'gaming' => 'Gaming'     // Categorie voor gaming evenementen
];

// Controleer of het formulier is verzonden via een POST-verzoek (dus als de gebruiker op 'Opslaan' heeft geklikt)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Haal de ingevulde titel op uit het formulier en maak deze veilig tegen XSS-aanvallen
    $title = sanitizeInput($_POST['title']); // Functie die ongewenste HTML of scripts uit de invoer haalt

    // Haal de ingevulde datum, tijd en categorie op uit het formulier
    $date = $_POST['date'];         // Datum van het evenement
    $time = $_POST['time'];         // Tijd van het evenement
    $category = $_POST['category']; // Gekozen categorie

    // Controleer of de gebruiker een herinnering heeft aangevinkt
    // Als de checkbox is aangevinkt, krijgt $reminder de waarde 1 (waar), anders 0 (niet waar)
    $reminder = isset($_POST['reminder']) ? 1 : 0;

    // Als er een herinnering is ingesteld, haal dan de gekozen herinneringstijd op, anders zet op null
    $reminder_time = $reminder ? $_POST['reminder_time'] : null;

    // Valideer de ingevulde gegevens voordat ze in de database worden opgeslagen
    if (empty($title)) {
        // Controleer of de titel niet leeg is
        $error = "Titel is verplicht.";
    } elseif (strtotime($date) < strtotime(date('Y-m-d'))) {
        // Controleer of de gekozen datum niet in het verleden ligt
        $error = "Datum mag niet in het verleden liggen.";
    } elseif (!array_key_exists($category, $categories)) {
        // Controleer of de gekozen categorie bestaat in de lijst met toegestane categorieën
        $error = "Ongeldige categorie.";
    } else {
        // Als alle validaties goed zijn, voeg het evenement toe aan de database

        // Bereid een SQL-query voor om het evenement veilig in de database te plaatsen
        // Gebruik prepared statements om SQL-injectie te voorkomen
        $stmt = $pdo->prepare(
            "INSERT INTO events (user_id, title, date, time, category, reminder, reminder_time) VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        // Voer de query uit met de juiste waarden:
        // - user_id: het ID van de ingelogde gebruiker (uit de sessie)
        // - title: de ingevoerde titel
        // - date: de gekozen datum
        // - time: de gekozen tijd
        // - category: de gekozen categorie
        // - reminder: of er een herinnering is ingesteld (1 of 0)
        // - reminder_time: de gekozen herinneringstijd of null
        $stmt->execute([
            $_SESSION['user_id'],
            $title,
            $date,
            $time,
            $category,
            $reminder,
            $reminder_time
        ]);

        // Zet een succesmelding in de sessie zodat deze op het dashboard getoond kan worden
        setFlashMessage('success', "Evenement '$title' succesvol toegevoegd!");

        // Stuur de gebruiker terug naar het dashboard na het toevoegen van het evenement
        header("Location: dashboard.php");
        exit(); // Stop verdere uitvoering van de code
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <!--
        Het <head>-gedeelte bevat informatie over de pagina:
        - De tekencodering (UTF-8) zorgt ervoor dat alle tekens correct worden weergegeven.
        - De viewport-instelling zorgt ervoor dat de pagina goed schaalt op mobiele apparaten.
        - De titel wordt getoond in het tabblad van de browser.
        - CSS-bestanden worden geladen voor de opmaak van de pagina.
        - Bootstrap wordt gebruikt voor een moderne en responsieve vormgeving.
    -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyMate - Evenement toevoegen</title>
    <link rel="stylesheet" href="style.css"> <!-- Eigen CSS-bestand voor extra styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap CSS -->
</head>
<body>
    <!--
        Navigatiebalk bovenaan de pagina:
        - Bevat de naam van de website (StudyMate).
        - Links naar andere pagina's: Home, Evenement toevoegen, Kalender, Uitloggen.
        - De actieve pagina (Evenement toevoegen) wordt gemarkeerd.
        - De navigatie is responsief en werkt ook op mobiele apparaten.
    -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">StudyMate</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="add_event.php">Evenement toevoegen</a></li>
                    <li class="nav-item"><a class="nav-link" href="kalender_event.php">Kalender</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Uitloggen</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!--
        Hoofdsectie van de pagina:
        - Hier staat het formulier waarmee de gebruiker een nieuw evenement kan toevoegen.
        - Het formulier bevat velden voor titel, datum, tijd, categorie, herinnering en herinneringstijd.
        - Eventuele foutmeldingen worden boven het formulier getoond.
    -->
    <section class="container mt-5 add-event">
        <h2 class="text-center">Evenement toevoegen</h2>

        <!--
            Als er een foutmelding is (bijvoorbeeld een verplicht veld is niet ingevuld),
            wordt deze hier getoond in rode tekst, gecentreerd op de pagina.
        -->
        <?php if (isset($error)): ?>
            <p class="text-danger text-center"><?php echo $error; ?></p>
        <?php endif; ?>

        <!--
            Het formulier voor het toevoegen van een evenement:
            - Methode: POST (gegevens worden veilig verzonden)
            - Bootstrap-klassen zorgen voor een mooie opmaak en centrering
        -->
        <form method="POST" class="col-md-6 mx-auto">
            <!--
                Invoerveld voor de titel van het evenement:
                - 'required' zorgt ervoor dat het veld niet leeg mag zijn.
                - De waarde blijft staan als het formulier opnieuw wordt getoond na een fout.
            -->
            <div class="mb-3">
                <label for="title" class="form-label">Titel</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>" required>
            </div>

            <!--
                Invoerveld voor de datum van het evenement:
                - 'type="date"' zorgt voor een datumkiezer.
                - 'required' maakt het veld verplicht.
            -->
            <div class="mb-3">
                <label for="date" class="form-label">Datum</label>
                <input type="date" class="form-control" id="date" name="date" required>
            </div>

            <!--
                Invoerveld voor de tijd van het evenement:
                - 'type="time"' zorgt voor een tijdkiezer.
                - 'required' maakt het veld verplicht.
            -->
            <div class="mb-3">
                <label for="time" class="form-label">Tijd</label>
                <input type="time" class="form-control" id="time" name="time" required>
            </div>

            <!--
                Dropdownmenu voor het kiezen van een categorie:
                - De opties worden automatisch gegenereerd uit de $categories-array.
                - 'required' maakt het veld verplicht.
            -->
            <div class="mb-3">
                <label for="category" class="form-label">Categorie</label>
                <select class="form-select" id="category" name="category" required>
                    <?php foreach ($categories as $key => $value): ?>
                        <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!--
                Checkbox voor het instellen van een herinnering:
                - Als deze is aangevinkt, kan de gebruiker een herinneringstijd kiezen.
            -->
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="reminder" name="reminder">
                <label class="form-check-label" for="reminder">Herinnering instellen</label>
            </div>

            <!--
                Dropdownmenu voor het kiezen van de herinneringstijd:
                - Wordt alleen gebruikt als de herinnering is aangevinkt.
                - De opties geven aan hoeveel tijd van tevoren de herinnering wordt gestuurd.
            -->
            <div class="mb-3">
                <label for="reminder_time" class="form-label">Herinneringstijd</label>
                <select class="form-select" id="reminder_time" name="reminder_time">
                    <option value="5 minuten ervoor">5 minuten ervoor</option>
                    <option value="30 minuten ervoor">30 minuten ervoor</option>
                    <option value="1 uur ervoor">1 uur ervoor</option>
                </select>
            </div>

            <!--
                Opslaan-knop:
                - Hiermee wordt het formulier verzonden en het evenement toegevoegd.
                - 'btn-success' geeft de knop een groene kleur.
                - 'w-100' zorgt ervoor dat de knop de volledige breedte van het formulier inneemt.
            -->
            <button type="submit" class="btn btn-success w-100">Opslaan</button>

            <!--
                Terugknop:
                - Ziet eruit als een knop, maar is een link naar het dashboard.
                - 'btn-secondary' geeft de knop een grijze kleur.
                - 'mt-2' voegt extra ruimte toe boven de knop.
            -->
            <a href="dashboard.php" class="btn btn-secondary w-100 mt-2">Terug naar overzicht</a>
        </form>
    </section>

    <!--
        Voettekst onderaan de pagina:
        - Donkere achtergrond met witte tekst.
        - Gecentreerde tekst met copyright-informatie.
        - 'py-3' zorgt voor verticale ruimte binnen de voettekst.
        - 'mt-5' zorgt voor extra ruimte boven de voettekst.
    -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>© 2025 StudyMate Event Manager</p>
    </footer>

    <!--
        JavaScript-bestanden:
        - Bootstrap JavaScript voor interactieve elementen zoals het uitklapbare menu.
        - Eigen script.js-bestand voor extra functionaliteit, zoals het in- of uitschakelen van het herinneringstijd-veld.
    -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>