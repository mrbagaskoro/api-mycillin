<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ModelUser extends CI_Model{

  public function __construct() {
    parent::__construct();
    $this->load->database();
  }

  public function is_valid_user($email) {
    $this->db->select('*');
    $this->db->from('user_account');
    $this->db->where('email',$email);
    $query = $this->db->get();
    return $query->row();
  }

  public function is_valid_num_user($email) {
    $this->db->select('*');
    $this->db->from('user_account');
    $this->db->where('email',$email);
    $query = $this->db->get();
    return $query->num_rows();
  }

  public function is_valid_token($email,$token_id) {
    $datetime = date('Y-m-d H:i:s');
    $query = $this->db->query("select * from mst_token where email='$email' and token='$token_id' and expire > STR_TO_DATE('$datetime', '%Y-%m-%d %H:%i:%s')");
    return $query?$query->row():FALSE;
  }

  public function register_user($data) {
    $date = date('Y-m-d H:i:s');

    $now = new DateTime();
    $now->add(new DateInterval('PT24H'));
    $datetime = $now->format('Y-m-d H:i:s');

    $token = md5($datetime.$data['email']);

    $data_token = array('created_by'=>$data['email'], 'created_date'=>$date, 'user_id'=>$data['user_id'], 'token'=>$token, 'expired'=>$datetime);

    $data_realtion = array('created_by'=>$data['email'], 'created_date'=>$date, 'user_id'=>$data['user_id'], 'relation_type'=>'01', 'full_name'=>$data['full_name']);

    $this->db->trans_begin();

    $q1 = $this->db->insert('user_account',$data);    
    $q2 = $this->db->insert('account_relation',$data_relation);
    $q3 = $this->db->insert('mst_token',$data_token);

    if ($q1 && $q2 && q3) {
      $this->db->trans_commit();
      return $this->is_valid_token($data['user_id'],$token);
    }
    $this->db->trans_rollback();
    return FALSE;
  }

  public function change_user_state($email) {
    $query = $this->db->query("update user_account set status_id = '01' where email = '$email'");
    return $query?TRUE:FALSE;
  }

  public function forgot_password($email) {
    $password = random_string('alnum', 6);
    $update['password'] = $this->encrypt->encode($password);

    $where['email'] = $email;

    $result = $this->db->update('user_account', $update, $where);

    return $password;
  }

  public function complete_account($data) {
    $where['email'] = $data['email'];

    $update['user_full_name'] = $data['full_name'];
    $update['user_resident_address'] = $data['address'];
    $update['user_mobile_number'] = $data['mobile_number'];
    $update['user_gender'] = $data['gender'];
    $update['user_date_of_birth'] = date("Y-m-d", strtotime($data['dob']));
    $update['user_height'] = $data['height'];
    $update['user_weight'] = $data['weight'];
    $update['user_blood_type'] = $data['blood_type'];
    //$query = $this->db->query("update mst_guess_host set user_resident_address='jshdfbjhsbdf' where user_email_address = 'native148@gmail.com'");
    $query = $this->db->update('mst_guess_host', $update, $where);
    return $query?TRUE:FALSE;
  }

  public function update_location($data) {
    $where['email'] = $data['email'];

    $update['user_location'] = $data['location'];
    $query = $this->db->update('mst_guess_host', $update, $where);
    return $query?TRUE:FALSE;
  }

}