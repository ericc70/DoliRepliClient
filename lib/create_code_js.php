<?php




$domaine = $_SERVER['HTTP_HOST'];

$urlEndPoint = '/api/index.php/repliclient/submit';
$apiUrl = $domaine . $urlEndPoint;


// creation du formulaire en js
$content_formhtlml ="
 function insertForm() {
            const formHTML = `
                <div class='form-container'>
                    <form action=''  id='dolirepliclient'>
                        <div class='form-group'>
                            <label for='name'>Nom</label>
                            <input type='text' id='name' name='name' class='form-control' required>
                        </div>
                        <div class='form-group'>
                            <label for='phone'>Téléphone</label>
                            <input type='tel' id='phone' name='phone' class='form-control' required>
                        </div>
                        <div class='form-group'>
                            <label for='reason'>Raison</label>
                            <textarea id='reason' name='reason' class='form-control' required></textarea>
                        </div>
                         <input type='hidden' id='authkey' value='$object->authkey'>
                        <div class='form-group'>
                            <button type='submit' class='btn btn-primary'>Envoyer</button>
                        </div>
                    </form>
                </div>
            `;

          
            const targetDiv = document.getElementById('repliclientdoli');
            
            if (targetDiv) {
                targetDiv.innerHTML = formHTML;
            } else {
                console.error('Div avec l\'ID 'repliclientdoli' non trouvée.');
            }
        }

        // Appeler la fonction pour insérer le formulaire après le chargement du DOM
        document.addEventListener('DOMContentLoaded', insertForm);
";


// traitement

$content_traitement = "

document.getElementById('dolirepliclient').addEventListener('submit', async (event) => {
    event.preventDefault();

    const name = document.getElementById('name').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const raison = document.getElementById('raison').value.trim();
    const authkey = document.getElementById('authkey').value.trim();


    // Remplacez cette URL par celle de votre API
    const apiUrl = '$apiUrl';

    if (!name || !phone || !raison) {
        console.error('Veuillez remplir tous les champs.');
        return;
    }

    const formData = { name, phone , raison, authkey};

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

$content = $content_formhtlml . $content_traitement;