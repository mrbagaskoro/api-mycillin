<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'controllers/Controlapi.php';

class ListParam extends Controlapi{

  public function __construct() {
    parent::__construct();
    $this->load->model('modelparam','mp');
  }

  public function list_mst_relation_get(){
    $data = $this->mp->mst_relation();
    $this->ok($data);
  }
}