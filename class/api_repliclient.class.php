<?php
/* Copyright (C) 2015   Jean-François Ferry     <jfefe@aternatik.fr>
 * Copyright (C) ---Put here your own copyright and developer email---
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

use Luracast\Restler\RestException;

dol_include_once('/repliclient/class/demande.class.php');
dol_include_once('/repliclient/class/source.class.php');
dol_include_once('/repliclient/class/suivit.class.php');



/**
 * \file    htdocs/modulebuilder/template/class/api_mymodule.class.php
 * \ingroup mymodule
 * \brief   File for API management of myobject.
 */

/**
 * API class for mymodule myobject
 *
 * @access protected
 * @class  DolibarrApiAccess {@requires user,external}
 */
class Repliclient extends DolibarrApi
{
	/**
	 * @var Demande $demande {@type Demande}
	 * @var Source $source {@type Source}
	 * @var Suivit $suivit {@type Suivit}
	 */
	public $demande;
	public $source;
	public $suivit;

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
		$this->suivit = new Suivit($this->db);
	}


	/* BEGIN MODULEBUILDER API DEMANDE */
	/**
	 * Get properties of a demande object
	 *
	 * Return an array with demande information
	 *
	 * @param	int		$id				ID of demande
	 * @return  Object					Object with cleaned properties
	 *
	 * @url	GET demandes/{id}
	 *
	 * @throws RestException 403 Not allowed
	 * @throws RestException 404 Not found
	 */
	public function get($id)
	{
		if (!DolibarrApiAccess::$user->hasRight('repliclient', 'demande', 'read')) {
			throw new RestException(403);
		}
		if (!DolibarrApi::_checkAccessToResource('demande', $id, 'repliclient_demande')) {
			throw new RestException(403, 'Access to instance id='.$id.' of object not allowed for login '.DolibarrApiAccess::$user->login);
		}

		$result = $this->demande->fetch($id);
		if (!$result) {
			throw new RestException(404, 'Demande not found');
		}

		return $this->_cleanObjectDatas($this->demande);
	}


	/**
	 * List demandes
	 *
	 * Get a list of demandes
	 *
	 * @param string		   $sortfield			Sort field
	 * @param string		   $sortorder			Sort order
	 * @param int			   $limit				Limit for list
	 * @param int			   $page				Page number
	 * @param string           $sqlfilters          Other criteria to filter answers separated by a comma. Syntax example "(t.ref:like:'SO-%') and (t.date_creation:<:'20160101')"
	 * @param string		   $properties			Restrict the data returned to these properties. Ignored if empty. Comma separated list of properties names
	 * @return  array                               Array of order objects
	 *
	 * @throws RestException 403 Not allowed
	 * @throws RestException 503 System error
	 *
	 * @url	GET /demandes/
	 */
	public function index($sortfield = "t.rowid", $sortorder = 'ASC', $limit = 100, $page = 0, $sqlfilters = '', $properties = '')
	{
		$obj_ret = array();
		$tmpobject = new Demande($this->db);

		if (!DolibarrApiAccess::$user->hasRight('repliclient', 'demande', 'read')) {
			throw new RestException(403);
		}

		$socid = DolibarrApiAccess::$user->socid ? DolibarrApiAccess::$user->socid : 0;

		$restrictonsocid = 0; // Set to 1 if there is a field socid in table of object

		// If the internal user must only see his customers, force searching by him
		$search_sale = 0;
		if ($restrictonsocid && !DolibarrApiAccess::$user->hasRight('societe', 'client', 'voir') && !$socid) {
			$search_sale = DolibarrApiAccess::$user->id;
		}
		if (!isModEnabled('societe')) {
			$search_sale = 0; // If module thirdparty not enabled, sale representative is something that does not exists
		}

		$sql = "SELECT t.rowid";
		$sql .= " FROM ".MAIN_DB_PREFIX.$tmpobject->table_element." AS t";
		$sql .= " LEFT JOIN ".MAIN_DB_PREFIX.$tmpobject->table_element."_extrafields AS ef ON (ef.fk_object = t.rowid)"; // Modification VMR Global Solutions to include extrafields as search parameters in the API GET call, so we will be able to filter on extrafields
		$sql .= " WHERE 1 = 1";
		if ($tmpobject->ismultientitymanaged) {
			$sql .= ' AND t.entity IN ('.getEntity($tmpobject->element).')';
		}
		if ($restrictonsocid && $socid) {
			$sql .= " AND t.fk_soc = ".((int) $socid);
		}
		// Search on sale representative
		if ($search_sale && $search_sale != '-1') {
			if ($search_sale == -2) {
				$sql .= " AND NOT EXISTS (SELECT sc.fk_soc FROM ".MAIN_DB_PREFIX."societe_commerciaux as sc WHERE sc.fk_soc = t.fk_soc)";
			} elseif ($search_sale > 0) {
				$sql .= " AND EXISTS (SELECT sc.fk_soc FROM ".MAIN_DB_PREFIX."societe_commerciaux as sc WHERE sc.fk_soc = t.fk_soc AND sc.fk_user = ".((int) $search_sale).")";
			}
		}
		if ($sqlfilters) {
			$errormessage = '';
			$sql .= forgeSQLFromUniversalSearchCriteria($sqlfilters, $errormessage);
			if ($errormessage) {
				throw new RestException(400, 'Error when validating parameter sqlfilters -> '.$errormessage);
			}
		}

		$sql .= $this->db->order($sortfield, $sortorder);
		if ($limit) {
			if ($page < 0) {
				$page = 0;
			}
			$offset = $limit * $page;

			$sql .= $this->db->plimit($limit + 1, $offset);
		}

		$result = $this->db->query($sql);
		$i = 0;
		if ($result) {
			$num = $this->db->num_rows($result);
			while ($i < $num) {
				$obj = $this->db->fetch_object($result);
				$tmp_object = new Demande($this->db);
				if ($tmp_object->fetch($obj->rowid)) {
					$obj_ret[] = $this->_filterObjectProperties($this->_cleanObjectDatas($tmp_object), $properties);
				}
				$i++;
			}
		} else {
			throw new RestException(503, 'Error when retrieving demande list: '.$this->db->lasterror());
		}

		return $obj_ret;
	}

	/**
	 * Create demande object
	 *
	 * @param array $request_data   Request datas
	 * @return int  				ID of demande
	 *
	 * @throws RestException 403 Not allowed
	 * @throws RestException 500 System error
	 *
	 * @url	POST demandes/
	 */
	public function post($request_data = null)
	{
		if (!DolibarrApiAccess::$user->hasRight('repliclient', 'demande', 'write')) {
			throw new RestException(403);
		}

		// Check mandatory fields
		$result = $this->_validateDemande($request_data);

		foreach ($request_data as $field => $value) {
			if ($field === 'caller') {
				// Add a mention of caller so on trigger called after action, we can filter to avoid a loop if we try to sync back again with the caller
				$this->demande->context['caller'] = sanitizeVal($request_data['caller'], 'aZ09');
				continue;
			}

			if ($field == 'array_options' && is_array($value)) {
				foreach ($value as $index => $val) {
					$this->demande->array_options[$index] = $this->_checkValForAPI('extrafields', $val, $this->demande);
				}
				continue;
			}

			$this->demande->$field = $this->_checkValForAPI($field, $value, $this->demande);
		}

		// Clean data
		// $this->demande->abc = sanitizeVal($this->demande->abc, 'alphanohtml');

		if ($this->demande->create(DolibarrApiAccess::$user)<0) {
			throw new RestException(500, "Error creating Demande", array_merge(array($this->demande->error), $this->demande->errors));
		}
		return $this->demande->id;
	}

	/**
	 * Update demande
	 *
	 * @param 	int   		$id             Id of demande to update
	 * @param 	array 		$request_data   Datas
	 * @return 	Object						Object after update
	 *
	 * @throws RestException 403 Not allowed
	 * @throws RestException 404 Not found
	 * @throws RestException 500 System error
	 *
	 * @url	PUT demandes/{id}
	 */
	public function put($id, $request_data = null)
	{
		if (!DolibarrApiAccess::$user->hasRight('repliclient', 'demande', 'write')) {
			throw new RestException(403);
		}
		if (!DolibarrApi::_checkAccessToResource('demande', $id, 'repliclient_demande')) {
			throw new RestException(403, 'Access to instance id='.$this->demande->id.' of object not allowed for login '.DolibarrApiAccess::$user->login);
		}

		$result = $this->demande->fetch($id);
		if (!$result) {
			throw new RestException(404, 'Demande not found');
		}

		foreach ($request_data as $field => $value) {
			if ($field == 'id') {
				continue;
			}
			if ($field === 'caller') {
				// Add a mention of caller so on trigger called after action, we can filter to avoid a loop if we try to sync back again with the caller
				$this->demande->context['caller'] = sanitizeVal($request_data['caller'], 'aZ09');
				continue;
			}

			if ($field == 'array_options' && is_array($value)) {
				foreach ($value as $index => $val) {
					$this->demande->array_options[$index] = $this->_checkValForAPI('extrafields', $val, $this->demande);
				}
				continue;
			}

			$this->demande->$field = $this->_checkValForAPI($field, $value, $this->demande);
		}

		// Clean data
		// $this->demande->abc = sanitizeVal($this->demande->abc, 'alphanohtml');

		if ($this->demande->update(DolibarrApiAccess::$user, false) > 0) {
			return $this->get($id);
		} else {
			throw new RestException(500, $this->demande->error);
		}
	}

	/**
	 * Delete demande
	 *
	 * @param   int     $id   Demande ID
	 * @return  array
	 *
	 * @throws RestException 403 Not allowed
	 * @throws RestException 404 Not found
	 * @throws RestException 409 Nothing to do
	 * @throws RestException 500 System error
	 *
	 * @url	DELETE demandes/{id}
	 */
	public function delete($id)
	{
		if (!DolibarrApiAccess::$user->hasRight('repliclient', 'demande', 'delete')) {
			throw new RestException(403);
		}
		if (!DolibarrApi::_checkAccessToResource('demande', $id, 'repliclient_demande')) {
			throw new RestException(403, 'Access to instance id='.$this->demande->id.' of object not allowed for login '.DolibarrApiAccess::$user->login);
		}

		$result = $this->demande->fetch($id);
		if (!$result) {
			throw new RestException(404, 'Demande not found');
		}

		if ($this->demande->delete(DolibarrApiAccess::$user) == 0) {
			throw new RestException(409, 'Error when deleting Demande : '.$this->demande->error);
		} elseif ($this->demande->delete(DolibarrApiAccess::$user) < 0) {
			throw new RestException(500, 'Error when deleting Demande : '.$this->demande->error);
		}

		return array(
			'success' => array(
				'code' => 200,
				'message' => 'Demande deleted'
			)
		);
	}


	/**
	 * Validate fields before create or update object
	 *
	 * @param	array		$data   Array of data to validate
	 * @return	array
	 *
	 * @throws	RestException
	 */
	private function _validateDemande($data)
	{
		$demande = array();
		foreach ($this->demande->fields as $field => $propfield) {
			if (in_array($field, array('rowid', 'entity', 'date_creation', 'tms', 'fk_user_creat')) || $propfield['notnull'] != 1) {
				continue; // Not a mandatory field
			}
			if (!isset($data[$field])) {
				throw new RestException(400, "$field field missing");
			}
			$demande[$field] = $data[$field];
		}
		return $demande;
	}

	/* END MODULEBUILDER API DEMANDE */




	/**
	 * Validate fields before create or update object
	 *
	 * @param	array		$data   Array of data to validate
	 * @return	array
	 *
	 * @throws	RestException
	 */
	private function _validateSuivit($data)
	{
		$suivit = array();
		foreach ($this->suivit->fields as $field => $propfield) {
			if (in_array($field, array('rowid', 'entity', 'date_creation', 'tms', 'fk_user_creat')) || $propfield['notnull'] != 1) {
				continue; // Not a mandatory field
			}
			if (!isset($data[$field])) {
				throw new RestException(400, "$field field missing");
			}
			$suivit[$field] = $data[$field];
		}
		return $suivit;
	}

	/* END MODULEBUILDER API SUIVIT */


	/* BEGIN MODULEBUILDER API MYOBJECT */
	/* END MODULEBUILDER API MYOBJECT */



	// phpcs:disable PEAR.NamingConventions.ValidFunctionName.PublicUnderscore
	/**
	 * Clean sensible object datas
	 *
	 * @param   Object  $object     Object to clean
	 * @return  Object              Object with cleaned properties
	 */
	protected function _cleanObjectDatas($object)
	{
		// phpcs:enable
		$object = parent::_cleanObjectDatas($object);

		unset($object->rowid);
		unset($object->canvas);

		// If object has lines, remove $db property
		if (isset($object->lines) && is_array($object->lines) && count($object->lines) > 0) {
			$nboflines = count($object->lines);
			for ($i = 0; $i < $nboflines; $i++) {
				$this->_cleanObjectDatas($object->lines[$i]);

				unset($object->lines[$i]->lines);
				unset($object->lines[$i]->note);
			}
		}

		return $object;
	}



		/**
	 * Create demande object public method
	 *
	 * @param array $request_data   Request datas
	 * @return int  				ID of demande
	 *
	 * @throws RestException 403 Not allowed
	 * @throws RestException 500 System error
	 *
	 * @url	POST submit/
	 */
	public function submit($request_data = null)
	{
		// if (!DolibarrApiAccess::$user->hasRight('repliclient', 'demande', 'write')) {
		// 	throw new RestException(403);
		// }

		// Check mandatory fields
		$result = $this->_validateDemande($request_data);

		foreach ($request_data as $field => $value) {
			if ($field === 'caller') {
				// Add a mention of caller so on trigger called after action, we can filter to avoid a loop if we try to sync back again with the caller
				$this->demande->context['caller'] = sanitizeVal($request_data['caller'], 'aZ09');
				continue;
			}

			if ($field == 'array_options' && is_array($value)) {
				foreach ($value as $index => $val) {
					$this->demande->array_options[$index] = $this->_checkValForAPI('extrafields', $val, $this->demande);
				}
				continue;
			}

			$this->demande->$field = $this->_checkValForAPI($field, $value, $this->demande);
		}

		// Clean data
		$this->demande->abc = sanitizeVal($this->demande->abc, 'alphanohtml');

		// if ($this->demande->create(DolibarrApiAccess::$user)<0) {
		// 	throw new RestException(500, "Error creating Demande", array_merge(array($this->demande->error), $this->demande->errors));
		// }
		return $this->demande->id;
	}


	

}
