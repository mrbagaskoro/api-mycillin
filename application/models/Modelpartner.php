<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ModelPartner extends CI_Model{

  public function __construct() {
    parent::__construct();
    $this->load->database();
  }

  public function is_valid_user($email) {
    $this->db->select('*');
    $this->db->from('partner_account');
    $this->db->where('email',$email);
    $query = $this->db->get();
    return $query->row();
  }

  public function is_valid_user_id($user_id) {
    $query = $this->db->query("select * from partner_account pa left join partner_profile pp on pa.user_id=pp.user_id where pa.user_id='$user_id' and pp.user_id='$user_id'");
    return $query->row();
  }

  public function is_valid_num_user($email) {
    $this->db->select('*');
    $this->db->from('partner_account');
    $this->db->where('email',$email);
    $query = $this->db->get();
    return $query->num_rows();
  }

  public function is_valid_num_user_id($user_id) {
    $this->db->select('*');
    $this->db->from('partner_account');
    $this->db->where('status_id',$user_id);
    $query = $this->db->get();
    return $query->num_rows();
  }

  public function is_valid_token($user_id,$token) {
    $datetime = date('Y-m-d H:i:s');
    $query = $this->db->query("select * from mst_token where user_id='$user_id' and token='$token' and expired > STR_TO_DATE('$datetime', '%Y-%m-%d %H:%i:%s')");
    return $query?$query->row():FALSE;
  }

  public function register_partner($data) {
    $date = date('Y-m-d H:i:s');

    $now = new DateTime();
    $now->add(new DateInterval('PT24H'));
    $datetime = $now->format('Y-m-d H:i:s');

    $token = md5($datetime.$data['email']);

    $data_partner = array('created_by'=>$data['user_id'], 'created_date'=>$date, 'user_id'=>$data['user_id'], 'email'=>$data['email'], 'password'=>$data['password'], 'status_id'=>$data['status_id']);

    $data_token = array('created_by'=>$data['user_id'], 'created_date'=>$date, 'user_id'=>$data['user_id'], 'token'=>$token, 'expired'=>$datetime);

    $data_relation = array('created_by'=>$data['user_id'], 'created_date'=>$date, 'user_id'=>$data['user_id'],'full_name'=>strtoupper($data['full_name']));

    $this->db->trans_begin();

    $q1 = $this->db->insert('partner_account',$data_partner);
    $q2 = $this->db->insert('partner_profile',$data_relation);
    $q3 = $this->db->insert('mst_token',$data_token);

    if ($q1 && $q2 && $q3) {
      $this->db->trans_commit();
      return $this->is_valid_token($data['user_id'],$token);
    }
    $this->db->trans_rollback();
    return FALSE;
  }

  public function change_user_state($user_id) {
    $query = $this->db->query("update partner_account set status_id='1', updated_by='$user_id' where user_id='$user_id'");
    return $query?TRUE:FALSE;
  }

  public function forgot_password($user_id) {
    $password = random_string('alnum', 6);
    $update['password'] = $this->encrypt->encode($password);

    $where['user_id'] = $user_id;
    $update['updated_by'] = $user_id;

    $result = $this->db->update('user_account', $update, $where);

    return $password;
  }

  public function toggle_status_account($data) {
    $where['user_id'] = $data['user_id'];

    $update['available_id'] = $data['available_id'];
    $update['reservasi_id'] = $data['reservasi_id'];
    $update['visit_id'] = $data['visit_id'];
    $update['consul_id'] = $data['consul_id'];

    $update['updated_by'] = $data['user_id'];

    $query = $this->db->update('partner_account', $update, $where);
    return $query?TRUE:FALSE;
  }

  public function detail_user($user_id,$relation_id) {
    $query = $this->db->query("select * from account_relation where user_id='$user_id' and relation_id='$relation_id'");
    return $query->result();
  }

  public function complete_account($data) {
    $where['user_id'] = $data['user_id'];
    $update['full_name'] = strtoupper($data['full_name']);
    $update['gender'] = strtoupper($data['gender']);
    $update['address'] = strtoupper($data['address']);
    $update['dob'] = date("Y-m-d", strtotime($data['dob']));
    $update['no_SIP'] = $data['no_SIP'];
    $update['partner_type_id'] = $data['partner_type_id'];
    $update['spesialisasi_id'] = $data['spesialisasi_id'];
    $update['wilayah_kerja'] = $data['wilayah_kerja'];
    $update['profile_desc'] = $data['profile_desc'];
    $update['lama_professi'] = $data['lama_professi'];
    $update['alamat_praktik'] = $data['alamat_praktik'];
    $update['map_praktik'] = $data['map_praktik'];
    $update['nama_institusi'] = $data['nama_institusi'];

    $update['updated_by'] = $data['user_id'];

    $query = $this->db->update('partner_profile', $update, $where);
    return $query?TRUE:FALSE;
  }


  public function update_location($data) {
    $where['email'] = $data['email'];

    $update['user_location'] = $data['location'];
    $query = $this->db->update('mst_guess_host', $update, $where);
    return $query?TRUE:FALSE;
  }

}
