<?php

use Luracast\Restler\RestException;

dol_include_once('/repliclient/class/demande.class.php');
dol_include_once('/repliclient/class/source.class.php');

/**
 * \file    htdocs/modulebuilder/template/class/api_mymodule.class.php
 * \ingroup mymodule
 * \brief   File for API management of myobject.
 */

/**
 * API class for mymodule myobject
 *
 * @access public
 * @class  DolibarrApiAccess {@requires user,external}
 */
class Repliclient extends DolibarrApi
{
    /**
     * @var Demande $demande {@type Demande}
     * @var Source $source {@type Source}
     */
    public $demande;
    public $source;

    /**
     * Constructor
     *
     * @url     GET /
     *
     */
    public function __construct()
    {
        global $db;
        $this->db = $db;
        $this->demande = new Demande($this->db);
        $this->source = new Source($this->db);
    }

    /* BEGIN MODULEBUILDER API DEMANDE */

    /**
     * Create demande object
     *
     * @param array $request_data   Request datas
     * @return int  				ID of demande
     *
     * @throws RestException 403 Not allowed
     * @throws RestException 500 System error
     *
     * @url    POST submit/
     */
    public function post( $request_data)
    {

        if ($this->isValid($request_data)) {
            if (!$this->isValidSource($request_data)) {
                throw new RestException(403);
            }

            // Sanitize and prepare data for saving
            $datapost = [];
            $datapost['name'] = strip_tags($request_data['request_data']['name']);
            $datapost['telephone'] = strip_tags($request_data['request_data']['telephone']);
            $datapost['raison'] = strip_tags($request_data['request_data']['raison']);
            $datapost['ip'] = strip_tags($_SERVER['REMOTE_ADDR']); // Get the client's IP
            $datapost['datetime'] = date('Y-m-d H:i:s'); // Current datetime
            $datapost['status'] = 10;
            $datapost['fk_source'] = (int)$this->isValidSource($request_data); // Assuming source ID is required

            if($this->save($datapost))
			{
				return array(
					'success' => array(
						'code' => 200,
						'message' => 'succes'
					)
				);
			
			}else{
				return array(
					'success' => array(
						'code' => 400,
						'message' => 'Error'
					)
				);
			
			}



        } else {
            throw new RestException(400, 'Invalid request data');
        }
    }

    protected function isValid(array $data) : bool
    {
        // Validate required fields
        return isset($data['request_data']['name'], $data['request_data']['telephone'], $data['request_data']['raison'], $data['request_data']['authkey']) &&
               !empty($data['request_data']['name']) &&
               !empty($data['request_data']['telephone']) &&
               !empty($data['request_data']['raison']) &&
               !empty($data['request_data']['authkey']);

		// return true;
    }

    protected function isValidSource( $data) : int|bool
    {
		
        $authkey = $this->db->escape($data['request_data']['authkey']);
        $sql = "SELECT rowid FROM " . MAIN_DB_PREFIX . "repliclient_source WHERE keyauth = '$authkey'";

        // Execute the query
        $resql = $this->db->query($sql);
	
       // Check if the query was successful
        if ($resql && $this->db->num_rows($resql) > 0) {
            $obj = $this->db->fetch_object($resql);
	
            return (int) $obj->rowid;

        } else {
            return false;
        }
    }

	protected function save(array $data): int
	{
		// Start the transaction
		$this->db->begin();
		
		try {
			// Escape data for SQL query
			$name = $this->db->escape($data['name']);
			$telephone = $this->db->escape($data['telephone']);
			$raison = $this->db->escape($data['raison']);
			$ip = $this->db->escape($data['ip']);
			$datetime = $this->db->escape($data['datetime']);
			$status = $this->db->escape($data['status']);
			$fk_source = (int)$data['fk_source'];
			
			// Prepare the SQL query
			$sql = "INSERT INTO " . MAIN_DB_PREFIX . "repliclient_demande 
					(name, telephone, raison, ip, datetime, status, fk_source) 
					VALUES ('$name', '$telephone', '$raison', '$ip', '$datetime', '$status', $fk_source)";
			
			// Execute the query
			$resql = $this->db->query($sql);
			
			if ($resql) {
				// Get the last inserted ID
				// $lastId = $this->db->insert_id();
				
				// Commit the transaction
				$this->db->commit();
				
				// Return the ID of the inserted record
				return 1;
			} else {
				// On error, throw an exception with the last error
				throw new Exception("Erreur lors de l'insertion : " . $this->db->lasterror());
			}
		} catch (Exception $e) {
			// Roll back the transaction in case of error
			$this->db->rollback();
			
			// Re-throw the exception to be handled by the caller
			throw new RestException(500, $e->getMessage());
		}
	}
	
}
