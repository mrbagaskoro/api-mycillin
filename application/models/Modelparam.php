<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ModelParam extends CI_Model{

  public function __construct() {
    parent::__construct();
    $this->load->database();
  }

  public function mst_relation() {
    $this->db->select('relation_id, relation_desc');
    $this->db->from('mst_relation');
    $this->db->where('is_active','Y');
    $query = $this->db->get();
    return $query->result();
  }

}