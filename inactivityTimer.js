let inactivityTime = function () {
    let time;
    const timerElement = document.getElementById('timer');
    const logoutUrl = 'login.php'; // URL de redirection en cas de déconnexion

    function resetTimer() {
        clearTimeout(time);
        startTimer();
    }

    function startTimer() {
        // Afficher le temps restant
        let timeLeft = 60; // Temps en secondes
        timerElement.textContent = `Temps restant avant déconnexion: ${timeLeft} secondes`;

        // Décompte de la minuterie
        time = setInterval(function () {
            timeLeft -= 1;
            timerElement.textContent = `Temps restant avant déconnexion: ${timeLeft} secondes`;

            if (timeLeft <= 0) {
                clearInterval(time);
                window.location.href = logoutUrl; // Redirection après le délai d'inactivité
            }
        }, 1000);
    }

    // Écouter les événements d'activité
    window.onload = resetTimer;
    document.onmousemove = resetTimer;
    document.onkeypress = resetTimer;
    document.onscroll = resetTimer;
};

window.onload = function () {
    inactivityTime();
};
