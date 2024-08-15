<?php



class QueryDemandeStatus {

    protected $db;
    

    public function __construct($db)
    {

        $this->db = $db;
  
    }

    public function count($status)
{
    try {
        // Préparation de la requête SQL avec un paramètre de sécurité pour éviter les injections SQL
        $sql = "SELECT COUNT(rowid) as nb FROM ".MAIN_DB_PREFIX."repliclient_demande WHERE status = ".(int)$status;

        // Exécution de la requête
        $resql = $this->db->query($sql);

        // Vérifier si la requête a réussi
        if ($resql) {
            // Récupérer et retourner le nombre de résultats
            $result = $this->db->fetch_object($resql);
            return $result->nb;
        } else {
            // Si la requête échoue, lancer une exception
            throw new Exception('Erreur lors de l\'exécution de la requête SQL : ' . $this->db->lasterror());
        }
    } catch (Exception $e) {
        // Capturer l'exception et afficher un message d'erreur
        // Vous pouvez également enregistrer l'erreur dans un fichier de log ou une autre méthode de journalisation
        error_log($e->getMessage());
        return false;
    }
}


}
