
// 1. Validation des Formulaires
// vérifier que les données soumises sont correctes avant d'envoyer le formulaire au serveur.


document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form');

    form.addEventListener('submit', (event) => {
        const email = form.querySelector('input[name="email"]').value;
        const telephone = form.querySelector('input[name="telephone"]').value;
        const niveau = form.querySelector('select[name="niveau"]').value;

        if (!email.includes('@')) {
            alert('L\'email doit être valide.');
            event.preventDefault();
            return;
        }

        if (telephone.length < 9) {
            alert('Le numéro de téléphone doit contenir au moins 9 chiffres.');
            event.preventDefault();
            return;
        }

        if (!['L1', 'L2', 'L3', 'M1', 'M2'].includes(niveau)) {
            alert('Le niveau doit être sélectionné.');
            event.preventDefault();
            return;
        }
    });
});

// Affichage des messages

const showMessage = (message, type) => {
    const messageBox = document.createElement('div');
    messageBox.textContent = message;
    messageBox.className = `message ${type}`;
    document.body.appendChild(messageBox);

    setTimeout(() => {
        messageBox.remove();
    }, 3000);
};
