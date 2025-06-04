/**
 * StudyMate Event Manager JavaScript
 * 
 * Dit bestand bevat alle interactieve functionaliteit en animaties voor de StudyMate applicatie.
 * Het gebruikt GSAP (GreenSock Animation Platform) voor vloeiende animaties en verbetert
 * de gebruikerservaring door visuele feedback te geven.
 */

// Wacht tot de pagina volledig is geladen voordat de JavaScript wordt uitgevoerd
document.addEventListener('DOMContentLoaded', () => {
    // Animeer de navigatiebalk met een inschuif-effect van boven
    // De balk beweegt 50px naar beneden gedurende 0,8 seconden met een vloeiende animatie
    gsap.from('.navbar', { y: -50, duration: 0.8, ease: 'power2.out' });
    
    // Animeer alle secties met een fade-in effect en verticale beweging
    // Elke sectie komt 20px omhoog en wordt geleidelijk zichtbaar, met 0,2 sec vertraging tussen elke sectie
    gsap.from('section', { opacity: 0, y: 20, duration: 1, stagger: 0.2 });

    // Speciale animatie voor succeesmeldingen (bijvoorbeeld na toevoegen van evenement)
    // Zoek naar een element met de klasse 'text-success'
    const successMessage = document.querySelector('.text-success');
    if (successMessage) {
        // Als er een succesmelding is, animeer deze met een pop-effect en fade-in
        // De melding schijnt even op door te vergroten en vervolgens terug te keren naar normale grootte
        gsap.from(successMessage, { 
            opacity: 0,         // Start volledig onzichtbaar
            scale: 0.8,         // Start op 80% van de normale grootte
            duration: 0.5,      // Animatie duurt 0,5 seconden
            ease: 'back.out(1.7)' // Speciale "terugveer" animatie voor een dynamisch effect
        });
    }

    // Functionaliteit voor de herinneringoptie bij het toevoegen/bewerken van evenementen
    // Controleert of de herinnering-checkbox en tijdselectie aanwezig zijn op de pagina
    const reminderCheckbox = document.getElementById('reminder');
    const reminderTimeSelect = document.getElementById('reminder_time');
    if (reminderCheckbox && reminderTimeSelect) {
        // Stel de initiële status in van het tijdselectieveld op basis van de checkbox
        reminderTimeSelect.disabled = !reminderCheckbox.checked;
        
        // Voeg een event listener toe aan de checkbox om te reageren wanneer deze wordt aangevinkt
        reminderCheckbox.addEventListener('change', () => {
            // Schakel het tijdselectieveld in of uit afhankelijk van de checkbox-status
            reminderTimeSelect.disabled = !reminderCheckbox.checked;
            
            // Animeer de doorzichtigheid van het tijdselectieveld voor visuele feedback
            // Bij inschakelen wordt het veld volledig zichtbaar, bij uitschakelen half doorzichtig
            gsap.to(reminderTimeSelect, { 
                opacity: reminderCheckbox.checked ? 1 : 0.5, 
                duration: 0.3 
            });
        });
    }

    // Animatie voor notificatie-items in de notificatielijst
    // Zoek alle items met de klasse 'list-group-item'
    const notificationItems = document.querySelectorAll('.list-group-item');
    if (notificationItems.length) {
        // Als er notificaties zijn, animeer ze met een cascade-effect
        // Elk item verschijnt na elkaar met een klein omhoog-bewegend effect
        gsap.from(notificationItems, { 
            opacity: 0,    // Start volledig onzichtbaar 
            y: 10,         // Start 10px lager dan de uiteindelijke positie
            duration: 0.5, // Elke animatie duurt 0,5 seconden
            stagger: 0.1   // Wacht 0,1 seconden tussen het starten van elke notificatie-animatie
        });
    }
});