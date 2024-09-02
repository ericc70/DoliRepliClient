<?php

/**
 * define method to write, ' write, rewrite, delete)
 * personalisé
 * 
 * 
 */
class CommandDemande  {

   protected $db;

    

    public function __construct($db)
    {
        $this->db = $db;
  
    }


    public function toggleStatut( $newStatut, $id)
    {
        $this->db->begin();

        try {
            $sql = "UPDATE " . MAIN_DB_PREFIX . "repliclient_demande SET status = " . $newStatut . " WHERE rowid = " . $id;

            if ($this->db->query($sql)) {
                $this->db->commit();
                return 1;
            } else {
                $this->db->rollback();
                return 0;
            }
        } catch (Exception $e) {
            // Handle exception
            $this->db->rollback();
            echo "Error updating request: " . $e->getMessage();
            return 0;
        }
    }
    



}