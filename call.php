<?php
/* Copyright (C) 2007-2017 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2024 SuperAdmin
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 *  \file       suivit_note.php
 *  \ingroup    repliclient
 *  \brief      Tab for notes on Suivit
 */


// General defined Options
//if (! defined('CSRFCHECK_WITH_TOKEN'))     define('CSRFCHECK_WITH_TOKEN', '1');					// Force use of CSRF protection with tokens even for GET
//if (! defined('MAIN_AUTHENTICATION_MODE')) define('MAIN_AUTHENTICATION_MODE', 'aloginmodule');	// Force authentication handler
//if (! defined('MAIN_LANG_DEFAULT'))        define('MAIN_LANG_DEFAULT', 'auto');					// Force LANG (language) to a particular value
//if (! defined('MAIN_SECURITY_FORCECSP'))   define('MAIN_SECURITY_FORCECSP', 'none');				// Disable all Content Security Policies
//if (! defined('NOBROWSERNOTIF'))     		 define('NOBROWSERNOTIF', '1');					// Disable browser notification
//if (! defined('NOIPCHECK'))                define('NOIPCHECK', '1');						// Do not check IP defined into conf $dolibarr_main_restrict_ip
//if (! defined('NOLOGIN'))                  define('NOLOGIN', '1');						// Do not use login - if this page is public (can be called outside logged session). This includes the NOIPCHECK too.
//if (! defined('NOREQUIREAJAX'))            define('NOREQUIREAJAX', '1');       	  		// Do not load ajax.lib.php library
//if (! defined('NOREQUIREDB'))              define('NOREQUIREDB', '1');					// Do not create database handler $db
//if (! defined('NOREQUIREHTML'))            define('NOREQUIREHTML', '1');					// Do not load html.form.class.php
//if (! defined('NOREQUIREMENU'))            define('NOREQUIREMENU', '1');					// Do not load and show top and left menu
//if (! defined('NOREQUIRESOC'))             define('NOREQUIRESOC', '1');					// Do not load object $mysoc
//if (! defined('NOREQUIRETRAN'))            define('NOREQUIRETRAN', '1');					// Do not load object $langs
//if (! defined('NOREQUIREUSER'))            define('NOREQUIREUSER', '1');					// Do not load object $user
//if (! defined('NOSCANGETFORINJECTION'))    define('NOSCANGETFORINJECTION', '1');			// Do not check injection attack on GET parameters
//if (! defined('NOSCANPOSTFORINJECTION'))   define('NOSCANPOSTFORINJECTION', '1');			// Do not check injection attack on POST parameters
//if (! defined('NOSTYLECHECK'))             define('NOSTYLECHECK', '1');					// Do not check style html tag into posted data
//if (! defined('NOTOKENRENEWAL'))           define('NOTOKENRENEWAL', '1');					// Do not roll the Anti CSRF token (used if MAIN_SECURITY_CSRF_WITH_TOKEN is on)


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
if (!$res && file_exists("../main.inc.php")) {
	$res = @include "../main.inc.php";
}
if (!$res && file_exists("../../main.inc.php")) {
	$res = @include "../../main.inc.php";
}
if (!$res && file_exists("../../../main.inc.php")) {
	$res = @include "../../../main.inc.php";
}
if (!$res) {
	die("Include of main fails");
}

dol_include_once('/repliclient/class/suivit.class.php');
dol_include_once('/repliclient/class/demande.class.php');
dol_include_once('/repliclient/lib/repliclient_suivit.lib.php');
dol_include_once('/repliclient/lib/repliclient_suivit.demande.php');

// Load translation files required by the page
$langs->loadLangs(array("repliclient@repliclient", "companies"));

// Get parameters
$id = GETPOST('id', 'int');
$ref        = GETPOST('ref', 'alpha');
$action = GETPOST('action', 'aZ09');
$cancel     = GETPOST('cancel', 'aZ09');
$backtopage = GETPOST('backtopage', 'alpha');

// Initialize technical objects
$object_suivit = new Suivit($db);
$object_demande = new Demande($db);


if (!$object_demande->fetch($id)) {
    accessforbidden($langs->trans("NoRecordFound"));
}




// $extrafields = new ExtraFields($db);
// $diroutputmassaction = $conf->repliclient->dir_output.'/temp/massgeneration/'.$user->id;
// $hookmanager->initHooks(array($object->element.'note', 'globalcard')); // Note that conf->hooks_modules contains array
// Fetch optionals attributes and labels
// $extrafields->fetch_name_optionals_label($object->table_element);

// Load object
// include DOL_DOCUMENT_ROOT.'/core/actions_fetchobject.inc.php'; // Must be include, not include_once  // Must be include, not include_once. Include fetch and fetch_thirdparty but not fetch_optionals
// if ($id > 0 || !empty($ref)) {
// 	// $upload_dir = $conf->repliclient->multidir_output[empty($object->entity) ? $conf->entity : $object->entity]."/".$object->id;
// }


// There is several ways to check permission.
// Set $enablepermissioncheck to 1 to enable a minimum low level of checks
$enablepermissioncheck = 0;
if ($enablepermissioncheck) {
	$permissiontoread = $user->hasRight('repliclient', 'suivit', 'read');
	$permissiontoadd = $user->hasRight('repliclient', 'suivit', 'write');
	$permissionnote = $user->hasRight('repliclient', 'suivit', 'write'); // Used by the include of actions_setnotes.inc.php
} else {
	$permissiontoread = 1;
	$permissiontoadd = 1;
	$permissionnote = 1;
}

// Security check (enable the most restrictive one)
//if ($user->socid > 0) accessforbidden();
//if ($user->socid > 0) $socid = $user->socid;
//$isdraft = (($object->status == $object::STATUS_DRAFT) ? 1 : 0);
//restrictedArea($user, $object->module, $object->id, $object->table_element, $object->element, 'fk_soc', 'rowid', $isdraft);
if (!isModEnabled("repliclient")) {
	accessforbidden();
}
if (!$permissiontoread) {
	accessforbidden();
}


/*
 * Actions
 */



/*
 * View
 */

$form = new Form($db);

$title = $langs->trans('Call').' - '.$langs->trans("call");
$title = $object->ref." - ".$langs->trans("Call");
$help_url = '';


llxHeader('', $title, $help_url, '', 0, 0, '', '', '', 'mod-repliclient');

if ($id > 0 || !empty($ref)) {

	

	print load_fiche_titre($langs->trans("Calling"), '', 'object_'.$object->picto);

   

$url = dol_buildpath('/clicktodial/script/interface.php', 1) . '?action=callnumber&value=' . urlencode($object_demande->telephone);
$url .= '&backurl=' . urlencode($_SERVER["PHP_SELF"] . '?id=' . $id);


	print '<div class="fichecenter">';
	print '<div class="underbanner clearboth"></div>';

    print '<div id="chronometer">00:00:00</div>';
	print '<div class="underbanner clearboth"></div>';
    print '<h2>Appel en cours :'.$object_demande->telephone.'</h2>';
// Formulaire de notes pendant l'appel
print '<form method="POST" action="">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">'; // Ajout du jeton CSRF
print '<input type="hidden" name="callduration" id="callduration" value="">'; // Champ caché pour la durée de l'appel
print '<div>';
print '<label for="callnotes">'.$langs->trans("CallNotes").'</label><br>';
print '<textarea name="callnotes" id="callnotes" rows="5" cols="50"></textarea>';
print '</div>';
// btn action
print '<div class="tabsAction">';
// print dolGetButtonAction('', $langs->trans('EndCall'), 'default', $_SERVER["PHP_SELF"].'?id='.$object_demande->id.'&action=end&token='.newToken(), '');
// print dolGetButtonAction('', $langs->trans('MissedCall'), 'default', $_SERVER["PHP_SELF"].'?id='.$object_demande->id.'&action=missed&token='.newToken(), '');
?>
<button type="submit" name="status" class="button" value="1" onclick="return setCallDuration();">
<?php echo $langs->trans("EndCall"); ?>
</button>
<button type="submit" name="status" class="button" value="2" onclick="return setCallDuration();">
<?php echo $langs->trans("FailCall"); ?>
</button>
<?php
print '</div>';

	print dol_get_fiche_end();

}
?>
<script>
 const startTime = new Date().getTime();
        const chronoDisplay = document.getElementById('chronometer');

        function formatTimeUnit(value) {
            return String(value).padStart(2, '0');
        }

        function updateChronometer() {
            const currentTime = new Date().getTime();
            const elapsedTime = currentTime - startTime;

            const hours = Math.floor(elapsedTime / (1000 * 60 * 60));
            const minutes = Math.floor((elapsedTime % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((elapsedTime % (1000 * 60)) / 1000);

            chronoDisplay.innerHTML = `${formatTimeUnit(hours)}:${formatTimeUnit(minutes)}:${formatTimeUnit(seconds)}`;
            document.getElementById('callduration').value = `${formatTimeUnit(hours)}:${formatTimeUnit(minutes)}:${formatTimeUnit(seconds)}`;
        }

        setInterval(updateChronometer, 1000);

        function setCallDuration() {
            const elapsedTime = new Date().getTime() - startTime;

            const hours = Math.floor(elapsedTime / (1000 * 60 * 60));
            const minutes = Math.floor((elapsedTime % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((elapsedTime % (1000 * 60)) / 1000);

            document.getElementById('callduration').value = `${formatTimeUnit(hours)}:${formatTimeUnit(minutes)}:${formatTimeUnit(seconds)}`;
        }
</script>
<style>
        #chronometer {
            width: 100%;
            font-family: 'Arial', sans-serif;
            font-size: 2rem;
            color: #333;
            background-color: #f1f1f1;
            border: 2px solid #333;
            border-radius: 5px;
            padding: 10px;
            display: inline-block;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
<?php
// End of page
llxFooter();
$db->close();
