<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ModelPatient extends CI_Model{

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

  public function is_valid_user_id($user_id) {
    /*$this->db->select('*');
    $this->db->from('user_account');
    $this->db->where('user_id',$user_id);
    $query = $this->db->get();*/
    $query = $this->db->query("select * from user_account ua left join account_relation ar on ua.user_id=ar.user_id where ua.user_id='$user_id' and ar.relation_type='01'");
    return $query->row();
  }

  public function is_valid_num_user($email) {
    $this->db->select('*');
    $this->db->from('user_account');
    $this->db->where('email',$email);
    $query = $this->db->get();
    return $query->num_rows();
  }

  public function is_valid_num_user_id($user_id) {
    $this->db->select('*');
    $this->db->from('user_account');
    $this->db->where('user_id',$user_id);
    $query = $this->db->get();
    return $query->num_rows();
  }

  public function is_valid_token($user_id,$token) {
    $datetime = date('Y-m-d H:i:s');
    $query = $this->db->query("select * from mst_token where user_id='$user_id' and token='$token' and expired > STR_TO_DATE('$datetime', '%Y-%m-%d %H:%i:%s')");
    return $query?$query->row():FALSE;
  }

  public function register_user($data) {
    $date = date('Y-m-d H:i:s');

    $now = new DateTime();
    $now->add(new DateInterval('PT24H'));
    $datetime = $now->format('Y-m-d H:i:s');

    $token = md5($datetime.$data['email']);

    $data_user = array('created_by'=>$data['user_id'], 'created_date'=>$date, 'user_id'=>$data['user_id'], 'email'=>$data['email'], 'password'=>$data['password'], 'status_id'=>$data['status_id']);

    $data_token = array('created_by'=>$data['user_id'], 'created_date'=>$date, 'user_id'=>$data['user_id'], 'token'=>$token, 'expired'=>$datetime);

    $data_relation = array('created_by'=>$data['user_id'], 'created_date'=>$date, 'user_id'=>$data['user_id'], 'relation_type'=>'01', 'full_name'=>strtoupper($data['full_name']));

    $this->db->trans_begin();

    $q1 = $this->db->insert('user_account',$data_user);    
    $q2 = $this->db->insert('account_relation',$data_relation);
    $q3 = $this->db->insert('mst_token',$data_token);

    if ($q1 && $q2 && $q3) {
      $this->db->trans_commit();
      return $this->is_valid_token($data['user_id'],$token);
    }
    $this->db->trans_rollback();
    return FALSE;
  }

  public function change_user_state($user_id) {
    $query = $this->db->query("update user_account set status_id='01', updated_by='$user_id' where user_id='$user_id'");
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

  public function complete_account($data) {
    $where['user_id'] = $data['user_id'];
    $where['relation_type'] = '01';

    $update['full_name'] = strtoupper($data['full_name']);
    $update['address'] = strtoupper($data['address']);
    $update['mobile_no'] = $data['mobile_number'];
    $update['gender'] = $data['gender'];
    $update['dob'] = date("Y-m-d", strtotime($data['dob']));
    $update['height'] = $data['height'];
    $update['weight'] = $data['weight'];
    $update['blood_type'] = $data['blood_type'];
    $update['insurance_id'] = $data['insurance_id'];

    $update['updated_by'] = $data['user_id'];

    $query = $this->db->update('account_relation', $update, $where);
    return $query?TRUE:FALSE;
  }

  public function add_member($data) {
    $insert['user_id'] = $data['user_id'];
    $insert['relation_type'] = $data['relation_type'];
    $insert['full_name'] = strtoupper($data['full_name']);
    $insert['address'] = strtoupper($data['address']);
    $insert['mobile_no'] = $data['mobile_number'];
    $insert['gender'] = $data['gender'];
    $insert['dob'] = date("Y-m-d", strtotime($data['dob']));
    $insert['height'] = $data['height'];
    $insert['weight'] = $data['weight'];
    $insert['blood_type'] = $data['blood_type'];
    $insert['insurance_id'] = $data['insurance_id'];

    $insert['created_by'] = $data['user_id'];

    $query = $this->db->insert('account_relation', $insert);
    return $query?TRUE:FALSE;
  }

  public function update_member($data) {
    $where['relation_id'] = $data['relation_id'];

    $update['full_name'] = strtoupper($data['full_name']);
    $update['address'] = strtoupper($data['address']);
    $update['mobile_no'] = $data['mobile_number'];
    $update['gender'] = $data['gender'];
    $update['dob'] = date("Y-m-d", strtotime($data['dob']));
    $update['height'] = $data['height'];
    $update['weight'] = $data['weight'];
    $update['blood_type'] = $data['blood_type'];
    $update['insurance_id'] = $data['insurance_id'];

    $update['updated_by'] = $data['user_id'];

    $query = $this->db->update('account_relation', $update, $where);
    return $query?TRUE:FALSE;
  }

  public function list_member($user_id) {
    $query = $this->db->query("select * from account_relation where user_id='$user_id'");
    return $query->result();
  }

  public function update_location($data) {
    $where['email'] = $data['email'];

    $update['user_location'] = $data['location'];
    $query = $this->db->update('mst_guess_host', $update, $where);
    return $query?TRUE:FALSE;
  }

}