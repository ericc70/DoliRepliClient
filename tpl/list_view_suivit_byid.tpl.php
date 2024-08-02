<?php
	print load_fiche_titre($langs->trans("ListSuivi"), '', 'object_'.$object->picto);
// Affichage des résultats
print '<div class="underbanner clearboth"></div>';
print '<div class="fichehafrightfichehafright"><div class="underbanner clearboth"></div>';
print '<table class="border" width="100%">';
print '<tr class="liste_titre">';
print '<td>'.$langs->trans("DateTime").'</td>';
print '<td>'.$langs->trans("User").'</td>';
print '<td>'.$langs->trans("Report").'</td>';
print '<td>'.$langs->trans("Duree").'</td>';
print '<td>'.$langs->trans("Status").'</td>';
print '</tr>';

if ($resql) {
    $num = $db->num_rows($resql);
    $i = 0;
    while ($i < $num) {
        $obj = $db->fetch_object($resql);
        print '<tr>';
        print '<td>'.dol_print_date($db->jdate($obj->datetime), 'dayhour').'</td>';
        print '<td>'.dol_escape_htmltag($obj->firstname).' '.dol_escape_htmltag($obj->lastname).'</td>';
        print '<td>'.nl2br(dol_escape_htmltag($obj->conterendu)).'</td>';
        print '<td>'.dol_escape_htmltag($obj->duree).' </td>';
        print '<td>'.dol_escape_htmltag($obj->status).' </td>';
        print '</tr>';
        $i++;
    }
} else {
    print '<tr><td colspan="5">'.$langs->trans("NoFollowUpFound").'</td></tr>';
}

print '</table></div>';

print dol_get_fiche_head();