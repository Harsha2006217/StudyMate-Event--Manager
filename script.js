/**
 * StudyMate Evenementenbeheer - Animaties en Effecten
 * 
 * Dit script maakt de website interactief en visueel aantrekkelijk door animaties
 * toe te voegen aan verschillende onderdelen van de pagina zoals de navigatiebalk,
 * secties, en notificaties. Het regelt ook de functionaliteit van formulieren zoals
 * de herinnering-optie bij evenementen.
 */

// Wacht tot de DOM volledig is geladen voordat we de code uitvoeren
// Dit is essentieel omdat we anders elementen proberen te animeren die nog niet bestaan
document.addEventListener('DOMContentLoaded', () => {
    // Animatie voor de navigatiebalk: deze glijdt van boven naar beneden in beeld
    // De 'y: -50' betekent dat de balk 50 pixels boven zijn eindpositie begint
    // 'power2.out' zorgt voor een vertraging aan het einde van de animatie
    gsap.from('.navbar', { y: -50, duration: 0.8, ease: 'power2.out' });
    
    // Animatie voor alle sectie-elementen: deze verschijnen één voor één in beeld
    // 'opacity: 0' betekent dat ze volledig onzichtbaar beginnen
    // 'stagger: 0.2' zorgt ervoor dat er 0.2 seconden tussen de start van elke animatie zit
    gsap.from('section', { opacity: 0, y: 20, duration: 1, stagger: 0.2 });

    // Zoek naar een succes-bericht op de pagina (bijvoorbeeld na het opslaan van een evenement)
    const successMessage = document.querySelector('.text-success');
    
    // Als er een succesbericht gevonden is, voeg dan een speciale 'pop-up' animatie toe
    // Deze code wordt alleen uitgevoerd als er daadwerkelijk een succesbericht bestaat
    if (successMessage) {
        // Animatie voor het succesbericht: het 'popt' in beeld met een kleine terugveer
        // 'scale: 0.8' betekent dat het bericht op 80% van de normale grootte begint
        // 'back.out(1.7)' zorgt voor een terugveereffect, alsof het bericht een beetje stuitert
        gsap.from(successMessage, { 
            opacity: 0,
            scale: 0.8,
            duration: 0.5,
            ease: 'back.out(1.7)'
        });
    }

    // Functionaliteit voor de herinnering-optie bij evenementen
    // Deze code regelt dat de tijdselectie alleen beschikbaar is als de herinneringscheckbox is aangevinkt
    const reminderCheckbox = document.getElementById('reminder');
    const reminderTimeSelect = document.getElementById('reminder_time');
    
    // Controleer of beide elementen bestaan op de huidige pagina
    // Dit is belangrijk omdat niet alle pagina's deze elementen bevatten
    if (reminderCheckbox && reminderTimeSelect) {
        // Stel de initiële status van het tijdselectieveld in op basis van de checkbox
        // Als de checkbox niet is aangevinkt, is het tijdselectieveld uitgeschakeld
        reminderTimeSelect.disabled = !reminderCheckbox.checked;
        
        // Voeg een event listener toe aan de checkbox om te reageren wanneer deze wordt aan- of uitgevinkt
        reminderCheckbox.addEventListener('change', () => {
            // Schakel het tijdselectieveld in of uit afhankelijk van de status van de checkbox
            reminderTimeSelect.disabled = !reminderCheckbox.checked;
            
            // Voeg een fade-effect toe aan het tijdselectieveld
            // Als het veld is ingeschakeld, wordt het volledig zichtbaar (opacity: 1)
            // Als het veld is uitgeschakeld, wordt het half-doorzichtig (opacity: 0.5)
            gsap.to(reminderTimeSelect, { 
                opacity: reminderCheckbox.checked ? 1 : 0.5, 
                duration: 0.3 
            });
        });
    }

    // Animatie voor de notificatie-items in een lijst
    // Deze code zoekt alle notificatie-items en laat ze één voor één in beeld komen
    const notificationItems = document.querySelectorAll('.list-group-item');
    if (notificationItems.length) {
        // Animatie voor alle notificatie-items: ze glijden omhoog terwijl ze verschijnen
        // Ze beginnen 10 pixels lager en volledig onzichtbaar
        // 'stagger: 0.1' zorgt ervoor dat ze na elkaar verschijnen met 0.1 seconde tussentijd
        gsap.from(notificationItems, { 
            opacity: 0,
            y: 10,
            duration: 0.5,
            stagger: 0.1
        });
    }
});