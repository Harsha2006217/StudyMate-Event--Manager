// GSAP-animaties en interactiviteit
document.addEventListener('DOMContentLoaded', () => {
    // Animeer navbar en section
    gsap.from('.navbar', { y: -50, duration: 0.8, ease: 'power2.out' });
    gsap.from('section', { opacity: 0, y: 20, duration: 1, stagger: 0.2 });

    // Animeer succesmelding (groen bij toevoegen)
    const successMessage = document.querySelector('.text-success');
    if (successMessage) {
        gsap.from(successMessage, { 
            opacity: 0, 
            scale: 0.8, 
            duration: 0.5, 
            ease: 'back.out(1.7)' 
        });
    }

    // Schakel reminder-tijdveld in/uit
    const reminderCheckbox = document.getElementById('reminder');
    const reminderTimeSelect = document.getElementById('reminder_time');
    if (reminderCheckbox && reminderTimeSelect) {
        reminderTimeSelect.disabled = !reminderCheckbox.checked;
        reminderCheckbox.addEventListener('change', () => {
            reminderTimeSelect.disabled = !reminderCheckbox.checked;
            gsap.to(reminderTimeSelect, { 
                opacity: reminderCheckbox.checked ? 1 : 0.5, 
                duration: 0.3 
            });
        });
    }

    // Animeer notificaties
    const notificationItems = document.querySelectorAll('.list-group-item');
    if (notificationItems.length) {
        gsap.from(notificationItems, { 
            opacity: 0, 
            y: 10, 
            duration: 0.5, 
            stagger: 0.1 
        });
    }
});