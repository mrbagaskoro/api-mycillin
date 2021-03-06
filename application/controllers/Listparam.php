<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'controllers/Controlpatient.php';

class ListParam extends Controlpatient {

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

  public function list_mst_cancel_reason_partner_get(){
    $data = $this->mp->mst_cancel_reason_partner();
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

  public function list_mst_partner_type_post(){
    $data = json_decode(file_get_contents('php://input'), true);
    $data = $this->mp->mst_partner_type1($data['service_type_id']);
    $this->ok($data);
  }

  public function list_mst_spesialisasi_post(){
    $data = json_decode(file_get_contents('php://input'), true);
    $data = $this->mp->mst_spesialisasi($data['partner_type_id']);
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

  public function list_action_type_get(){
    $data = $this->mp->list_action_type();
    $this->ok($data);
  }

  public function list_prescription_type_get(){
    $data = $this->mp->list_prescription_type();
    $this->ok($data);
  }

  public function valid_promo_code_post(){
    $data = json_decode(file_get_contents('php://input'), true);
    $data = $this->mp->promo_code($data['promo_code']);
    $this->ok($data);
  }

}