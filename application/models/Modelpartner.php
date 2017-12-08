<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ModelPartner extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function detail_token_fcm($user_id)
    {
        $query = $this->db->query("select * from mst_token_fcm where user_id='$user_id'");
        return $query->result();
    }

    public function detail_partner($user_id)
    {
        $query = $this->db->query("select pr.user_id, pa.email, pa.mobile_no, pr.full_name, concat('".FULL_UPLOAD_PATH_PROFILE."', profile_photo) profile_photo, pr.gender, pr.address, pr.dob, pr.no_SIP, pr.SIP_berakhir, concat('".FULL_UPLOAD_PATH_DOCUMENT."', photo_SIP) photo_SIP, pr.no_STR, pr.STR_berakhir, concat('".FULL_UPLOAD_PATH_DOCUMENT."', photo_STR) photo_STR, pr.partner_type_id, pt.partner_type_desc,  pr.spesialisasi_id, ss.spesialisasi_desc, pr.wilayah_kerja, pr.profile_desc, pr.lama_professi, pr.alamat_praktik, pr.nama_institusi, pa.available_id as available_status, pa.visit_id as status_visit, pa.reservasi_id as status_reservasi, pa.consul_id as status_consul, pa.BPJS_RCV_status as status_BPJS, pa.status_id as partner_status, avg(bt.service_rating) as rating from partner_profile pr inner join partner_account pa on pa.user_id=pr.user_id left join mst_partner_type pt on pr.partner_type_id=pt.partner_type_id left join mst_spesialisasi ss on pr.spesialisasi_id=ss.spesialisasi_id left join booking_trx bt on pr.user_id=bt.partner_selected where pr.user_id='$user_id'");
        return $query->result();
    }

    public function update_valid_token_fcm($data,$token)
    {
        $date = date('Y-m-d H:i:s');
        
        $where['user_id'] = $data;
        
        $update['token'] = $token;
        $update['updated_by'] = $data;
        $update['updated_date'] = $date;
        
        $query = $this->db->update('mst_token_fcm', $update, $where);
        return $query?TRUE:FALSE;
    }

    public function insert_valid_token_fcm($data,$token)
    {

        $date = date('Y-m-d H:i:s');

        $insert['created_by'] = $data;
        $insert['created_date'] = $date;
        $insert['user_id'] = $data;
        $insert['token'] = $token;
    
        $query = $this->db->insert('mst_token_fcm', $insert);
        return $query?TRUE:FALSE;
    }

    public function is_valid_token_fcm($user_id)
    {
        $this->db->select('*');
        $this->db->from('mst_token_fcm');
        $this->db->where('user_id', $user_id);
        $query = $this->db->get();
        return $query->row();
    }

    public function is_valid_user($email)
    {
        $this->db->select('*');
        $this->db->from('partner_account');
        $this->db->where('email', $email);
        $query = $this->db->get();
        return $query->row();
    }

    public function is_valid_user_id($user_id)
    {
        $query = $this->db->query("select * from partner_account pa left join partner_profile pp on pa.user_id=pp.user_id where pa.user_id='$user_id' and pp.user_id='$user_id'");
        return $query->row();
    }

    public function is_valid_user_email($email)
    {
        $query = $this->db->query("select * from partner_account pa left join partner_profile pp on pa.user_id=pp.user_id where pa.email='$email'");
        return $query->row();
    }

    public function is_valid_num_user($email)
    {
        $this->db->select('*');
        $this->db->from('partner_account');
        $this->db->where('email', $email);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function is_valid_num_user_id($user_id)
    {
        $this->db->select('*');
        $this->db->from('partner_account');
        $this->db->where('status_id', $user_id);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function is_valid_token($user_id, $token)
    {
        $datetime = date('Y-m-d H:i:s');
        $query = $this->db->query("select * from mst_token where user_id='$user_id' and token='$token' and expired > STR_TO_DATE('$datetime', '%Y-%m-%d %H:%i:%s')");
        return $query?$query->row():false;
    }

    public function change_avatar($data)
    {
        $uid = $data['uid'];
        $where['user_id'] = $data['uid'];
  
        $update['profile_photo'] = $data['file_name'];
  
        $update['updated_by'] = $data['uid'];
  
        $query = $this->db->update('partner_account', $update, $where);
        if ($query) {
            $query = $this->db->query("select user_id, concat('".FULL_UPLOAD_PATH_PROFILE."', profile_photo) image_profile from partner_account where user_id='$uid'");
            return $query->result();
        }
        return false;
    }

    public function get_avatar($data)
    {
        $uid = $data;
        $query = $this->db->query("select user_id, concat('".FULL_UPLOAD_PATH_PROFILE."', profile_photo) image_profile from partner_account where user_id='$uid'");
        // var_dump($query);
        //   exit();
        return $query->result();
    }

    public function change_doc($data)
    {
        $uid = $data['uid'];
        $where['user_id'] = $data['uid'];
  
        $update[$data['type']] = $data['file_name'];
  
        $update['updated_by'] = $data['uid'];
  
        $query = $this->db->update('partner_profile', $update, $where);
        if ($query) {
            $query = $this->db->query("select user_id, concat('".FULL_UPLOAD_PATH_DOCUMENT."', ".$data['type'].") image_profile from partner_profile where user_id='$uid'");
            return $query->result();
        }
        return false;
    }

    public function get_doc($data)
    {
        $uid = $data['user_id'];
        $query = $this->db->query("select user_id, concat('".FULL_UPLOAD_PATH_DOCUMENT."', ".$data['type'].") image_profile from partner_profile where user_id='$uid'");
        // var_dump($query);
        // exit();
        return $query->result();
    }

    public function register_partner($data)
    {
        $date = date('Y-m-d H:i:s');

        $now = new DateTime();
        $now->add(new DateInterval('PT24H'));
        $datetime = $now->format('Y-m-d H:i:s');

        $token = md5($datetime.$data['email']);

        $data_partner = array('created_by'=>$data['user_id'], 'created_date'=>$date, 'user_id'=>$data['user_id'], 'email'=>$data['email'], 'password'=>$data['password'], 'mobile_no'=>$data['mobile_no'], 'status_id'=>$data['status_id']);

        $data_token = array('created_by'=>$data['user_id'], 'created_date'=>$date, 'user_id'=>$data['user_id'], 'token'=>$token, 'expired'=>$datetime);

        $data_relation = array('created_by'=>$data['user_id'], 'created_date'=>$date, 'user_id'=>$data['user_id'],'full_name'=>strtoupper($data['full_name']));

        $this->db->trans_begin();

        $q1 = $this->db->insert('partner_account', $data_partner);
        $q2 = $this->db->insert('partner_profile', $data_relation);
        $q3 = $this->db->insert('mst_token', $data_token);

        if ($q1 && $q2 && $q3) {
            $this->db->trans_commit();
            return $this->is_valid_token($data['user_id'], $token);
        }
        $this->db->trans_rollback();
        return false;
    }

    public function change_user_state($data)
    {
        //$where['email'] = $data['email'];

        $date = date('Y-m-d H:i:s');
        $eml = $data['email'];

        $a = $this->db->query("select user_id from partner_account where email='$eml'")->row();
        $uid = $a->user_id;
        
        $update_status = array('updated_by'=>$uid, 'updated_date'=>$date, 'status_id'=>'01');

        $now = new DateTime();
        $now->add(new DateInterval('P30D'));
        $end_date = $now->format('Y-m-d H:i:s');

        $data_share = array('created_by'=>$uid, 'created_date'=>$date, 'partner_id'=>$uid,'profit_sharing'=>'1', 'start_date'=>$date, 'end_date'=>$end_date);
        
        $this->db->trans_begin();

        $q1 = $this->db->update('partner_account', $update_status);
        $q2 = $this->db->insert('mst_partner_ps', $data_share);
 
        if ($q1 && $q2) {
            $this->db->trans_commit();
            return TRUE;
        }
        $this->db->trans_rollback();
        return FALSE;
    }

    public function forgot_password($user_id)
    {
        $password = random_string('alnum', 6);
        $update['password'] = $this->encrypt->encode($password);

        $where['user_id'] = $user_id;
        $update['updated_by'] = $user_id;

        $result = $this->db->update('partner_account', $update, $where);

        return $password;
    }

    public function toggle_status_account($data)
    {
        $where['user_id'] = $data['user_id'];
        $update[$data['status']] = $data['value'];
        $query = $this->db->update('partner_account', $update, $where);

        return $query?true:false;
    }

    public function change_password($uid, $password)
    {
        $where['user_id'] = $uid;

        $update['password'] = $password;
        $update['updated_by'] = $uid;

        $query = $this->db->update('partner_account', $update, $where);
        return $query?true:false;
    }

    public function detail_user($user_id, $relation_id)
    {
        $query = $this->db->query("select ar.user_id, ar.full_name, ar.relation_id, mr.relation_desc, ar.gender, ar.address, ar.mobile_no, ar.dob, ar.height, ar.weight, ar.blood_type from account_relation ar inner join mst_relation mr on ar.relation_type=mr.relation_id where ar.user_id='$user_id' and ar.relation_id='$relation_id'");
        return $query->result();
    }

    public function list_partner_booking($data)
    {

        if ($data['booking_id']!='') {
            $query = $this->db->query("select bt.created_date as order_date, bt.booking_id, bt.service_type_id, st.service_type_desc, bt.partner_selected, pr.full_name as partner_name, pr.partner_type_id, pr.spesialisasi_id, concat('".FULL_UPLOAD_PATH_PROFILE."', pa.profile_photo) profile_photo, pa.mobile_no, bt.pymt_methode_id, mpm.pymt_methode_desc, bt.promo_code, bt.service_rating, bt.price_amount, bt.partner_profit_share, bt.cancel_by, cr.cancel_reason_desc as cancel_reason_by_user, crp.cancel_reason_desc as cancel_reason_by_partner, mr.diagnosa, mat.action_type_desc, mr.prescription_type_id, concat('".FULL_UPLOAD_PATH_PRESCRIPTION."', mr.prescription_img) prescription_img, bt.booking_status_id, bt.cancel_status from booking_trx bt inner join partner_profile pr on bt.partner_selected=pr.user_id left join mst_service_type st on bt.service_type_id=st.service_type_id left join partner_account pa on bt.partner_selected=pa.user_id left join mst_payment_methode mpm on bt.pymt_methode_id=mpm.pymt_methode_id left join mst_cancel_reason cr on bt.cancel_reason_id=cr.cancel_reason_id left join mst_cancel_reason_partner crp on bt.cancel_reason_id=crp.cancel_reason_id left join medical_record mr on bt.booking_id=mr.booking_id left join mst_action_type mat on bt.action_type_id=mat.action_type_id where bt.partner_selected='".$data['user_id']."' and bt.booking_id=".$data['booking_id']."");
            return $query->result();
        } elseif ($data['booking_status_id']!='' && $data['service_type_id']==''&& $data['booking_id']=='') {
            $query = $this->db->query("select bt.created_date as order_date, bt.booking_id, bt.service_type_id, st.service_type_desc, bt.partner_selected, pr.full_name as partner_name, pr.partner_type_id, pr.spesialisasi_id, concat('".FULL_UPLOAD_PATH_PROFILE."', pa.profile_photo) profile_photo, pa.mobile_no, bt.pymt_methode_id, mpm.pymt_methode_desc, bt.promo_code, bt.service_rating, bt.price_amount, bt.partner_profit_share, bt.cancel_by, cr.cancel_reason_desc as cancel_reason_by_user, crp.cancel_reason_desc as cancel_reason_by_partner, mr.diagnosa, mat.action_type_desc, mr.prescription_type_id, concat('".FULL_UPLOAD_PATH_PRESCRIPTION."', mr.prescription_img) prescription_img, bt.booking_status_id, bt.cancel_status from booking_trx bt inner join partner_profile pr on bt.partner_selected=pr.user_id left join mst_service_type st on bt.service_type_id=st.service_type_id left join partner_account pa on bt.partner_selected=pa.user_id left join mst_payment_methode mpm on bt.pymt_methode_id=mpm.pymt_methode_id left join mst_cancel_reason cr on bt.cancel_reason_id=cr.cancel_reason_id left join mst_cancel_reason_partner crp on bt.cancel_reason_id=crp.cancel_reason_id left join medical_record mr on bt.booking_id=mr.booking_id left join mst_action_type mat on bt.action_type_id=mat.action_type_id where bt.partner_selected='".$data['user_id']."' and bt.booking_status_id=".$data['booking_status_id']."");
            return $query->result();
        } elseif ($data['booking_status_id']!='' && $data['service_type_id']!='') {
            $query = $this->db->query("select bt.created_date as order_date, bt.booking_id, bt.service_type_id, st.service_type_desc, bt.partner_selected, pr.full_name as partner_name, pr.partner_type_id, pr.spesialisasi_id, concat('".FULL_UPLOAD_PATH_PROFILE."', pa.profile_photo) profile_photo, pa.mobile_no, bt.pymt_methode_id, mpm.pymt_methode_desc, bt.promo_code, bt.service_rating, bt.price_amount, bt.partner_profit_share, bt.cancel_by, cr.cancel_reason_desc as cancel_reason_by_user, crp.cancel_reason_desc as cancel_reason_by_partner, mr.diagnosa, mat.action_type_desc, mr.prescription_type_id, concat('".FULL_UPLOAD_PATH_PRESCRIPTION."', mr.prescription_img) prescription_img, bt.booking_status_id, bt.cancel_status from booking_trx bt inner join partner_profile pr on bt.partner_selected=pr.user_id left join mst_service_type st on bt.service_type_id=st.service_type_id left join partner_account pa on bt.partner_selected=pa.user_id left join mst_payment_methode mpm on bt.pymt_methode_id=mpm.pymt_methode_id left join mst_cancel_reason cr on bt.cancel_reason_id=cr.cancel_reason_id left join mst_cancel_reason_partner crp on bt.cancel_reason_id=crp.cancel_reason_id left join medical_record mr on bt.booking_id=mr.booking_id left join mst_action_type mat on bt.action_type_id=mat.action_type_id where bt.partner_selected='".$data['user_id']."' and bt.booking_status_id=".$data['booking_status_id']." and bt.service_type_id=".$data['service_type_id']."");
            return $query->result();
        } elseif ($data['booking_status_id']==''&& $data['service_type_id']!='') {
            $query = $this->db->query("select bt.created_date as order_date, bt.booking_id, bt.service_type_id, st.service_type_desc, bt.partner_selected, pr.full_name as partner_name, pr.partner_type_id, pr.spesialisasi_id, concat('".FULL_UPLOAD_PATH_PROFILE."', pa.profile_photo) profile_photo, pa.mobile_no, bt.pymt_methode_id, mpm.pymt_methode_desc, bt.promo_code, bt.service_rating, bt.price_amount, bt.partner_profit_share, bt.cancel_by, cr.cancel_reason_desc as cancel_reason_by_user, crp.cancel_reason_desc as cancel_reason_by_partner, mr.diagnosa, mat.action_type_desc, mr.prescription_type_id, concat('".FULL_UPLOAD_PATH_PRESCRIPTION."', mr.prescription_img) prescription_img, bt.booking_status_id, bt.cancel_status from booking_trx bt inner join partner_profile pr on bt.partner_selected=pr.user_id left join mst_service_type st on bt.service_type_id=st.service_type_id left join partner_account pa on bt.partner_selected=pa.user_id left join mst_payment_methode mpm on bt.pymt_methode_id=mpm.pymt_methode_id left join mst_cancel_reason cr on bt.cancel_reason_id=cr.cancel_reason_id left join mst_cancel_reason_partner crp on bt.cancel_reason_id=crp.cancel_reason_id left join medical_record mr on bt.booking_id=mr.booking_id left join mst_action_type mat on bt.action_type_id=mat.action_type_id where bt.partner_selected='".$data['user_id']."' and bt.service_type_id=".$data['service_type_id']."");
            return $query->result();
        } else {
            $query = $this->db->query("select bt.created_date as order_date, bt.booking_id, bt.service_type_id, st.service_type_desc, bt.partner_selected, pr.full_name as partner_name, pr.partner_type_id, pr.spesialisasi_id, concat('".FULL_UPLOAD_PATH_PROFILE."', pa.profile_photo) profile_photo, pa.mobile_no, bt.pymt_methode_id, mpm.pymt_methode_desc, bt.promo_code, bt.service_rating, bt.price_amount, bt.partner_profit_share, bt.cancel_by, cr.cancel_reason_desc as cancel_reason_by_user, crp.cancel_reason_desc as cancel_reason_by_partner, mr.diagnosa, mat.action_type_desc, mr.prescription_type_id, concat('".FULL_UPLOAD_PATH_PRESCRIPTION."', mr.prescription_img) prescription_img, bt.booking_status_id, bt.cancel_status from booking_trx bt inner join partner_profile pr on bt.partner_selected=pr.user_id left join mst_service_type st on bt.service_type_id=st.service_type_id left join partner_account pa on bt.partner_selected=pa.user_id left join mst_payment_methode mpm on bt.pymt_methode_id=mpm.pymt_methode_id left join mst_cancel_reason cr on bt.cancel_reason_id=cr.cancel_reason_id left join mst_cancel_reason_partner crp on bt.cancel_reason_id=crp.cancel_reason_id left join medical_record mr on bt.booking_id=mr.booking_id left join mst_action_type mat on bt.action_type_id=mat.action_type_id where bt.partner_selected='".$data['user_id']."'");
            return $query->result();
        }
    }

    public function complete_account($data)
    {
        $where['user_id'] = $data['user_id'];
        $update['full_name'] = strtoupper($data['full_name']);
        $update['gender'] = strtoupper($data['gender']);
        $update['address'] = strtoupper($data['address']);
        $update['dob'] = date("Y-m-d", strtotime($data['dob']));
        $update['no_SIP'] = $data['no_SIP'];
        $update['SIP_berakhir'] = date("Y-m-d", strtotime($data['SIP_berakhir']));
        $update['no_STR'] = $data['no_STR'];
        $update['STR_berakhir'] = date("Y-m-d", strtotime($data['STR_berakhir']));
        $update['partner_type_id'] = $data['partner_type_id'];
        $update['spesialisasi_id'] = $data['spesialisasi_id'];
        $update['wilayah_kerja'] = $data['wilayah_kerja'];
        $update['profile_desc'] = $data['profile_desc'];
        $update['lama_professi'] = $data['lama_professi'];
        $update['alamat_praktik'] = $data['alamat_praktik'];
        $update['latitude_praktik'] = $data['latitude_praktik'];
        $update['longitude_praktik'] = $data['longitude_praktik'];
        $update['nama_institusi'] = $data['nama_institusi'];

        $update['updated_by'] = $data['user_id'];

        $query = $this->db->update('partner_profile', $update, $where);
        return $query?true:false;
    }

    public function partner_loc_autoupdate($data)
    {
        $where['user_id'] = $data['user_id'];

        $update['latitude'] = $data['latitude'];
        $update['longitude'] = $data['longitude'];

        $query = $this->db->update('partner_account', $update, $where);
        return $query?true:false;
    }

    public function update_location($data)
    {
        $where['email'] = $data['email'];

        $update['user_location'] = $data['location'];
        $query = $this->db->update('mst_guess_host', $update, $where);
        return $query?true:false;
    }

    

    public function partner_confirmation($data) 
    {
        $where['booking_id'] = $data['booking_id'];

        $update['actual_partner_loc'] = $data['actual_partner_loc']; 
        $update['booking_status_id'] = "02";
        $update['updated_by'] = $data['user_id'];

        $query = $this->db->update('booking_trx', $update, $where);
        return $query?TRUE:FALSE;
    }

    public function partner_cancel_transaction($data) 
    {
        $where['booking_id'] = $data['booking_id'];

        $update['cancel_status'] = "Y";
        $update['cancel_by'] = $data['user_id'];
        $update['cancel_reason_id'] = $data['cancel_reason_id'];
        $update['updated_by'] = $data['user_id'];
        $query = $this->db->update('booking_trx', $update, $where);
        return $query?TRUE:FALSE;
    }

    public function partner_task_completed($data) 
    {
        $date = date('Y-m-d H:i:s');    
        $test = md5($date.$data['user_id']); //crate random nomor resep obat- test sementara
        $booking = $data['booking_id'];

        $data_transaction = array('action_type_id'=>$data['action_type_id'], 'booking_status_id'=>"04", 'updated_by'=>$data['user_id']);

        $trx_data = $this->db->query("select user_id, relation_id from booking_trx where booking_id='$booking' ")->row();
        $data_record = array('created_by'=>$data['user_id'], 'created_date'=>$date, 'user_id'=>$trx_data->user_id, 'relation_id'=>$trx_data->relation_id, 'partner_id'=>$data['user_id'], 'booking_id'=>$data['booking_id'], 'body_temperature'=>$data['body_temperature'], 'blood_sugar_level'=>$data['blood_sugar_level'], 'cholesterol_level'=>$data['cholesterol_level'], 'blood_press_upper'=>$data['blood_press_upper'], 'blood_press_lower'=>$data['blood_press_lower'], 'patient_condition'=>$data['patient_condition'], 'diagnosa'=>$data['diagnosa'], 'prescription_status'=>$data['prescription_status'], 'prescription_id'=>$test,'prescription_type_id'=>$data['prescription_type_id']);

        $cur_date = date ("Y-m-d");
        $pymt_methode = $this->db->query("select pymt_methode_id from booking_trx where booking_id='$booking'")->row();      
        if ($pymt_methode->pymt_methode_id =='01') {
            $now = new DateTime();
            $now->add(new DateInterval('P5D'));
            $effective_date = $now->format('Y-m-d H:i:s');
        } else {
            $effective_date = $cur_date;
        }
        
        $profit_share = $this->db->query("select partner_profit_share from booking_trx where booking_id=$booking ")->row();
        $wallet_partner = array('created_by'=>$data['user_id'], 'created_date'=>$date, 'user_id'=>$data['user_id'], 'effective_date'=>$effective_date,'transaction_type_id'=>'Honor Pelayanan', 'amount'=>$profit_share->partner_profit_share, 'notes'=>$booking);

        $cur_date1 = date ("Y-m-d");
        $trx_data1 = $this->db->query("select user_id, price_amount from booking_trx where booking_id='$booking' ")->row();
        if ($pymt_methode->pymt_methode_id =='03') {            
            $wallet_user = array('created_by'=>$data['user_id'], 'created_date'=>$date, 'user_id'=>$trx_data1->user_id, 'effective_date'=>$cur_date1,'transaction_type_id'=>'Biaya Pelayanan', 'amount'=>$trx_data1->price_amount *-1, 'notes'=>$booking);
        } else {
            $wallet_user = array('created_by'=>$data['user_id'], 'created_date'=>$date, 'user_id'=>$trx_data1->user_id, 'effective_date'=>$cur_date1,'transaction_type_id'=>'Biaya Pelayanan', 'amount'=>'0', 'notes'=>$booking);
        }
        
        $where['booking_id'] = $data['booking_id'];
        $this->db->trans_begin();

        $q1 = $this->db->update('booking_trx', $data_transaction, $where);
        $q2 = $this->db->insert('medical_record', $data_record);
        $q3 = $this->db->insert('va_balance', $wallet_partner);
        $q4 = $this->db->insert('va_balance', $wallet_user);

        if ($q1 && $q2 && $q3 && $q4) {
            $this->db->trans_commit();
            return TRUE;
        }
        $this->db->trans_rollback();
        return FALSE;
    }


    public function add_prescription($data) {
        $insert['prescription_no'] = $data['prescription_id'];
        $insert['nama_obat'] = $data['nama_obat'];
        $insert['jumlah_obat'] = $data['jumlah_obat'];
        $insert['satuan_id'] = $data['satuan_id'];
        $insert['dosage_id'] = $data['dosage_id'];
        $insert['use_instruction_id'] = $data['use_instruction_id'];
        $insert['created_by'] = $data['user_id'];

        $query = $this->db->insert('prescription_detail', $insert);
        return $query?TRUE:FALSE;
    }

    public function detail_medical_record1($user_id, $record_id)
    {
        $query = $this->db->query("select mr.created_date, mr.partner_id, pr.full_name as partner_name, mr.record_id, mr.user_id, mst.service_type_desc, mr.body_temperature, mr.blood_sugar_level, mr.cholesterol_level, mr.blood_press_upper, mr.blood_press_lower, mr.patient_condition, mr.diagnosa, mr.prescription_status, mr.prescription_id, mpt.prescription_type_desc, concat('".FULL_UPLOAD_PATH_PRESCRIPTION."', mr.prescription_img) prescription_img from medical_record mr inner join partner_profile pr on mr.partner_id=pr.user_id left join mst_service_type mst on mr.service_type_id=mst.service_type_id left join mst_prescription_type mpt on mr.prescription_type_id=mpt.prescription_type_id where mr.user_id='$user_id' and mr.record_id='$record_id'");
      return $query->result();
    }

  public function list_medical_record1($user_id, $relation_id)
  {
        $query = $this->db->query("select mr.created_date, mr.partner_id, pr.full_name as partner_name, mr.record_id, mr.user_id, mst.service_type_desc, mr.body_temperature, mr.blood_sugar_level, mr.cholesterol_level, mr.blood_press_upper, mr.blood_press_lower, mr.patient_condition, mr.diagnosa, mr.prescription_status, mr.prescription_id, mpt.prescription_type_desc, concat('".FULL_UPLOAD_PATH_PRESCRIPTION."', mr.prescription_img) prescription_img from medical_record mr inner join partner_profile pr on mr.partner_id=pr.user_id left join mst_service_type mst on mr.service_type_id=mst.service_type_id left join mst_prescription_type mpt on mr.prescription_type_id=mpt.prescription_type_id  where mr.user_id='$user_id' and mr.relation_id='$relation_id'");
      return $query->result();
  }

  public function detail_prescription1($prescription_no)
  {
        $query = $this->db->query("select pd.created_date, pd.prescription_no, pd.nama_obat, pd.jumlah_obat, so.satuan_obat_desc, do.dosis_obat_desc as dosis_pemakaian from prescription_detail pd inner join mst_satuan_obat so on pd.satuan_id=so.satuan_obat_id left join mst_dosis_obat do on pd.dosage_id=do.dosis_obat_id where pd.prescription_no='$prescription_no'");
      return $query->result();
  }

  public function partner_top_up($data) {
        $cur_date = date ("Y-m-d");
        $insert['transaction_type_id'] = $data['transaction_type_id'];
        $insert['amount'] = $data['amount'];
        $insert['user_id'] = $data['user_id'];
        $insert['created_by'] = $data['user_id'];
        $insert['effective_date'] = $cur_date;

        $query = $this->db->insert('va_balance', $insert);
        return $query?TRUE:FALSE;
    }

    public function partner_check_transaction($user_id)
    {
        $cur_date = date ("Y-m-d");
        $query = $this->db->query("select created_date as transaction_date, transaction_id, transaction_type_id as transaction_desc, notes as referensi_no, amount from va_balance where user_id='$user_id'");
      return $query->result();
    }

    public function partner_check_balance($user_id)
    {
        $cur_date = date ("Y-m-d");
        $query = $this->db->query("select user_id, user_id as virtual_acount_no, sum(amount) as total_saldo, sum(CASE WHEN effective_date <= $cur_date THEN amount ELSE 0 END) as saldo_eff, sum(CASE WHEN effective_date > $cur_date THEN amount ELSE 0 END) as saldo_tertahan from va_balance where user_id='$user_id'");
      return $query->result();
    }

    public function add_prescription_photo($data) {
    $update['updated_by'] = $data['user_id'];
    $update['prescription_img'] = $data['prescription_img'];
    $where['booking_id'] = $data['booking_id'];

    $query = $this->db->update('medical_record', $update, $where);
    return $query?TRUE:FALSE;
  }
}
