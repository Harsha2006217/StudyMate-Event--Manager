<?php
/**
 * Pagina: Evenement toevoegen
 * 
 * Dit bestand maakt het mogelijk voor ingelogde gebruikers om een nieuw evenement toe te voegen.
 * 
 * Functionaliteiten:
 * - Formulier voor het toevoegen van een nieuw evenement
 * - Validatie van alle ingevoerde gegevens
 * - Opslaan van het evenement in de database
 * - Doorsturen naar dashboard na succesvol toevoegen
 */

// Importeren van het functions.php bestand dat algemene functies bevat
// zoals inlogcontrole, databaseverbinding, en beveiligingsfuncties
require_once 'functions.php';

// Controleer of de gebruiker is ingelogd
// Als de gebruiker niet is ingelogd, wordt hij/zij doorgestuurd naar de loginpagina
// Dit is een beveiligingsmaatregel om te voorkomen dat ongeautoriseerde gebruikers evenementen kunnen toevoegen
requireLogin(); 

// Definieer de categorieën waaruit de gebruiker kan kiezen
// Dit is een associatieve array met de interne waarde (key) en de zichtbare naam (value) voor elke categorie
// Deze array wordt later gebruikt om automatisch de opties in het dropdown-menu te genereren
$categories = [
    'school' => 'School',    // Voor schoolgerelateerde evenementen zoals lessen, tentamens en opdrachten
    'sociaal' => 'Sociaal',  // Voor sociale activiteiten zoals feestjes, afspraken en bijeenkomsten
    'gaming' => 'Gaming'     // Voor gaming-gerelateerde evenementen zoals toernooien of speelsessies
];

// Controleer of het formulier is verzonden (wanneer de gebruiker op 'Opslaan' heeft geklikt)
// We gebruiken $_SERVER['REQUEST_METHOD'] om te controleren of de pagina via een POST-verzoek is geladen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Haal de titel op uit het formulier en verwijder ongewenste tekens
    // De sanitizeInput functie beschermt tegen XSS-aanvallen door HTML-tags te verwijderen
    $title = sanitizeInput($_POST['title']);

    // Haal de overige gegevens op uit het formulier
    // Deze waarden worden rechtstreeks uit de POST-array gehaald
    $date = $_POST['date'];         // De gekozen datum in formaat JJJJ-MM-DD
    $time = $_POST['time'];         // De gekozen tijd in formaat UU:MM
    $category = $_POST['category']; // De geselecteerde categorie uit de dropdown

    // Bepaal of een herinnering is ingesteld
    // Als de checkbox is aangevinkt, wordt $reminder 1 (true), anders 0 (false)
    // De isset-functie controleert of de 'reminder'-key bestaat in de POST-array
    $reminder = isset($_POST['reminder']) ? 1 : 0;

    // Haal de herinneringstijd op als een herinnering is ingesteld, anders zet op null
    // Deze voorwaardelijke toewijzing (ternary operator) controleert of reminder true is
    // Als dat zo is, wordt de herinneringstijd opgehaald, anders wordt null toegewezen
    $reminder_time = $reminder ? $_POST['reminder_time'] : null;

    // Valideer de ingevoerde gegevens voordat ze worden opgeslagen
    // Deze controles zorgen ervoor dat alleen geldige gegevens in de database komen
    if (empty($title)) {
        // Controleer of de titel niet leeg is
        // Als de titel leeg is, krijgt de gebruiker een foutmelding
        $error = "Titel is verplicht.";
    } elseif (strtotime($date) < strtotime(date('Y-m-d'))) {
        // Controleer of de gekozen datum niet in het verleden ligt
        // Vergelijkt de ingevoerde datum met de huidige datum
        $error = "Datum mag niet in het verleden liggen.";
    } elseif (!array_key_exists($category, $categories)) {
        // Controleer of de gekozen categorie bestaat in de lijst met toegestane categorieën
        // Dit voorkomt dat iemand een ongeldige categorie probeert in te voeren via formuliermanipulatie
        $error = "Ongeldige categorie.";
    } else {
        // Als alle validaties succesvol zijn, voeg het evenement toe aan de database

        // Bereid een SQL-statement voor met placeholders om SQL-injectie te voorkomen
        // De vraagtekens zijn placeholders die later worden vervangen door echte waarden
        $stmt = $pdo->prepare(
            "INSERT INTO events (user_id, title, date, time, category, reminder, reminder_time) VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        // Voer het SQL-statement uit met de waarden uit het formulier
        // De execute-methode vervangt de placeholders (?) met de waarden in de array
        // Dit is een veilige manier om gegevens in de database op te slaan
        $stmt->execute([
            $_SESSION['user_id'], // ID van de ingelogde gebruiker (uit de sessie)
            $title,               // Titel van het evenement
            $date,                // Datum van het evenement
            $time,                // Tijd van het evenement
            $category,            // Categorie van het evenement
            $reminder,            // Of er een herinnering is ingesteld (1 of 0)
            $reminder_time        // Wanneer de herinnering moet worden verzonden
        ]);

        // Sla een succesmelding op in de sessie voor weergave op het dashboard
        // Dit geeft de gebruiker feedback dat het evenement succesvol is toegevoegd
        setFlashMessage('success', "Evenement '$title' succesvol toegevoegd!");

        // Stuur de gebruiker door naar het dashboard
        // De header-functie stuurt een HTTP-header om door te verwijzen naar een andere pagina
        header("Location: dashboard.php");
        
        // Stop de verdere uitvoering van de code
        // Dit voorkomt dat er nog code wordt uitgevoerd nadat de gebruiker is doorgestuurd
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <!--
        Het <head> element bevat meta-informatie over de webpagina:
        - charset="UTF-8": Zorgt voor correcte weergave van speciale tekens en accenten
        - viewport: Zorgt voor goede weergave op verschillende schermformaten (responsiveness)
        - title: Verschijnt in het tabblad van de browser
        - CSS-bestanden: Voor de opmaak van de pagina (eigen CSS en Bootstrap-framework)
    -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyMate - Evenement toevoegen</title>
    <link rel="stylesheet" href="style.css"> <!-- Eigen CSS-bestand voor specifieke styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap CSS-framework -->
</head>
<body>
    <!--
        De navigatiebalk bovenaan de pagina:
        - navbar-dark bg-dark: Donkere kleurstelling
        - container: Houdt de inhoud gecentreerd en responsief
        - navbar-brand: Logo of naam van de website (StudyMate)
        - navbar-toggler: Hamburger-menu voor mobiele weergave
        - navbar-nav: De lijst met navigatie-items
        - active: Markeert de huidige pagina in het menu
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
        Hoofdsectie met het formulier:
        - container: Houdt de inhoud gecentreerd en responsief
        - mt-5: Margin top (ruimte bovenaan) van formaat 5
        - add-event: Eigen CSS-klasse voor eventuele specifieke styling
        - h2 text-center: Gecentreerde hoofdtitel
    -->
    <section class="container mt-5 add-event">
        <h2 class="text-center">Evenement toevoegen</h2>

        <!--
            Foutmelding weergave (indien aanwezig):
            - Toont alleen als er een fout is opgetreden bij het valideren van het formulier
            - text-danger: Rode tekstkleur voor de foutmelding
            - text-center: Gecentreerde tekst
        -->
        <?php if (isset($error)): ?>
            <p class="text-danger text-center"><?php echo $error; ?></p>
        <?php endif; ?>

        <!--
            Het formulier voor het toevoegen van een evenement:
            - method="POST": Verzendt formuliergegevens veilig en niet zichtbaar in de URL
            - col-md-6 mx-auto: Maakt het formulier half zo breed als de container op medium schermen en centreert het
            - Alle velden hebben labels voor toegankelijkheid en gebruiksvriendelijkheid
        -->
        <form method="POST" class="col-md-6 mx-auto">
            <!--
                Titel invoerveld:
                - mb-3: Margin bottom (ruimte onderaan) van formaat 3
                - form-label: Bootstrap-stijl voor het label
                - form-control: Bootstrap-stijl voor het invoerveld
                - required: Veld moet worden ingevuld
                - value="...": Behoudt de ingevoerde waarde als het formulier opnieuw wordt geladen na een fout
                - htmlspecialchars: Voorkomt XSS-aanvallen door speciale tekens om te zetten
            -->
            <div class="mb-3">
                <label for="title" class="form-label">Titel</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>" required>
            </div>

            <!--
                Datum invoerveld:
                - type="date": Toont een datumkiezer in ondersteunde browsers
                - required: Veld moet worden ingevuld
            -->
            <div class="mb-3">
                <label for="date" class="form-label">Datum</label>
                <input type="date" class="form-control" id="date" name="date" required>
            </div>

            <!--
                Tijd invoerveld:
                - type="time": Toont een tijdkiezer in ondersteunde browsers
                - required: Veld moet worden ingevuld
            -->
            <div class="mb-3">
                <label for="time" class="form-label">Tijd</label>
                <input type="time" class="form-control" id="time" name="time" required>
            </div>

            <!--
                Categorie keuzemenu:
                - form-select: Bootstrap-stijl voor een dropdown-menu
                - required: Er moet een optie worden geselecteerd
                - De opties worden dynamisch gegenereerd uit de $categories array
                - De foreach-lus loopt door elke categorie en maakt een option-element
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
                Herinnering checkbox:
                - form-check: Bootstrap-stijl voor een checkbox
                - form-check-input: Stijl voor het vinkje zelf
                - form-check-label: Stijl voor het label naast het vinkje
                - Als deze checkbox wordt aangevinkt, wordt het reminder_time veld gebruikt
            -->
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="reminder" name="reminder">
                <label class="form-check-label" for="reminder">Herinnering instellen</label>
            </div>

            <!--
                Herinneringstijd keuzemenu:
                - Wordt gebruikt als de gebruiker een herinnering wil instellen
                - Bevat vooraf gedefinieerde tijdsopties (5 min, 30 min, 1 uur voor het evenement)
                - Het JavaScript in script.js zorgt ervoor dat dit veld alleen actief is als de checkbox is aangevinkt
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
                - btn btn-success: Groene knop in Bootstrap-stijl
                - w-100: Volledige breedte (width 100%)
                - type="submit": Verstuurt het formulier wanneer erop geklikt wordt
            -->
            <button type="submit" class="btn btn-success w-100">Opslaan</button>

            <!--
                Terugknop:
                - btn btn-secondary: Grijze knop in Bootstrap-stijl
                - w-100: Volledige breedte
                - mt-2: Margin top van formaat 2 (ruimte boven de knop)
                - Dit is een link die teruggaat naar het dashboard, opgemaakt als een knop
            -->
            <a href="dashboard.php" class="btn btn-secondary w-100 mt-2">Terug naar overzicht</a>
        </form>
    </section>

    <!--
        Voettekst (footer):
        - bg-dark: Donkere achtergrond
        - text-white: Witte tekst
        - text-center: Gecentreerde tekst
        - py-3: Padding (ruimte binnen het element) in y-richting (boven en onder) van formaat 3
        - mt-5: Margin top (ruimte bovenaan) van formaat 5
        - Bevat copyright-informatie
    -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>© 2025 StudyMate Event Manager</p>
    </footer>

    <!--
        JavaScript-bestanden:
        - Bootstrap JavaScript voor interactieve elementen zoals het uitklapbare menu
        - script.js voor eigen functionaliteit zoals het in-/uitschakelen van het herinneringstijd-veld
        - Deze worden onderaan de pagina geladen zodat de pagina eerst visueel laadt
    -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>