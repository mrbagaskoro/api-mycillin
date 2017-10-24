<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Modelapidoc extends CI_Model{

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

  public function is_valid_num_user($email) {
    $this->db->select('*');
    $this->db->from('mst_medical_host');
    $this->db->where('user_email_address',$email);
    $query = $this->db->get();
    return $query->num_rows();
  }

  public function is_valid_token($email,$token_id) {
    $datetime = date('Y-m-d H:i:s');
    $query = $this->db->query("select * from token where email='$email' and token_id='$token_id' and expire > STR_TO_DATE('$datetime', '%Y-%m-%d %H:%i:%s')");
    return $query?$query->row():FALSE;
  }

  public function register_guess_host($data) {
    $date = date('Y-m-d H:i:s');
    $now = new DateTime();
    $now->add(new DateInterval('PT24H'));
    $datetime = $now->format('Y-m-d H:i:s');
    $token = md5($datetime.$data['user_email_address']);

    $data_token = array('user_create'=>$data['user_email_address'], 'date_create'=>$date, 'email'=>$data['user_email_address'], 'token_id'=>$token, 'expire'=>$datetime);

    $this->db->trans_begin();

    $q1 = $this->db->insert('mst_guess_host',$data);
    $q2 = $this->db->insert('token',$data_token);

    if ($q1 && $q2) {
      $this->db->trans_commit();
      return $this->is_valid_token($data['user_email_address'],$token);
    }
    $this->db->trans_rollback();
    return FALSE;
  }

  public function change_user_state($email) {
    $query = $this->db->query("update mst_guess_host set user_status = '1' where user_email_address = '$email'");
    return $query?TRUE:FALSE;
  }

  public function forgot_password($email) {
    $password = random_string('alnum', 6);
    $update['user_password'] = $this->encrypt->encode($password);

    $where['user_email_address'] = $email;

    $result = $this->db->update('mst_medical_host', $update, $where);

    return $password;
  }

  public function get_loc_doc() {
    $this->db->select('*');
    $this->db->from('mst_medical_host');
    $this->db->where('is_available_status','1');
    $query = $this->db->get();
    return $query->result();
  }

  public function update_location($data) {
    $where['user_email_address'] = $data['email'];

    $update['user_location'] = $data['location'];
    $query = $this->db->update('mst_medical_host', $update, $where);
    return $query?TRUE:FALSE;
  }

}
