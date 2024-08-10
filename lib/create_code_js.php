<?php

$urlSource = '';

$pathUrl ='';
$url = 'api/replitclient/submit';



$content = "

document.getElementById('mon-formulaire').addEventListener('submit', async (event) => {
    event.preventDefault();

    const name = document.getElementById('name').value.trim();
    const phone = document.getElementById('phone').value.trim();

    // Remplacez cette URL par celle de votre API
    const apiUrl = '$apiUrl';

    if (!name || !phone) {
        console.error('Veuillez remplir tous les champs.');
        return;
    }

    const formData = { name, phone };

    try {
        const response = await fetch(apiUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });

        if (!response.ok) {
            console.error('Erreur lors de l\'envoi des données');
            return;
        }

        const result = await response.json();
        console.log('Données envoyées avec succès !', result);
        // Réinitialisez le formulaire si nécessaire
        document.getElementById('mon-formulaire').reset();
    } catch (error) {
        console.error(\`Erreur : \${error.message}\`);
    }
});
";

