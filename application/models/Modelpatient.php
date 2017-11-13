<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ModelPatient extends CI_Model {

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

  function get_banner_apps(){
      $cur_date = date('Y-m-d');
      $query = $this->db->query("SELECT 
        image_id,
        url_link,
        concat('".FULL_UPLOAD_PATH_BANNER."', image_name) image_name,
        start_date,
        end_date
        FROM
        banner_apps
        WHERE  '$cur_date' BETWEEN start_date AND end_date");
      return $query->result();
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

  public function confirm_account($user_id) {
    $query = $this->db->query("update user_account set status_id='01', updated_by='$user_id' where user_id='$user_id'");
    return $query?TRUE:FALSE;
  }

  public function register_fb($data) {
    $date = date('Y-m-d H:i:s');

    $data_user = array('created_by'=>$data['user_id'], 'created_date'=>$date, 'user_id'=>$data['user_id'], 'email'=>$data['email'], 'password'=>$data['password'], 'profile_photo'=>$data['profile_photo'], 'status_id'=>$data['status_id']);

    $data_relation = array('created_by'=>$data['user_id'], 'created_date'=>$date, 'user_id'=>$data['user_id'], 'relation_type'=>'01', 'full_name'=>strtoupper($data['full_name']));

    $this->db->trans_begin();

    $q1 = $this->db->insert('user_account',$data_user);    
    $q2 = $this->db->insert('account_relation',$data_relation);

    if ($q1 && $q2) {
      $this->db->trans_commit();
      return TRUE;
    }
    $this->db->trans_rollback();
    return FALSE;
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

  public function delete_member($data) {
    $where['relation_id'] = $data['relation_id'];
    $user_id = $data['user_id'];

    $query = $this->db->delete('account_relation', $where);
    if ($query) {
      $query = $this->db->query("select * from account_relation where user_id='$user_id'");
      return $query->result();
    }
    return FALSE;
  }

  public function change_password($uid, $password) {
    $where['user_id'] = $uid;

    $update['password'] = $password;
    $update['updated_by'] = $uid;

    $query = $this->db->update('user_account', $update, $where);
    return $query?TRUE:FALSE;
  }

  public function change_avatar($data) {
    $uid = $data['uid'];
    $where['user_id'] = $data['uid'];

    $update['profile_photo'] = $data['file_name'];

    $update['updated_by'] = $data['uid'];

    $query = $this->db->update('user_account', $update, $where);
    if ($query) {
      $query = $this->db->query("select user_id, concat('".FULL_UPLOAD_PATH_PROFILE."', profile_photo) image_profile from user_account where user_id='$uid'");
      return $query->result();
    }
    return FALSE;
  }

  public function detail_medical_record($user_id, $record_id)
  {
      $query = $this->db->query("select mr.partner_id, pr.full_name as partner_name, mr.record_id, mr.user_id, mst.service_type_desc, mr.body_temperature, mr.blood_sugar_level, mr.cholesterol_level, mr.blood_press_upper, mr.blood_press_lower, mr.patient_condition, mr.diagnosa, mr.prescription_status, mr.prescription_id, mpt.prescription_type_desc from medical_record mr inner join partner_profile pr on mr.partner_id=pr.user_id left join mst_service_type mst on mr.service_type_id=mst.service_type_id left join mst_prescription_type mpt on mr.prescription_type_id=mpt.prescription_type_id where mr.user_id='$user_id' and mr.record_id='$record_id'");
      return $query->result();
  }

  public function list_medical_record($user_id, $relation_id)
  {
      $query = $this->db->query("select mr.partner_id, pr.full_name as partner_name, mr.record_id, mr.user_id, mst.service_type_desc, mr.body_temperature, mr.blood_sugar_level, mr.cholesterol_level, mr.blood_press_upper, mr.blood_press_lower, mr.patient_condition, mr.diagnosa, mr.prescription_status, mr.prescription_id, mpt.prescription_type_desc from medical_record mr inner join partner_profile pr on mr.partner_id=pr.user_id left join mst_service_type mst on mr.service_type_id=mst.service_type_id left join mst_prescription_type mpt on mr.prescription_type_id=mpt.prescription_type_id  where mr.user_id='$user_id' and mr.relation_id='$relation_id'");
      return $query->result();
  }

  public function detail_prescription($prescription_no)
  {
      $query = $this->db->query("select pd.created_date, pd.prescription_no, pd.nama_obat, pd.jumlah_obat, so.satuan_obat_desc, do.dosis_obat_desc as dosis_pemakaian from prescription_detail pd inner join mst_satuan_obat so on pd.satuan_id=so.satuan_obat_id left join mst_dosis_obat do on pd.dosage_id=do.dosis_obat_id where pd.prescription_no='$prescription_no'");
      return $query->result();
  }

  public function get_avatar($data) {
    $uid = $data;
    $query = $this->db->query("select user_id, concat('".FULL_UPLOAD_PATH_PROFILE."', profile_photo) image_profile from user_account where user_id='$uid'");
    return $query->result();
  }

  public function add_member_insurance($data) {
    $insert['user_id'] = $data['user_id'];
    $insert['relation_id'] = $data['relation_id'];
    $insert['no_polis_insr'] = $data['no_polis_insr'];
    $insert['insr_provider_id'] = $insr_id = $data['insr_provider_id'];
    $insert['nama_tertanggung'] = $data['nama_tertanggung'];
    $insert['nama_pemilik_insr'] = $data['nama_pemilik_insr'];
    $insert['created_by'] = $data['user_id'];
    $insert['photo_kartu_insr'] = $data['photo_kartu_insr'];

    $insr = $this->db->query("select insr_provider_id, insr_provider_desc from mst_insr_provider where insr_provider_id='$insr_id' and is_active='Y'")->row();
    $insert['nama_asuransi'] = $insr->insr_provider_desc;

    $query = $this->db->insert('member_insurance', $insert);
    return $query?TRUE:FALSE;
  }
  
  public function list_member_insurance($user_id, $relation_id) 
  {
    $query = $this->db->query("select mi.member_insr_id, mi.user_id, mi.relation_id, mi.no_polis_insr, mi.insr_provider_id, mi.nama_asuransi, mi.nama_tertanggung, mi.nama_pemilik_insr, concat('".FULL_UPLOAD_PATH_INSR."', mi.photo_kartu_insr) img_insr_card from member_insurance mi where user_id='$user_id' and relation_id='$relation_id'");
    return $query->result();
  }

  public function delete_member_insurance($data) {
    $where['member_insr_id'] = $member_insr_id = $data['member_insr_id'];
    $where['user_id'] = $data['user_id'];
    $where['relation_id'] = $data['relation_id'];

    $photo = $this->db->query("select photo_kartu_insr from member_insurance where member_insr_id=$member_insr_id")->row();

    $query = $this->db->delete('member_insurance', $where);
    if ($query) {
      if ($photo->photo_kartu_insr != null) {
        unlink(UPLOAD_PATH_INSR.$photo->photo_kartu_insr);
      }
      return TRUE;
    }
    return FALSE;
  }

  public function change_insurance_photocard($data) {
    $uid = $data['uid'];
    $where['user_id'] = $data['uid'];
    $where['relation_id'] = $data['relation_id'];

    $update['photo_kartu_insr'] = $data['file_name'];

    $update['updated_by'] = $data['uid'];

    $query = $this->db->update('member_insurance', $update, $where);
    if ($query) {
      $query = $this->db->query("select user_id, concat('".FULL_UPLOAD_PATH_PROFILE."', photo_kartu_insr) image_profile from member_insurance where user_id='$uid'");
      return $query->result();
    }
    return FALSE;
  }

  public function get_insurance_photocard($data) {
    $uid = $data;
    $query = $this->db->query("select user_id, concat('".FULL_UPLOAD_PATH_PROFILE."', photo_kartu_insr) image_profile from member_insurance where user_id='$uid'");
    return $query->result();
  }

  public function add_request($data) {
    $insert['user_id'] = $data['user_id'];
    $insert['relation_id'] = $data['relation_id'];
    /*$insert['partner_selected'] = $data['partner_selected'];*/
    $partner_selected = $data['partner_selected'];
    $insert['service_type_id'] = $service_type = $data['service_type_id'];
    $promo_code = $data['promo_code'];
    $insert['pymt_methode_id'] = $pymt_methode = $data['pymt_methode_id'];
    $insert['request_location'] = $data['request_location'];
    $insert['booking_status_id'] = "01";
    $insert['cancel_status'] = "N";

    $partner_type = $data['partner_type_id'];
    $spesialisasi_id = $data['spesialisasi_id'];

    $insert['created_by'] = $data['user_id'];

    $price = $this->db->query("select price_amount from mst_price where service_type_id='$service_type' and pymt_methode_id='$pymt_methode' and partner_type_id='$partner_type' and spesialisasi_id='$spesialisasi_id' ")->row();

    if ($promo_code != null || $promo_code !='') {
      $cur_date = date('Y-m-d');
      $promo = $this->db->query("select promo_code, discount from mst_promo_code where promo_code='$promo_code' and '$cur_date' BETWEEN start_date AND end_date")->row();
      if ($promo != null) {
        $total_price = $price->price_amount-($price->price_amount*$promo->discount);
        $insert["promo_code"]=$data['promo_code'];
      } else {
        $total_price = $price->price_amount;
      }
    } else {
      $total_price = $price->price_amount;
    }

    $insert['price_amount'] = $total_price;


    $cur_date = date('Y-m-d');
    $profit_sharing = $this->db->query("select partner_id, profit_sharing from mst_partner_ps where partner_id='$partner_selected' and '$cur_date' BETWEEN start_date AND end_date")->row();
    
    if ($pymt_methode !='03') {
      $partner_fee = -$total_price*(1-$profit_sharing->profit_sharing);
    } else {   
      $partner_fee = $total_price*($profit_sharing->profit_sharing);
    }

    $insert['partner_profit_share'] = $partner_fee;

    $query = $this->db->insert('booking_trx', $insert);
    return $query?TRUE:FALSE;
  }

  public function user_booking_confirmation($data) {
      $where['booking_id'] = $data['booking_id'];

      $update['booking_status_id'] = "03";
      $update['updated_by'] = $data['user_id'];
      $query = $this->db->update('booking_trx', $update, $where);
      return $query?TRUE:FALSE;
  }

  public function service_price($service_type_id, $pymt_methode_id, $partner_type_id, $spesialisasi_id) {
      $query = $this->db->query("select * from mst_price where service_type_id='$service_type_id' and pymt_methode_id='$pymt_methode_id' and partner_type_id='$partner_type_id' and spesialisasi_id='$spesialisasi_id' ");
      return $query->result();
  }

  public function user_cancel_transaction($data) {
      $where['booking_id'] = $data['booking_id'];

      $update['cancel_status'] = "Y";
      $update['cancel_by'] = $data['user_id'];
      $update['cancel_reason_id'] = $data['cancel_reason_id'];
      $update['updated_by'] = $data['user_id'];
      $query = $this->db->update('booking_trx', $update, $where);
      return $query?TRUE:FALSE;
  }

  public function user_rating_feedback($data) {
      $where['booking_id'] = $data['booking_id'];

      $update['service_rating'] = $data['service_rating'];
      $update['user_comment'] = $data['user_comment'];
      
      $update['updated_by'] = $data['user_id'];
      $query = $this->db->update('booking_trx', $update, $where);
      return $query?TRUE:FALSE;
  }

  public function rating_fill_checking($booking_status_id, $cancel_status, $service_rating) {
      $query = $this->db->query("select * from booking_trx where booking_status_id='04' and cancel_status='N' and service_rating is null ");
      return $query->result();
  }

  public function detail_partner_information($user_id) {
      $query = $this->db->query("select pr.user_id, pr.full_name, concat('".FULL_UPLOAD_PATH_PROFILE."', profile_photo) profile_photo, pr.gender, pr.address, pr.dob, pr.no_SIP, pr.SIP_berakhir, concat('".FULL_UPLOAD_PATH_PROFILE."', photo_SIP) photo_SIP, pr.no_STR, pr.STR_berakhir, concat('".FULL_UPLOAD_PATH_PROFILE."', photo_STR) photo_STR, pr.partner_type_id, pt.partner_type_desc,  pr.spesialisasi_id, ss.spesialisasi_desc, pr.wilayah_kerja, pr.profile_desc, pr.lama_professi, pr.alamat_praktik, pr.nama_institusi from partner_profile pr inner join partner_account pa on pa.user_id=pr.user_id left join mst_partner_type pt on pr.partner_type_id=pt.partner_type_id left join mst_spesialisasi ss on pr.spesialisasi_id=ss.spesialisasi_id where pr.user_id='$user_id'");
      return $query->result();
  }

  public function mst_spesialisasi($partner_type_id) {
    $query = $this->db->query("select * from mst_spesialisasi where partner_type_id='$partner_type_id' and is_active='Y'");
    return $query->result();
  }

  Public function find_partner($user_id, $partner_type_id, $spesialisasi_id, $gender, $request_location)
  {

      /*----------ambil koordinate masing-masing partner--------*/
      
      $partner_loc = $this->db->query("select pa.location_autoupdate from partner_account pa inner join partner_profile pr on pa.user_id=pr.user_id where pr.partner_type_id='$partner_type_id' and pr.spesialisasi_id='$spesialisasi_id' and pr.gender='$gender' and pa.status_id='01' and pa.available_id='1' and pa.visit_id='1' ")->row();

      
      $coord_a = explode(",",$request_location); /*-------pemecahan koordinate requestor--*/
      $coord_b = explode(",",$partner_loc->location_autoupdate); /*-------pemecahan koordinate partner--*/

      /*--------------perhitungan jarak antar 2 koordinate---------------*/
      $theta = $coord_a[0] - $coord_b[0]; 
      $distance = (sin(deg2rad($coord_a[1])) * sin(deg2rad($coord_b[1])))  + (cos(deg2rad($coord_a[1])) * cos(deg2rad($coord_b[1])) * cos(deg2rad($theta))); 
      $distance = acos($distance); 
      $distance = rad2deg($distance); 
      $distance = $distance * 60 * 1.1515; 
      $distance = round($distance * 1.609344,2);

      /*----------pemilihan parner berdasarkan kriteria dan status dan distance--------*/
      $query = $this->db->query("select pa.user_id as partner_id, pr.full_name as nama_partner, pr.wilayah_kerja, pa.location_autoupdate as partner_loc, $distance from partner_account pa inner join partner_profile pr on pa.user_id=pr.user_id where pr.partner_type_id='$partner_type_id' and pr.spesialisasi_id='$spesialisasi_id' and pr.gender='$gender' and pa.status_id='01' and pa.available_id='1' and pa.visit_id='1' and $distance < '10' ");

      return $query->result();
  }
}