<?php
/* Copyright (C) 2024 SuperAdmin
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
 *
 * Library javascript to enable Browser notifications
 */

if (!defined('NOREQUIREUSER')) {
	define('NOREQUIREUSER', '1');
}
if (!defined('NOREQUIREDB')) {
	define('NOREQUIREDB', '1');
}
if (!defined('NOREQUIRESOC')) {
	define('NOREQUIRESOC', '1');
}
if (!defined('NOREQUIRETRAN')) {
	define('NOREQUIRETRAN', '1');
}
if (!defined('NOCSRFCHECK')) {
	define('NOCSRFCHECK', 1);
}
if (!defined('NOTOKENRENEWAL')) {
	define('NOTOKENRENEWAL', 1);
}
if (!defined('NOLOGIN')) {
	define('NOLOGIN', 1);
}
if (!defined('NOREQUIREMENU')) {
	define('NOREQUIREMENU', 1);
}
if (!defined('NOREQUIREHTML')) {
	define('NOREQUIREHTML', 1);
}
if (!defined('NOREQUIREAJAX')) {
	define('NOREQUIREAJAX', '1');
}


/**
 * \file    repliclient/js/repliclient.js.php
 * \ingroup repliclient
 * \brief   JavaScript file for module RepliClient.
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
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/../main.inc.php")) {
	$res = @include substr($tmp, 0, ($i + 1))."/../main.inc.php";
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

// Define js type
header('Content-Type: application/javascript');
// Important: Following code is to cache this file to avoid page request by browser at each Dolibarr page access.
// You can use CTRL+F5 to refresh your browser cache.
if (empty($dolibarr_nocache)) {
	header('Cache-Control: max-age=3600, public, must-revalidate');
} else {
	header('Cache-Control: no-cache');
}
?>

  const notifyNewContact = (nb) => {
        console.log('hello');

        // Sélectionnez le conteneur cible
        const contenaire = document.querySelector('#mainmenutd_repliclient');
        
        // Assurez-vous que le conteneur existe avant de continuer
        if (contenaire) {
            // Trouvez le span cible dans le conteneur
            
            
            // Créez une nouvelle div avec un chiffre
            const newDiv = document.createElement('div');
            newDiv.textContent = '5'; // Remplacez '5' par le chiffre que vous souhaitez afficher
            newDiv.style.display = 'inline-block'; // Assure que la div s'affiche en ligne avec les autres éléments
            newDiv.style.marginRight = '5px'; // Ajoute un petit espace à droite de la nouvelle div
            newDiv.style.fontWeight = 'bold'; // Style optionnel pour rendre le chiffre plus visible
            
            // Trouvez l'élément parent qui contient le span cible
            const parentDiv = contenaire.querySelector('.tmenucenter');
			const targetSpan = parentDiv.querySelector('.mainmenuaspan');
            
            // Vérifiez si le span cible et le parent existent
            if (targetSpan && parentDiv) {
                // Insérez la nouvelle div avant le span cible
			//	 targetSpan.innerHTML ="<span>5</span>";
				targetSpan.insertAdjacentHTML("afterbegin", "<span class ='badge badge-dot badge-status-1 ' >"+ nb +"</span>"  )
           
            } else {
                if (!targetSpan) {
                    console.error('Le span cible n\'a pas été trouvé.');
                }
                if (!parentDiv) {
                    console.error('Le parent contenant le span cible n\'a pas été trouvé.');
                }
            }
        } else {
            console.error('Conteneur non trouvé.');
        }
    }

    const fetchNb = async (etat) => {
        try {
            // Définir l'URL courante du navigateur
        const currentUrl = new URL(window.location.href);

        // Obtenez la base de l'URL (protocole, domaine, et port)
        const baseUrl = window.location.origin;

        // Récupérez le chemin de l'URL actuelle
        const pathName = currentUrl.pathname;

        // Vérifiez si "htdocs" est présent dans le chemin
        let updatedBaseUrl;
        if (pathName.includes('htdocs')) {
            // Si "htdocs" est présent, ajoutez-le à la base URL
            updatedBaseUrl = `${baseUrl}/htdocs`;
        } else {
            // Sinon, utilisez la base URL actuelle
            updatedBaseUrl = baseUrl;
        }


        // Construire l'URL complète pour le fetch
        const fetchUrl = `${updatedBaseUrl}/custom/repliclient/query_demande.php?action=countbystatus&status=${etat}`;


            const response = await fetch( fetchUrl);
            
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
    
            const data = await response.json();  
            

            return data;
        } catch (error) {
            console.error('Une erreur est survenue :', error.message);
        }
    };

  
document.addEventListener('DOMContentLoaded', async () => {
    // Attendre la résolution de la promesse
    const nbdemande = await fetchNb(10);
    console.log(nbdemande); 
    if (nbdemande > 0) {
        notifyNewContact(nbdemande);
    }
});
 




//  <li class="tmenu" id="mainmenutd_repliclient">
// 	<div class="tmenucenter">
// 		<a class="tmenuimage tmenu" tabindex="-1" href="/htdocs/custom/repliclient/repliclientindex.php?idmenu=193&amp;mainmenu=repliclient&amp;leftmenu=" title="RepliClient">
// 		<div class="mainmenu repliclient topmenuimage">
// 			<span class="far fa-file pictofixedwidth valignmiddle" style=""></span>
// 		</div></a>
// 		<a class="tmenulabel tmenu" id="mainmenua_repliclient" href="/htdocs/custom/repliclient/repliclientindex.php?idmenu=193&amp;mainmenu=repliclient&amp;leftmenu=" title="RepliClient">
// 		<span class="mainmenuaspan">RepliClient</span></a></div></li> 