<?php 

class Model_attributes extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	/* get the attribute value data */
	
	public function getAttributeValueData($id = null)
	{
		if($id) {
			$sql = "SELECT * FROM attribute_value where id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}
		$sql = "SELECT * FROM attribute_value";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getAttributeValueById($id = null)
	{
		$sql = "SELECT * FROM attribute_value WHERE id = ?";
		$query = $this->db->query($sql, array($id));
		return $query->row_array();
	}

	public function createValue($data)
	{
		if($data) {
			$insert = $this->db->insert('attribute_value', $data);
			return ($insert == true) ? true : false;
		}
	}

	public function updateValue($data, $id)
	{
		if($data && $id) {
			$this->db->where('id', $id);
			$update = $this->db->update('attribute_value', $data);
			return ($update == true) ? true : false;
		}
	}

	public function removeValue($id)
	{
		if($id) {
			$this->db->where('id', $id);
			$delete = $this->db->delete('attribute_value');
			return ($delete == true) ? true : false;
		}
	}

}