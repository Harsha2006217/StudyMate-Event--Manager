/**
 * StudyMate Evenementenbeheer - Animaties en Effecten
 * 
 * Dit bestand zorgt ervoor dat de website er levendig uitziet en soepel werkt.
 * Het maakt de webpagina's mooier met bewegende onderdelen en zorgt dat bepaalde
 * knoppen en velden automatisch reageren wanneer je ze gebruikt.
 */

// Deze regel wacht tot de hele pagina is geladen voordat de effecten starten
// Zo weten we zeker dat alle onderdelen van de pagina aanwezig zijn voordat we ze animeren
document.addEventListener('DOMContentLoaded', () => {
    // Dit zorgt ervoor dat de menubalk bovenaan de pagina mooi naar beneden schuift
    // De menubalk begint 50 pixels boven waar hij moet staan en schuift dan in 0,8 seconden naar beneden
    // Het effect is niet lineair maar vertraagt op het eind, waardoor het er natuurlijker uitziet
    gsap.from('.navbar', { y: -50, duration: 0.8, ease: 'power2.out' });
    
    // Dit zorgt ervoor dat de inhoudsvakken (secties) één voor één in beeld verschijnen
    // Ze beginnen onzichtbaar en 20 pixels lager, en schuiven dan omhoog terwijl ze zichtbaar worden
    // Er zit 0,2 seconden tussen elke sectie, zodat ze niet allemaal tegelijk verschijnen
    gsap.from('section', { opacity: 0, y: 20, duration: 1, stagger: 0.2 });

    // Dit is speciaal voor de groene succesboodschappen die verschijnen na een actie
    // Bijvoorbeeld als je een evenement hebt toegevoegd of gewijzigd
    // We zoeken eerst of er zo'n succesboodschap op de pagina staat
    const successMessage = document.querySelector('.text-success');
    
    // Als er een succesboodschap is, krijgt deze een speciaal effect
    if (successMessage) {
        // De boodschap verschijnt met een 'pop'-effect
        // Het begint onzichtbaar en klein (80% van normale grootte)
        // Het groeit dan in 0,5 seconden naar de normale grootte
        // Het 'back.out' effect zorgt ervoor dat het lijkt alsof het een beetje terugveert, zoals een bal
        gsap.from(successMessage, { 
            opacity: 0,         // Begin volledig onzichtbaar
            scale: 0.8,         // Begin op 80% van de normale grootte
            duration: 0.5,      // Duurt 0,5 seconden om volledig te verschijnen
            ease: 'back.out(1.7)' // Een effect dat lijkt op een kleine terugveer aan het einde
        });
    }

    // Dit regelt de herinnering-functie bij het toevoegen of bewerken van evenementen
    // Het zorgt ervoor dat je alleen een herinneringstijd kunt kiezen als je de herinnering aanvinkt
    
    // Eerst kijken we of deze elementen bestaan op de pagina (ze zijn alleen op bepaalde pagina's aanwezig)
    const reminderCheckbox = document.getElementById('reminder');  // Het aanvinkvakje voor herinneringen
    const reminderTimeSelect = document.getElementById('reminder_time');  // Het keuzevakje voor de herinneringstijd
    
    // Als beide elementen bestaan op de huidige pagina, dan voegen we de functionaliteit toe
    if (reminderCheckbox && reminderTimeSelect) {
        // Hier stellen we de beginstatus in: als de checkbox niet is aangevinkt, 
        // dan kan het tijdselectieveld niet worden gebruikt
        reminderTimeSelect.disabled = !reminderCheckbox.checked;
        
        // Dit gebeurt er als je op de checkbox klikt:
        reminderCheckbox.addEventListener('change', () => {
            // Als de checkbox aangevinkt is, wordt het tijdselectieveld ingeschakeld
            // Anders wordt het uitgeschakeld
            reminderTimeSelect.disabled = !reminderCheckbox.checked;
            
            // Voor een mooi effect veranderen we de doorzichtigheid van het tijdselectieveld
            // Als het tijdselectieveld ingeschakeld wordt, wordt het volledig zichtbaar
            // Als het uitgeschakeld wordt, wordt het een beetje doorzichtig gemaakt
            gsap.to(reminderTimeSelect, { 
                opacity: reminderCheckbox.checked ? 1 : 0.5, 
                duration: 0.3 
            });
        });
    }

    // Dit zorgt voor de binnenkomende animatie van de notificaties in de lijst
    // We zoeken naar alle elementen die een notificatie weergeven
    const notificationItems = document.querySelectorAll('.list-group-item');
    if (notificationItems.length) {
        // Voor elk notificatie-item zorgen we ervoor dat het mooi in beeld komt
        // Ze beginnen allemaal onzichtbaar en 10 pixels lager dan waar ze moeten komen
        // Terwijl ze in beeld komen, bewegen ze een beetje omhoog
        // Er zit 0,1 seconden tussen de animaties van de verschillende notificaties
        gsap.from(notificationItems, { 
            opacity: 0,    // Begin volledig onzichtbaar 
            y: 10,         // Begin 10px lager dan de uiteindelijke positie
            duration: 0.5, // Elke animatie duurt 0,5 seconden
            stagger: 0.1   // Wacht 0,1 seconden tussen het starten van elke notificatie-animatie
        });
    }
});