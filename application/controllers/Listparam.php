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

    public function list_mst_cancel_reason_get(){
    $data = $this->mp->mst_cancel_reason();
    $this->ok($data);
  }

  public function list_mst_insr_provider_get(){
    $data = $this->mp->mst_insr_provider();
    $this->ok($data);
  }

  public function list_mst_payment_methode_get(){
    $data = $this->mp->mst_payment_methode();
    $this->ok($data);
  }

  public function list_mst_service_type_get(){
    $data = $this->mp->mst_service_type();
    $this->ok($data);
  }

  public function list_mst_partner_type_get(){
    $data = $this->mp->mst_partner_type();
    $this->ok($data);
  }

  public function list_mst_spesialisasi_get(){
    $data = $this->mp->mst_spesialisasi();
    $this->ok($data);
  }

  public function list_mst_dosis_obat_get(){
    $data = $this->mp->mst_dosis_obat();
    $this->ok($data);
  }

  public function list_mst_prescription_type_get(){
    $data = $this->mp->mst_prescription_type();
    $this->ok($data);
  }

  public function list_mst_use_instruction_get(){
    $data = $this->mp->mst_use_instruction();
    $this->ok($data);
  }

  public function list_mst_satuan_obat_get(){
    $data = $this->mp->mst_satuan_obat();
    $this->ok($data);
  }
}