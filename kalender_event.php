<?php
require_once 'functions.php';
requireLogin();

$stmt = $pdo->prepare("SELECT * FROM events WHERE user_id = ? ORDER BY date");
$stmt->execute([$_SESSION['user_id']]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyMate - Kalender</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">StudyMate</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="add_event.php">Evenement toevoegen</a></li>
                    <li class="nav-item"><a class="nav-link active" href="kalender_event.php">Kalender</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Uitloggen</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <section class="container mt-5">
        <h2 class="text-center">Kalender</h2>
        <div id="calendar" class="row"></div>
    </section>
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>© 2025 StudyMate Event Manager</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
    <script>
        const events = <?php echo json_encode($events); ?>;
        const calendar = document.getElementById('calendar');
        const today = new Date();
        const daysInMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0).getDate();

        for (let i = 1; i <= daysInMonth; i++) {
            const dayDiv = document.createElement('div');
            dayDiv.className = 'col-2 p-2 border';
            dayDiv.innerHTML = `<strong>${i}</strong>`;

            events.forEach(event => {
                const eventDate = new Date(event.date);
                if (eventDate.getDate() === i && eventDate.getMonth() === today.getMonth()) {
                    const eventDiv = document.createElement('div');
                    eventDiv.className = `event ${event.category}`;
                    eventDiv.textContent = `${event.time} - ${event.title}`;
                    dayDiv.appendChild(eventDiv);
                }
            });

            calendar.appendChild(dayDiv);
            gsap.from(dayDiv, { opacity: 0, y: 20, duration: 0.5, delay: i * 0.05 });
        }
    </script>
</body>
</html>