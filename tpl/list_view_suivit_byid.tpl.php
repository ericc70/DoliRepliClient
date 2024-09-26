<?php
	print load_fiche_titre($langs->trans("ListSuivi"), '', 'object_'.$object->picto);
// Affichage des résultats
print '<div class="underbanner clearboth"></div>';
print '<div class="fichehafrightfichehafright"><div class="underbanner clearboth"></div>';
print '<table class="noborder listsuivi" width="100%">';
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
        print '<tr class="oddeven">';
        print '<td>'.dol_print_date($db->jdate($obj->datetime), 'dayhour').'</td>';
        print '<td>'.dol_escape_htmltag($obj->firstname).' '.dol_escape_htmltag($obj->lastname).'</td>';
        print '<td>'.nl2br(dol_escape_htmltag($obj->conterendu)).'</td>';
        print '<td>'.dol_escape_htmltag($obj->duree).' </td>';
        print '<td>';
        if($obj->status == 1) {
             print '<span title="Ouvert" aria-label="Ouvert" class="badge badge-dot badge-status4 classfortooltip badge-status"></span>' ;
        }
        if($obj->status == 2) {
             print '<span title="Absence" aria-label="Absence" class="badge badge-dot badge-status10 classfortooltip badge-status"></span>' ;
        }
        print '</td>';
        print '</tr>';
        $i++;
    }
} else {
    print '<tr><td colspan="5">'.$langs->trans("NoFollowUpFound").'</td></tr>';
}

print '</table></div>';

?>
<style>
   .listsuivi  td{
        padding:7px !important;  
}
</style>

<?php
print dol_get_fiche_head();