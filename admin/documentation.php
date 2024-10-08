<?php
/* Copyright (C) 2004-2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2024 SuperAdmin
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    repliclient/admin/about.php
 * \ingroup repliclient
 * \brief   About page of module RepliClient.
 */

// Load Dolibarr environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
	$res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
}
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME']; $tmp2 = realpath(__FILE__); $i = strlen($tmp) - 1; $j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) {
	$i--;
	$j--;
}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/main.inc.php")) {
	$res = @include substr($tmp, 0, ($i + 1))."/main.inc.php";
}
if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php")) {
	$res = @include dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php";
}
// Try main.inc.php using relative path
if (!$res && file_exists("../../main.inc.php")) {
	$res = @include "../../main.inc.php";
}
if (!$res && file_exists("../../../main.inc.php")) {
	$res = @include "../../../main.inc.php";
}
if (!$res) {
	die("Include of main fails");
}

// Libraries
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php';
require_once '../lib/repliclient.lib.php';

// Translations
$langs->loadLangs(array("errors", "admin", "repliclient@repliclient"));

// Access control
if (!$user->admin) {
	accessforbidden();
}

// Parameters
$action = GETPOST('action', 'aZ09');
$backtopage = GETPOST('backtopage', 'alpha');


/*
 * Actions
 */

// None


/*
 * View
 */

$form = new Form($db);

$help_url = '';
$title = "RepliClientSetup";

llxHeader('', $langs->trans($title), $help_url, '', 0, 0, '', '', '', 'mod-repliclient page-admin_about');

// Subheader
$linkback = '<a href="'.($backtopage ? $backtopage : DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1').'">'.$langs->trans("BackToModuleList").'</a>';

print load_fiche_titre($langs->trans($title), $linkback, 'title_setup');

// Configuration header
$head = repliclientAdminPrepareHead();
print dol_get_fiche_head($head, 'documentation', $langs->trans($title), 0, 'repliclient@repliclient');




?>
<h2>Modules à activer</h2>
<ul>
    <li>ClickToDial</li>
    <li>API / Web services (serveur REST)</li>
</ul>
<hr>
   <h2>Documentation de l'API - Endpoint <code>/api/repliclient/submit</code></h2>
    <p>Cet endpoint permet aux utilisateurs d'envoyer des données au serveur en utilisant la méthode POST. Les données doivent être au format JSON.</p>
    <h2>URL</h2>
    <p><code>/api/repliclient/submit</code></p>
    <h3>Méthode</h3>
    <p>POST</p>
    <h3>Paramètres</h3>
    <ul>
        <li><strong>Content-Type</strong>: <code>application/json</code></li>
        <li><strong>Corps de la requête (JSON)</strong> :</li>
    </ul>
    <pre>
        {
            "name": "John Doe",
            "telephone":"0606060606",
            "raison": "Demande d'information",
            "authkey": "YOUR_KEY"
        }
    </pre>
    <h3>Réponses</h3>
    <ul>
        <li><strong>Code 200 succes</strong> : La requête a été traitée avec succès.</li>
        <li><strong>Code 403 Forbidden: Unauthorised</strong> : La clé n'est pas valide</li>
        <li><strong>Code 400 Bad Request</strong> : La requête est mal formée ou manque des informations.</li>
        <li><strong>Code 500</strong> : Erreur dans le traitement de la requete</li>
    </ul>
<?php
print "<hr>";
print "<section>";
print "<h2>Exemple de code html (boostrap)</h2>";

print "<textarea class='text-html'>";
$content  = <<<HTML
    <div class="container">
            <div class="row">
                <div class="col-xl-9 mx-auto">
                    <div class="row align-items-center ">
                        <div class=" col-12 col-md-4">
                            <div class="mr-2 mb-2 d-flex ">
                                <img src="assets/img/phone-call.png" alt="Logo" class="m-auto">
                            </div>
                            <div class="text-center">
                                <h2>Besoin d'un renseignement ?</h2>
                                <p>On vous rappelle dès que possible.</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-8">
                            
                            <form action='' id='dolirepliclient'>
                                <div class='form-group'>
                                    <label for='name'>Nom</label>
                                    <input type='text' id='name' name='name' class='form-control' required>
                                </div>
                                <div class='form-group'>
                                    <label for='phone'>Téléphone</label>
                                    <input type='tel' id='phone' name='phone' class='form-control' required>
                                </div>
                                <div class='form-group'>
                                    <label for='raison'>Raison</label>
                                    <input type="text" id='raison' name='raison' class='form-control'
                                        required></textarea>
                                </div>

                                <input type='hidden' id='authkey' value='__YOUR___KEY___'>
                                <div class='form-group mt-3'>
                                   
                                    <button type='submit' class='btn btn-primary'>Envoyer</button>
                                </div>
                                <div id="response-repli"></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
HTML;
echo  htmlentities($content);
print "</textarea></section>";
print "<hr>";
print "<section>";
print "<h2>Exemple de code JS(vanilla)</h2>";

print "<textarea class='text-html'>";
$content  = <<<HTML

document.getElementById('dolirepliclient').addEventListener('submit', async (event) => {
    event.preventDefault();

    const name = document.getElementById('name').value.trim();
    const telephone = document.getElementById('phone').value.trim();
    const raison = document.getElementById('raison').value.trim();
    const authkey = document.getElementById('authkey').value.trim();
    const callresponse = document.querySelector('#response-repli')

    // Remplacez cette URL par celle de votre API
    const apiUrl = '__YOUR__URL__';

    if (!name || !phone || !raison) {
        callresponse.innerHTML = "Veuillez remplir tous les champs.";
        console.error('Veuillez remplir tous les champs.');
        return;
    }

    const formData = { name, telephone , raison, authkey};

    try {
        const response = await fetch(apiUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body:  JSON.stringify(formData)
        });


        if (response.status != 200) {
            console.error('Erreur lors de l\'envoi des données');
            return;
        }

        const result = await response.json();

        if (response.status === 200) {
            callresponse.innerHTML = "Demande envoyée avec succès !"
            document.getElementById('dolirepliclient').reset();
        }
        // Réinitialisez le formulaire si nécessaire
        
    } catch (error) {
        console.error(error.message);
    }
})
HTML;
echo  htmlentities($content);
print "</textarea></section>";
// Page end
print dol_get_fiche_end();
llxFooter();
$db->close();
?>
<style>
.text-html{
min-height:300px ;
width: 100%;
}
    </style>