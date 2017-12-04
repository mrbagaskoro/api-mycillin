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

    public function mst_cancel_reason() {
    $this->db->select('cancel_reason_id, cancel_reason_desc');
    $this->db->from('mst_cancel_reason');
    $this->db->where('is_active','Y');
    $query = $this->db->get();
    return $query->result();
  }

  public function mst_cancel_reason_partner() {
    $this->db->select('cancel_reason_id, cancel_reason_desc');
    $this->db->from('mst_cancel_reason_partner');
    $this->db->where('is_active','Y');
    $query = $this->db->get();
    return $query->result();
  }

  public function mst_insr_provider() {
    $this->db->select('insr_provider_id, insr_provider_desc');
    $this->db->from('mst_insr_provider');
    $this->db->where('is_active','Y');
    $query = $this->db->get();
    return $query->result();
  }

  public function mst_payment_methode() {
    $this->db->select('pymt_methode_id, pymt_methode_desc');
    $this->db->from('mst_payment_methode');
    $this->db->where('is_active','Y');
    $query = $this->db->get();
    return $query->result();
  }

  public function mst_service_type() {
    $this->db->select('service_type_id, service_type_desc');
    $this->db->from('mst_service_type');
    $this->db->where('is_active','Y');
    $query = $this->db->get();
    return $query->result();
  }

  public function mst_partner_type() {
    $this->db->select('partner_type_id, partner_type_desc');
    $this->db->from('mst_partner_type');
    $this->db->where('is_active','Y');
    $query = $this->db->get();
    return $query->result();
  }

  public function mst_partner_type1($service_type_id) {
    $where = array('is_active' => 'Y', 'service_type_id' => $service_type_id);
    $this->db->select('partner_type_id, partner_type_desc');
    $this->db->from('mst_partner_type');
    $this->db->where($where);
    $query = $this->db->get();
    return $query->result();
  }

  public function mst_spesialisasi($partner_type_id) {
    $where = array('is_active' => 'Y', 'partner_type_id' => $partner_type_id);
    $this->db->select('spesialisasi_id, spesialisasi_desc');
    $this->db->from('mst_spesialisasi');
    $this->db->where($where);
    $query = $this->db->get();
    return $query->result();
  }

  public function mst_dosis_obat() {
    $this->db->select('dosis_obat_id, dosis_obat_desc');
    $this->db->from('mst_dosis_obat');
    $this->db->where('is_active','Y');
    $query = $this->db->get();
    return $query->result();
  }

  public function mst_prescription_type() {
    $this->db->select('prescription_type_id, prescription_type_desc');
    $this->db->from('mst_prescription_type');
    $this->db->where('is_active','Y');
    $query = $this->db->get();
    return $query->result();
  }

  public function mst_use_instruction() {
    $this->db->select('use_instruction_id, use_instruction_desc');
    $this->db->from('mst_use_instruction');
    $this->db->where('is_active','Y');
    $query = $this->db->get();
    return $query->result();
  }

  public function mst_satuan_obat() {
    $this->db->select('satuan_obat_id, satuan_obat_desc');
    $this->db->from('mst_satuan_obat');
    $this->db->where('is_active','Y');
    $query = $this->db->get();
    return $query->result();
  }

  public function dash_kunjungan() {
    $this->db->select('booking_id, created_by, created_date, booking_id, request_location, pymt_methode_id');
    $this->db->from('booking_trx');
    $this->db->where('booking_status_id','01');
    $this->db->where('service_type_id','00');
    $query = $this->db->get();
    return $query->result();
  }

  public function dash_reservasi() {
    $this->db->select('booking_id, created_by, created_date, booking_id, pymt_methode_id');
    $this->db->from('booking_trx');
    $this->db->where('booking_status_id','01');
    $this->db->where('service_type_id','01');
    $query = $this->db->get();
    return $query->result();
  }

  public function dash_konsultasi() {
    $this->db->select('booking_id, created_by, created_date, booking_id, pymt_methode_id');
    $this->db->from('booking_trx');
    $this->db->where('booking_status_id','01');
    $this->db->where('service_type_id','02');
    $query = $this->db->get();
    return $query->result();
  }

  public function todo_inprogress() {
    $this->db->select('booking_id, created_by, created_date, booking_id, service_type_id, pymt_methode_id');
    $this->db->from('booking_trx');
    $this->db->where('booking_status_id','03');
    $query = $this->db->get();
    return $query->result();
  }

  public function todo_completed() {
    $this->db->select('booking_id, created_by, created_date, booking_id, service_type_id, pymt_methode_id');
    $this->db->from('booking_trx');
    $this->db->where('booking_status_id','04');
    $query = $this->db->get();
    return $query->result();
  }

  public function promo_code($promo_code) {
    $curr_date = date('Y-m-d');
    $this->db->select('promo_id, promo_code, discount, start_date, end_date');
    $this->db->from('mst_promo_code');
    $this->db->where('promo_code', $promo_code);
    $this->db->where('start_date <=', $curr_date);
    $this->db->where('end_date >=', $curr_date);
    $query = $this->db->get();
    return $query->result();
  }
}
