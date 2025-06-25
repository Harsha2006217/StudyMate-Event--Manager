/**
 * Dit script voegt animaties en functionaliteit toe aan de StudyMate Event Manager website.
 * Het zorgt voor een interactieve en visueel aantrekkelijke gebruikerservaring.
 */

// Wacht tot de DOM volledig is geladen voordat de code wordt uitgevoerd.
// Dit voorkomt fouten bij het manipuleren van elementen die nog niet bestaan.
document.addEventListener('DOMContentLoaded', () => {
    // Animatie voor de navigatiebalk.
    // De navigatiebalk glijdt van boven naar beneden in beeld.
    // 'y: -50' betekent dat de balk 50 pixels boven zijn eindpositie begint.
    // 'power2.out' zorgt voor een vloeiende vertraging aan het einde van de animatie.
    gsap.from('.navbar', { y: -50, duration: 0.8, ease: 'power2.out' });

    // Animatie voor alle secties op de pagina.
    // Secties verschijnen één voor één in beeld met een lichte beweging naar boven.
    // 'opacity: 0' betekent dat ze onzichtbaar beginnen.
    // 'stagger: 0.2' zorgt voor een interval van 0.2 seconden tussen de animaties van de secties.
    gsap.from('section', { opacity: 0, y: 20, duration: 1, stagger: 0.2 });

    // Controleer of er een succesbericht op de pagina aanwezig is.
    // Dit gebeurt bijvoorbeeld na het opslaan van een evenement.
    const successMessage = document.querySelector('.text-success');
    if (successMessage) {
        // Animatie voor het succesbericht.
        // Het bericht 'popt' in beeld met een lichte terugveer.
        // 'scale: 0.8' betekent dat het bericht kleiner begint en vervolgens groter wordt.
        gsap.from(successMessage, { 
            opacity: 0,
            scale: 0.8,
            duration: 0.5,
            ease: 'back.out(1.7)' 
        });
    }

    // Functionaliteit voor de herinneringsoptie bij evenementen.
    // Zorgt ervoor dat de tijdselectie alleen beschikbaar is als de herinneringscheckbox is aangevinkt.
    const reminderCheckbox = document.getElementById('reminder');
    const reminderTimeSelect = document.getElementById('reminder_time');
    if (reminderCheckbox && reminderTimeSelect) {
        // Stel de initiële status van het tijdselectieveld in.
        // Als de checkbox niet is aangevinkt, wordt het tijdselectieveld uitgeschakeld.
        reminderTimeSelect.disabled = !reminderCheckbox.checked;

        // Voeg een event listener toe aan de checkbox.
        // Hiermee wordt het tijdselectieveld ingeschakeld of uitgeschakeld op basis van de checkboxstatus.
        reminderCheckbox.addEventListener('change', () => {
            reminderTimeSelect.disabled = !reminderCheckbox.checked;

            // Voeg een fade-effect toe aan het tijdselectieveld.
            // Het veld wordt volledig zichtbaar (opacity: 1) als het is ingeschakeld.
            // Het veld wordt half-doorzichtig (opacity: 0.5) als het is uitgeschakeld.
            gsap.to(reminderTimeSelect, { 
                opacity: reminderCheckbox.checked ? 1 : 0.5, 
                duration: 0.3 
            });
        });
    }

    // Animatie voor notificatie-items in een lijst.
    // Notificaties verschijnen één voor één in beeld met een lichte beweging naar boven.
    const notificationItems = document.querySelectorAll('.list-group-item');
    if (notificationItems.length) {
        // 'stagger: 0.1' zorgt ervoor dat notificaties na elkaar verschijnen met een interval van 0.1 seconden.
        gsap.from(notificationItems, { 
            opacity: 0,
            y: 10,
            duration: 0.5,
            stagger: 0.1 
        });
    }
});
