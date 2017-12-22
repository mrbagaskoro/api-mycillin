<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'controllers/Controlpartner.php';

class UserPartner extends Controlpartner
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('modelpartner', 'ma');
        $segment = $this->uri->segment(2);
    }

    public function test_get()
    {
        //echo 'token test';
        $ini = $this->input->get('id', true);
        echo $ini;
    }

    public function list_partner_booking_post()
    {
        $this->validate_jwt();
        $data = json_decode(file_get_contents('php://input'), true);
        $data = $this->ma->list_partner_booking($data);
        if($data){
            $this->ok($data);
        }else{
            $this->bad_req('Data Is Empty');
        }
    }

    public function detail_token_fcm_post()
    {
        $this->validate_jwt();
        $data = json_decode(file_get_contents('php://input'), true);
        $data = $this->ma->detail_token_fcm($data['user_id']);
        if($data){
            $this->ok($data);
        }else{
            $this->bad_req('Data Is Empty');
        }
        
    }
    
    public function detail_partner_post()
    {
        $this->validate_jwt();
        $data = json_decode(file_get_contents('php://input'), true);
        $data = $this->ma->detail_partner($data['user_id']);
        if($data){
            $this->ok($data);
        }else{
            $this->bad_req('Data Is Empty');
        }
        
    }
    public function token_fcm_post()
    {

        
        $this->validate_jwt();
        $data = json_decode(file_get_contents('php://input'), true);
        // $data = $this->ma->token_fcm($data);

        $user_data = $this->ma->is_valid_user_id($data['user_id']);
    // var_dump($user_data);
    // exit();
        if($user_data){
            $isToken = $this->ma->is_valid_token_fcm($data['user_id']);
            if(!$isToken){
                $this->ma->insert_valid_token_fcm($data['user_id'],$data['token']);
                $this->success('Insert token success');
            }else{
                $this->ma->update_valid_token_fcm($data['user_id'],$data['token']);
                $this->success('Update token success');
            }
            
        }else{
            $this->bad_req('User does not exist');
            
        }
        
    }

    public function change_partner_avatar_post()
    {
        $this->validate_jwt();
        $data = file_get_contents('php://input');
        $user_data = $this->ma->is_valid_user_id($this->post('user_id'));
  
        if ($user_data) {
            $config['upload_path'] = UPLOAD_PATH_PROFILE;
            $config['allowed_types'] = 'jpeg|jpg|png';
            $config['max_size'] = 4096;
            $config['overwrite'] = true;

            $this->load->library('upload', $config);
            if (!empty($_FILES['profile_img']['name'])) {
                $config['file_name'] = 'img_'.$this->post('user_id');
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('profile_img')) {
                    $err = array("result" => $this->upload->display_errors());
                    $this->bad_req($err);
                }

                $up = $this->upload->data();
                $upd['uid'] = $this->post('user_id');
                $upd['file_name'] = $up['file_name'];

                $update = $this->ma->change_avatar($upd);
                if ($update) {
                    $this->success($update);
                } else {
                    $this->bad_req('Changet photo fail, please try again');
                }
            } else {
                $this->bad_req('File can not empty');
            }
        } else {
            $this->bad_req('Account does not exist');
        }
    }

    public function get_partner_avatar_post()
    {
        $this->validate_jwt();
        $data = json_decode(file_get_contents('php://input'), true);

        $user_data = $this->ma->is_valid_user_id($data['user_id']);
  
        if ($user_data) {
            $q = $this->ma->get_avatar($data['user_id']);
            if ($q) {
                $this->success($q);
            } else {
                $this->bad_req('Changet photo fail, please try again');
            }
        } else {
            $this->bad_req('Account does not exist');
        }
    }

    public function change_partner_doc_post()
    {
        $this->validate_jwt();
        $data = file_get_contents('php://input');
        $user_data = $this->ma->is_valid_user_id($this->post('user_id'));
  
        if ($user_data) {
            $config['upload_path'] = UPLOAD_PATH_DOCUMENT;
            $config['allowed_types'] = 'jpeg|jpg|png';
            $config['max_size'] = 4096;
            $config['overwrite'] = true;

            $this->load->library('upload', $config);
            if (!empty($_FILES['profile_img']['name'])) {
                $config['file_name'] = $this->post('type').'_'.$this->post('user_id');
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('profile_img')) {
                    $err = array("result" => $this->upload->display_errors());
                    $this->bad_req($err);
                }

                $up = $this->upload->data();
                $upd['uid'] = $this->post('user_id');
                $upd['file_name'] = $up['file_name'];
                $upd['type'] = $this->post('type');
                

                $update = $this->ma->change_doc($upd);
                if ($update) {
                    $this->success($update);
                } else {
                    $this->bad_req('Changet photo fail, please try again');
                }
            } else {
                $this->bad_req('File can not empty');
            }
        } else {
            $this->bad_req('Account does not exist');
        }
    }

    public function get_partner_doc_post()
    {
        $this->validate_jwt();
        $data = json_decode(file_get_contents('php://input'), true);

        $user_data = $this->ma->is_valid_user_id($data['user_id']);
  
        if ($user_data) {
            $q = $this->ma->get_doc($data);
            if ($q) {
                $this->success($q);
            } else {
                $this->bad_req('Get photo fail, please try again');
            }
        } else {
            $this->bad_req('Account does not exist');
        }
    }

    public function detail_user_post()
    {
        $this->validate_jwt();
        $data = json_decode(file_get_contents('php://input'), true);
        $data = $this->ma->detail_user($data['user_id'], $data['relation_id']);
        $this->ok($data);
    }

    public function change_password_partner_post()
    {
        $this->validate_jwt();
        $data = json_decode(file_get_contents('php://input'), true);

        $old_pass = $data['old_password'];
        $new_pass = $data['new_password'];
        if ($new_pass == $old_pass) {
            $this->bad_req('New password can not same');
        } else {
            $user_full = $this->ma->is_valid_user_id($data['user_id']);
            if ($old_pass == $this->encrypt->decode($user_full->password)) {
                $update_pass = $this->encrypt->encode($new_pass);
                $change = $this->ma->change_password($data['user_id'], $update_pass);
                if ($change) {
                    $this->success('Password changed successfully');
                } else {
                    $this->bad_req('Change password failed');
                }
            } else {
                $this->bad_req('Current password does not match');
            }
        }
    }

    public function toggle_status_partner_post()
    {
        $this->validate_jwt();
        $data = json_decode(file_get_contents('php://input'), true);

        $user_data = $this->ma->is_valid_user_id($data['user_id']);

        if ($user_data) {
            if ($this->ma->toggle_status_account($data)) {
                $this->success('Toggle updated');
            } else {
                $this->bad_req('An error was occured');
            }
        } else {
            $this->bad_req('Account does not exist');
        }
    }

    public function complete_account_partner_post()
    {
        $this->validate_jwt();
        $data = json_decode(file_get_contents('php://input'), true);

        $user_data = $this->ma->is_valid_user_id($data['user_id']);

        if ($user_data) {
            if ($this->ma->complete_account($data)) {
                $this->success('Account completed');
            } else {
                $this->bad_req('An error was occured');
            }
        } else {
            $this->bad_req('Account does not exist');
        }
    }

    function register_partner_post()
    {
        $this->form_validation->set_rules('email', 'EMAIL', 'trim|max_length[50]|required');
        $this->form_validation->set_rules('name', 'NAME', 'trim|max_length[30]|required');
        $this->form_validation->set_rules('password', 'PASSWORD', 'trim|required');
        $this->form_validation->set_rules('mobile_no', 'MOBILE_NO', 'trim|required');

        if ($this->form_validation->run()==false) {
            $this->bad_req($this->validation_errors());
        } else {
            $user_id = random_string('alnum', 15);
            $data = [
            'email'=>$this->post('email', true),
            'full_name'=>$this->post('name', true),
            'password'=>$this->encrypt->encode($this->post('password')),
            'created_by'=>$this->post('email', true),
            'mobile_no'=>$this->post('mobile_no',true),
            'created_date'=>date("Y-m-d H:i:s"),
            'user_id'=>$user_id,
            'status_id'=>'03' /*-------------> harusnya status tetap 3, aktivasi dilakukan oleh admin*/
            ];
            //jika berhasil di masukan maka akan di respon kembali sesuai dengan data yang di masukan
            $user_exist = $this->ma->is_valid_num_user($data['email']);
            if ($user_exist > 0) {
                $this->bad_req('email already registered');
            } else {
                if ($result = $this->ma->register_partner($data)) {
                    $this->email->from(EMAIL_ADDR, 'Lucy@MyCillin', EMAIL_ADDR);
                    $this->email->to($data['email']);
                    $this->email->subject('[noreply] Registrasi anda berhasil');
                    $this->email->set_mailtype("html");

                    $this->email->message('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                            <html xmlns="http://www.w3.org/1999/xhtml">
                            <head>
                              <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                              <meta name="viewport" content="width=device-width, initial-scale=1" />
                              <title>Welcome!</title>
                              <style type="text/css" media="screen"> /* Force Hotmail to display emails at full width */ .ExternalClass {display: block !important; width: 100%; } /* Force Hotmail to display normal line spacing */ .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%; } body, p, h1, h2, h3, h4, h5, h6 {margin: 0; padding: 0; } body, p, td {font-family: Arial, Helvetica, sans-serif; font-size: 15px; color: #333333; line-height: 1.5em; } h1 {font-size: 24px; font-weight: normal; line-height: 24px; } body, p {margin-bottom: 0; -webkit-text-size-adjust: none; -ms-text-size-adjust: none; } img {outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; } a img {border: none; } .background {background-color: #333333; } table.background {margin: 0; padding: 0; width: 100% !important; } .block-img {display: block; line-height: 0; } a {color: white; text-decoration: none; } a, a:link {color: #2A5DB0; text-decoration: underline; } table td {border-collapse: collapse; } td {vertical-align: top; text-align: left; } .wrap {width: 600px; } .wrap-cell {padding-top: 30px; padding-bottom: 30px; } .header-cell, .body-cell, .footer-cell {padding-left: 20px; padding-right: 20px; } .header-cell {background-color: #eeeeee; font-size: 24px; color: #ffffff; } .body-cell {background-color: #ffffff; padding-top: 30px; padding-bottom: 34px; } .footer-cell {background-color: #eeeeee; text-align: center; font-size: 13px; padding-top: 30px; padding-bottom: 30px; } .card {width: 400px; margin: 0 auto; } .data-heading {text-align: right; padding: 10px; background-color: #ffffff; font-weight: bold; } .data-value {text-align: left; padding: 10px; background-color: #ffffff; } .force-full-width {width: 100% !important; } </style> <style type="text/css" media="only screen and (max-width: 600px)"> @media only screen and (max-width: 600px) {body[class*="background"], table[class*="background"], td[class*="background"] {background: #eeeeee !important; } table[class="card"] {width: auto !important; } td[class="data-heading"], td[class="data-value"] {display: block !important; } td[class="data-heading"] {text-align: left !important; padding: 10px 10px 0; } table[class="wrap"] {width: 100% !important; } td[class="wrap-cell"] {padding-top: 0 !important; padding-bottom: 0 !important; } } </style>
                            </head>

                            <body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" bgcolor="" class="background">
                              <table align="center" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" class="background">
                                <tr>
                                  <td align="center" valign="top" width="100%" class="background">
                                    <center>
                                      <table cellpadding="0" cellspacing="0" width="600" class="wrap">
                                        <tr>
                                          <td valign="top" class="wrap-cell" style="padding-top:30px; padding-bottom:30px;">
                                            <table cellpadding="0" cellspacing="0" class="force-full-width">
                                              <tr>
                                               <td height="60" valign="top" class="header-cell">
                                                  <img style="height: 100px;" src="'.FULL_UPLOAD_PATH.'assets/logo_trf.png" alt="mycillin-logo" />
                                                </td>
                                              </tr>
                                              <tr>
                                                <td valign="top" class="body-cell">

                                                  <table cellpadding="0" cellspacing="0" width="100%" bgcolor="#ffffff">
                                                    <tr>
                                                      <td valign="top" style="padding-bottom:15px; background-color:#ffffff;">
                                                        <h1>Welcome to MyCillin</h1>
                                                      </td>
                                                    </tr>
                                                    <tr>
                                                      <td valign="top" style="padding-bottom:20px; background-color:#ffffff;">
                                                        <b>Hi '.$data['full_name'].',</b><br>
                                                        Terima kasih telah mendaftar sebagai partner mycillin (www.mycillin.com). Kini tinggal selangkah lagi bagi anda untuk bergabung bersama ribuan rekan sejawat dengan profesi tenaga medis ke dalam wadah penyedia platform digital medis no 1 di Indonesia!
                                                      </td>
                                                    <tr>
                                                      <td valign="top" style="padding-bottom:20px; background-color:#ffffff;">
                                                        Maksimal tiga hari kedepan, petugas mycillin  akan menghubungi anda untuk melakukan proses verifikasi dan kami berharap anda dapat bekerjasama dengan petugas kami memberikan informasi dan dokumen yang dibutuhkan dalam proses verifikasi tersebut.
                                                      </td>
                                                    </tr>
                                                    <tr>                                                    
                                                    <tr>
                                                      <td valign="top" style="padding-bottom:20px; background-color:#ffffff;">
                                                        <br><b>Pertanyaan?</b><br>
                                                        Informasi lebih lanjut mengenai proses verifikasi serta panduan penggunaan aplikasi mycillin bagi partner, silahkan hubungi petugas kami atau dapat diunduh secara langsung melalui situs resmi kami www.mycillin.com. Kami akan melayani dan memberikan support dengan segenap hati..
                                                      </td>
                                                    </tr>
                                                    <tr>
                                                      <td style="padding-top:10px;background-color:#ffffff;">
                                                        Salam Hangat,<br>
                                                        MyCillin Team
                                                      </td>
                                                    </tr>
                                                  </table>
                                                </td>
                                              </tr>
                                              <tr>
                                                <td valign="top" class="footer-cell">
                                                  www.mycillin.com<br>
                                                  © 2018 All Rights Reserved
                                                </td>
                                              </tr>
                                            </table>
                                          </td>
                                        </tr>
                                      </table>
                                    </center>
                                  </td>
                                </tr>
                              </table>

                            </body>
                            </html>');

                    $this->email->send();

                    $this->success('register success');
                }
            }
        }
    }

    public function partner_activation_post()
    {
        /*$this->validate_jwt();*/
        $data = json_decode(file_get_contents('php://input'), true);

        $user_data = $this->ma->is_valid_user_email($data['email']);
  
        if ($user_data) {
            if ($this->ma->change_user_state($data)) {
                $this->email->from(EMAIL_ADDR, 'Lucy@MyCillin', EMAIL_ADDR);
                    $this->email->to($user_data->email);
                    $this->email->subject('[noreply] Akun mycillin anda telah aktif!');
                    $this->email->set_mailtype("html");

                    $this->email->message('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                            <html xmlns="http://www.w3.org/1999/xhtml">
                            <head>
                              <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                              <meta name="viewport" content="width=device-width, initial-scale=1" />
                              <title>Welcome!</title>
                              <style type="text/css" media="screen"> /* Force Hotmail to display emails at full width */ .ExternalClass {display: block !important; width: 100%; } /* Force Hotmail to display normal line spacing */ .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%; } body, p, h1, h2, h3, h4, h5, h6 {margin: 0; padding: 0; } body, p, td {font-family: Arial, Helvetica, sans-serif; font-size: 15px; color: #333333; line-height: 1.5em; } h1 {font-size: 24px; font-weight: normal; line-height: 24px; } body, p {margin-bottom: 0; -webkit-text-size-adjust: none; -ms-text-size-adjust: none; } img {outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; } a img {border: none; } .background {background-color: #333333; } table.background {margin: 0; padding: 0; width: 100% !important; } .block-img {display: block; line-height: 0; } a {color: white; text-decoration: none; } a, a:link {color: #2A5DB0; text-decoration: underline; } table td {border-collapse: collapse; } td {vertical-align: top; text-align: left; } .wrap {width: 600px; } .wrap-cell {padding-top: 30px; padding-bottom: 30px; } .header-cell, .body-cell, .footer-cell {padding-left: 20px; padding-right: 20px; } .header-cell {background-color: #eeeeee; font-size: 24px; color: #ffffff; } .body-cell {background-color: #ffffff; padding-top: 30px; padding-bottom: 34px; } .footer-cell {background-color: #eeeeee; text-align: center; font-size: 13px; padding-top: 30px; padding-bottom: 30px; } .card {width: 400px; margin: 0 auto; } .data-heading {text-align: right; padding: 10px; background-color: #ffffff; font-weight: bold; } .data-value {text-align: left; padding: 10px; background-color: #ffffff; } .force-full-width {width: 100% !important; } </style> <style type="text/css" media="only screen and (max-width: 600px)"> @media only screen and (max-width: 600px) {body[class*="background"], table[class*="background"], td[class*="background"] {background: #eeeeee !important; } table[class="card"] {width: auto !important; } td[class="data-heading"], td[class="data-value"] {display: block !important; } td[class="data-heading"] {text-align: left !important; padding: 10px 10px 0; } table[class="wrap"] {width: 100% !important; } td[class="wrap-cell"] {padding-top: 0 !important; padding-bottom: 0 !important; } } </style> </head>

                            <body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" bgcolor="" class="background">
                              <table align="center" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" class="background">
                                <tr>
                                  <td align="center" valign="top" width="100%" class="background">
                                    <center>
                                      <table cellpadding="0" cellspacing="0" width="600" class="wrap">
                                        <tr>
                                          <td valign="top" class="wrap-cell" style="padding-top:30px; padding-bottom:30px;">
                                            <table cellpadding="0" cellspacing="0" class="force-full-width">
                                              <tr>
                                               <td height="60" valign="top" class="header-cell">
                                                  <img style="height: 100px;" src="'.FULL_UPLOAD_PATH.'assets/logo_trf.png" alt="mycillin-logo" />
                                                </td>
                                              </tr>
                                              <tr>
                                                <td valign="top" class="body-cell">

                                                  <table cellpadding="0" cellspacing="0" width="100%" bgcolor="#ffffff">
                                                    <tr>
                                                      <td valign="top" style="padding-bottom:15px; background-color:#ffffff;">
                                                        <h1>Selamat Datang di Mycillin</h1>
                                                      </td>
                                                    </tr>
                                                    <tr>
                                                      <td valign="top" style="padding-bottom:20px; background-color:#ffffff;">
                                                        <b>Hi '.$user_data->full_name.',</b><br>
                                                        Selamat, kini anda telah tergabung bersama ribuan rekan sejawat dengan profesi tenaga medis ke dalam keluarga besar mycillin. Account Anda telah siap digunakan. Untuk memulai memberikan layanan, silahkan login menggunakan alamat email yang terdaftar dan mulailah mengatur waktu anda secara effektif bersama mycillin.
                                                      </td>
                                                    </tr>
                                                    <tr>
                                                      <td valign="top" style="padding-bottom:20px; background-color:#ffffff;">
                                                        <b>Informasi akun anda: </b><br>
                                                        Nama Akun : '.$user_data->full_name.'<br>
                                                        Email : '.$user_data->email.'<br>
                                                      </td>
                                                    </tr>
                                                    <tr>
                                                      <td valign="top" style="padding-bottom:20px; background-color:#ffffff;">
                                                        <b>Pertanyaan?</b><br>
                                                        Informasi lebih lanjut mengenai panduan penggunaan aplikasi mycillin bagi partner, silahkan hubungi petugas kami atau dapat diunduh secara langsung melalui situs resmi kami www.mycillin.com. Kami akan melayani dan memberikan support dengan segenap hati.
                                                      </td>
                                                    </tr>
                                                    <tr>
                                                      <td style="padding-top:20px;background-color:#ffffff;">
                                                        Salam Hangat,<br>
                                                        MyCillin Team
                                                      </td>
                                                    </tr>
                                                  </table>
                                                </td>
                                              </tr>
                                              <tr>
                                                <td valign="top" class="footer-cell">
                                                  www.mycillin.com<br>
                                                  © 2018 All Rights Reserved
                                                </td>
                                              </tr>
                                            </table>
                                          </td>
                                        </tr>
                                      </table>
                                    </center>
                                  </td>
                                </tr>
                              </table>
                            </body>
                            </html>');

                $this->email->send();

                $this->success('activation success');
            } else {
                $this->bad_req('An error was occured');
            }
        } else {
            $this->bad_req('Account does not exist');
        }
    }

    public function forgot_password_post()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $user_data = $this->ma->is_valid_user($data['email']);

        if ($user_data) {
            if ($user_data->status_id == '01') {
                if ($new_pass = $this->ma->forgot_password($user_data->user_id)) {
                    // var_dump($new_pass);
                    // exit();
                    $user_full = $this->ma->is_valid_user_id($user_data->user_id);
                    $this->email->from(EMAIL_ADDR, 'Lucy@MyCillin', EMAIL_ADDR);
                    $this->email->to($user_data->email);
                    $this->email->subject('[noreply] MyCillin Password Reset!');
                    $this->email->set_mailtype("html");

                    $this->email->message('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
								<html xmlns="http://www.w3.org/1999/xhtml">
								<head>
								  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
								  <meta name="viewport" content="width=device-width, initial-scale=1" />
								  <title>Welcome!</title>
								  <style type="text/css" media="screen"> /* Force Hotmail to display emails at full width */ .ExternalClass {display: block !important; width: 100%; } /* Force Hotmail to display normal line spacing */ .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%; } body, p, h1, h2, h3, h4, h5, h6 {margin: 0; padding: 0; } body, p, td {font-family: Arial, Helvetica, sans-serif; font-size: 15px; color: #333333; line-height: 1.5em; } h1 {font-size: 24px; font-weight: normal; line-height: 24px; } body, p {margin-bottom: 0; -webkit-text-size-adjust: none; -ms-text-size-adjust: none; } img {outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; } a img {border: none; } .background {background-color: #333333; } table.background {margin: 0; padding: 0; width: 100% !important; } .block-img {display: block; line-height: 0; } a {color: white; text-decoration: none; } a, a:link {color: #2A5DB0; text-decoration: underline; } table td {border-collapse: collapse; } td {vertical-align: top; text-align: left; } .wrap {width: 600px; } .wrap-cell {padding-top: 30px; padding-bottom: 30px; } .header-cell, .body-cell, .footer-cell {padding-left: 20px; padding-right: 20px; } .header-cell {background-color: #eeeeee; font-size: 24px; color: #ffffff; } .body-cell {background-color: #ffffff; padding-top: 30px; padding-bottom: 34px; } .footer-cell {background-color: #eeeeee; text-align: center; font-size: 13px; padding-top: 30px; padding-bottom: 30px; } .card {width: 400px; margin: 0 auto; } .data-heading {text-align: right; padding: 10px; background-color: #ffffff; font-weight: bold; } .data-value {text-align: left; padding: 10px; background-color: #ffffff; } .force-full-width {width: 100% !important; } </style> <style type="text/css" media="only screen and (max-width: 600px)"> @media only screen and (max-width: 600px) {body[class*="background"], table[class*="background"], td[class*="background"] {background: #eeeeee !important; } table[class="card"] {width: auto !important; } td[class="data-heading"], td[class="data-value"] {display: block !important; } td[class="data-heading"] {text-align: left !important; padding: 10px 10px 0; } table[class="wrap"] {width: 100% !important; } td[class="wrap-cell"] {padding-top: 0 !important; padding-bottom: 0 !important; } } </style> </head>

								<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" bgcolor="" class="background">
								  <table align="center" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" class="background">
									<tr>
									  <td align="center" valign="top" width="100%" class="background">
										<center>
										  <table cellpadding="0" cellspacing="0" width="600" class="wrap">
											<tr>
											  <td valign="top" class="wrap-cell" style="padding-top:30px; padding-bottom:30px;">
												<table cellpadding="0" cellspacing="0" class="force-full-width">
												  <tr>
												   <td height="60" valign="top" class="header-cell">
													  <img style="height: 100px;" src="'.FULL_UPLOAD_PATH.'assets/logo_trf.png" alt="mycillin-logo" />
													</td>
												  </tr>
												  <tr>
													<td valign="top" class="body-cell">

													  <table cellpadding="0" cellspacing="0" width="100%" bgcolor="#ffffff">
														<tr>
														  <td valign="top" style="padding-bottom:15px; background-color:#ffffff;">
															<h1>Informasi Pemulihan Password Anda</h1>
														  </td>
														</tr>
														<tr>
														  <td valign="top" style="padding-bottom:20px; background-color:#ffffff;">
															<b>Hi '.$user_full->full_name.',</b><br>
															Anda telah meminta pemulihan paswword login, email ini berisi password login yang baru. Segera login ke aplikasi mycillin anda dan lakukan penggantian password. Segala bentuk penyalahgunaan akses aplikasi menjadi tanggung jawab anda sendiri.
														  </td>
														</tr>
														<tr>
														  <td valign="top" style="padding-bottom:20px; background-color:#ffffff;">
															<b>Informasi Akun Anda : </b><br>
															Nama Akun : '.$user_full->full_name.'<br>
															Email : '.$user_data->email.'<br>
															<b>Password Baru : '.$new_pass.'</b><br>
														  </td>
														</tr>
														<tr>
														  <td valign="top" style="padding-bottom:20px; background-color:#ffffff;">
															<b>Pertanyaan?</b><br>
                                                        Informasi lebih lanjut mengenai panduan penggunaan aplikasi mycillin bagi partner, silahkan hubungi petugas kami atau dapat diunduh secara langsung melalui situs resmi kami www.mycillin.com. Kami akan melayani dan memberikan support dengan segenap hati.
                                                      </td>
                                                    </tr>
                                                    <tr>
                                                      <td style="padding-top:20px;background-color:#ffffff;">
                                                        Salam Hangat,<br>
                                                        MyCillin Team
                                                      </td>
                                                    </tr>
                                                  </table>
                                                </td>
                                              </tr>
                                              <tr>
                                                <td valign="top" class="footer-cell">
                                                  www.mycillin.com<br>
                                                  © 2018 All Rights Reserved
													</td>
												  </tr>
												</table>
											  </td>
											</tr>
										  </table>
										</center>
									  </td>
									</tr>
								  </table>
								</body>
								</html>');

                    $this->email->send();
                    $this->success('Reset password success, please check your email');
                } else {
                    $this->bad_req('Reset password failed');
                }
            } elseif ($user_data->status_id == '02') {
                $this->not_auth('user inactive');
            } else {
                $this->not_auth('user deleted');
            }
        } else {
            //$this->failed_token($email, $password);
            $this->not_auth('invalid email');
        }
    }

    public function partner_loc_autoupdate_post()
    {
          $this->validate_jwt();
          $data = json_decode(file_get_contents('php://input'), true);

          $user_data = $this->ma->is_valid_user_id($data['user_id']);

        if ($user_data) {
            if ($this->ma->partner_loc_autoupdate($data)) {
                $this->success('Location updated successfully');
            } else {
                $this->bad_req('An error was occured');
            }
        } else {
            $this->bad_req('Account does not exist');
        }
    }

   

    public function partner_confirmation_post()
    {
          $this->validate_jwt();
          $data = json_decode(file_get_contents('php://input'), true);

          $user_data = $this->ma->is_valid_user_id($data['user_id']);

        if ($user_data) {
            if ($this->ma->partner_confirmation($data)) {
                $this->success('Partner Confirmation successfully');
            } else {
                $this->bad_req('An error was occured');
            }
        } else {
            $this->bad_req('Account does not exist');
        }
    }

    public function partner_cancel_transaction_post(){
      $this->validate_jwt();
      $data = json_decode(file_get_contents('php://input'), true);

      $user_data = $this->ma->is_valid_user_id($data['user_id']);

      if ($user_data) {
        if ($this->ma->partner_cancel_transaction($data)) {
          $this->success('Transaction Cancelation successfully');
        } else {
          $this->bad_req('An error was occured');
        }
      } else {
        $this->bad_req('Account does not exist');
      }
    }

    public function partner_task_completed_post() 
    {
          $this->validate_jwt();
          $data = json_decode(file_get_contents('php://input'), true);

          $user_data = $this->ma->is_valid_booking_id($data['booking_id']);
          /*var_dump($user_data);
          exit();*/

        if ($user_data) {
            
            if ($this->ma->partner_task_completed($data)) {
                $this->email->from(EMAIL_ADDR, 'Lucy@MyCillin', EMAIL_ADDR);
                    $this->email->to($user_data->email);
                    $this->email->subject('[noreply] Your Mycillin E-Receipt');
                    $this->email->set_mailtype("html");

                    $this->email->message('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                            <html xmlns="http://www.w3.org/1999/xhtml">
                            <head>
                              <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                              <meta name="viewport" content="width=device-width, initial-scale=1" />
                              <title>E-Receipt!</title>
                              <style type="text/css" media="screen"> /* Force Hotmail to display emails at full width */ .ExternalClass {display: block !important; width: 100%; } /* Force Hotmail to display normal line spacing */ .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%; } body, p, h1, h2, h3, h4, h5, h6 {margin: 0; padding: 0; } body, p, td {font-family: Arial, Helvetica, sans-serif; font-size: 15px; color: #333333; line-height: 1.5em; } h1 {font-size: 24px; font-weight: normal; line-height: 24px; } body, p {margin-bottom: 0; -webkit-text-size-adjust: none; -ms-text-size-adjust: none; } img {outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; } a img {border: none; } .background {background-color: #333333; } table.background {margin: 0; padding: 0; width: 100% !important; } .block-img {display: block; line-height: 0; } a {color: white; text-decoration: none; } a, a:link {color: #2A5DB0; text-decoration: underline; } table td {border-collapse: collapse; } td {vertical-align: top; text-align: left; } .wrap {width: 600px; } .wrap-cell {padding-top: 30px; padding-bottom: 30px; } .header-cell, .body-cell, .footer-cell {padding-left: 20px; padding-right: 20px; } .header-cell {background-color: #eeeeee; font-size: 24px; color: #ffffff; } .body-cell {background-color: #ffffff; padding-top: 30px; padding-bottom: 34px; } .footer-cell {background-color: #eeeeee; text-align: center; font-size: 13px; padding-top: 30px; padding-bottom: 30px; } .card {width: 400px; margin: 0 auto; } .data-heading {text-align: right; padding: 10px; background-color: #ffffff; font-weight: bold; } .data-value {text-align: left; padding: 10px; background-color: #ffffff; } .force-full-width {width: 100% !important; } </style> <style type="text/css" media="only screen and (max-width: 600px)"> @media only screen and (max-width: 600px) {body[class*="background"], table[class*="background"], td[class*="background"] {background: #eeeeee !important; } table[class="card"] {width: auto !important; } td[class="data-heading"], td[class="data-value"] {display: block !important; } td[class="data-heading"] {text-align: left !important; padding: 10px 10px 0; } table[class="wrap"] {width: 100% !important; } td[class="wrap-cell"] {padding-top: 0 !important; padding-bottom: 0 !important; } } </style> </head>

                            <body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" bgcolor="" class="background">
                              <table align="center" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" class="background">
                                <tr>
                                  <td align="center" valign="top" width="100%" class="background">
                                    <center>
                                      <table cellpadding="0" cellspacing="0" width="600" class="wrap">
                                        <tr>
                                          <td valign="top" class="wrap-cell" style="padding-top:30px; padding-bottom:30px;">
                                            <table cellpadding="0" cellspacing="0" class="force-full-width">
                                              <tr>
                                               <td height="60" valign="top" class="header-cell">
                                                  <img style="height: 100px;" src="'.FULL_UPLOAD_PATH.'assets/logo_trf.png" alt="mycillin-logo" />
                                                </td>
                                              </tr>
                                              <tr>
                                                <td valign="top" class="body-cell">

                                                  <table cellpadding="0" cellspacing="0" width="100%" bgcolor="#ffffff">
                                                    <tr>
                                                      <td valign="top" style="padding-bottom:15px; background-color:#ffffff;">
                                                        <h1>Transaction E-Receipt</h1>
                                                      </td>
                                                    </tr>
                                                    <tr>
                                                      <td valign="top" style="padding-bottom:20px; background-color:#ffffff;">
                                                        <b>Yth '.$user_data->user_name.',</b><br>
                                                        Semoga jasa pelayanan kesehatan tadi memuaskan !
                                                      </td>
                                                    </tr>
                                                    <tr>
                                                      <td valign="top" style="padding-bottom:20px; background-color:#ffffff;">
                                                        <b>No e-recipt : </b>'.$user_data->booking_id.'<br>
                                                        <b>Diterbitkan Untuk : </b>'.$user_data->user_name.'<br>
                                                      </td>
                                                      </table>
                                                      </tr>
                                                        <b>Detail Pesanan: </b><br>
                                                        Tanggal Pesanan : '.$user_data->created_date.'<br>
                                                        Jenis Layanan : '.$user_data->service_type_desc.'<br>
                                                        Jenis Tindakan : '.$user_data->action_type_desc.'<br>
                                                        Petugas Medis : '.$user_data->partner_name.'<br>
                                                      </td>
                                                      </tr>
                                                       <td valign="top" style="padding-bottom:20px; background-color:#ffffff;">
                                                        <b>Detail Tagihan: </b><br>
                                                        Cara Pembayaran : '.$user_data->pymt_methode_desc.'<br>
                                                        Tarif Layanan : '.$user_data->price_amount.'<br>
                                                        Promo : '.$user_data->promo_code.'<br>
                                                        Total : '.$user_data->price_amount.'<br>
                                                      </td>
                                                    </tr>
                                                    <tr>
                                                      <td valign="top" style="padding-bottom:20px; background-color:#ffffff;">
                                                        <b>Pertanyaan?</b><br>
                                                        Silahkan kontak kami apabila anda memiliki pertanyaan tentang layanan.
                                                      </td>
                                                    <tr>
                                                      <td style="padding-top:20px;background-color:#ffffff;">
                                                        Salam Hangat,<br>
                                                        Mycillin Team
                                                      </td>
                                                    </tr>
                                                  </table>
                                                </td>
                                              </tr>
                                              <tr>
                                                <td valign="top" class="footer-cell">
                                                  www.mycillin.com<br>
                                                  © 2018 All Rights Reserved
                                                </td>
                                              </tr>
                                            </table>
                                          </td>
                                        </tr>
                                      </table>
                                    </center>
                                  </td>
                                </tr>
                              </table>
                            </body>
                            </html>');

                $this->email->send();

                $this->success('Partner Task Completed successfully');
            } else {
                $this->bad_req('An error was occured');
            }
        } else {
            $this->bad_req('Account does not exist');
        }
    }

    public function add_prescription_post()
    {
        $this->validate_jwt();
        $data = json_decode(file_get_contents('php://input'), true);

        $user_data = $this->ma->is_valid_user_id($data['user_id']);
  
        if ($user_data) {
            if ($this->ma->add_prescription($data)) {
                $this->success('Medicine added successfully');
            } else {
                $this->bad_req('An error was occured');
            }
        } else {
            $this->bad_req('Account does not exist');
        }
    }

    public function detail_medical_record1_post()
    {
        $this->validate_jwt();
        $data = json_decode(file_get_contents('php://input'), true);
        $data = $this->ma->detail_medical_record1($data['user_id'], $data['record_id']);
        $this->ok($data);
    }   

    public function list_medical_record1_post()
    {
        $this->validate_jwt();
        $data = json_decode(file_get_contents('php://input'), true);
        $data = $this->ma->list_medical_record1($data['user_id'], $data['relation_id']);
        $this->ok($data);
    }

    public function detail_prescription1_post()
    {
        $this->validate_jwt();
        $data = json_decode(file_get_contents('php://input'), true);
        $data = $this->ma->detail_prescription1($data['prescription_no']);
        $this->ok($data);
    }

    public function partner_top_up_post()
    {
        $this->validate_jwt();
        $data = json_decode(file_get_contents('php://input'), true);

        $user_data = $this->ma->is_valid_user_id($data['user_id']);
  
        if ($user_data) {
            if ($this->ma->partner_top_up($data)) {
                $this->success('Balance added successfully');
            } else {
                $this->bad_req('An error was occured');
            }
        } else {
            $this->bad_req('Account does not exist');
        }
    }

    public function partner_check_transaction_post()
    {
        $this->validate_jwt();
        $data = json_decode(file_get_contents('php://input'), true);
        $data = $this->ma->partner_check_transaction($data['user_id']);
        $this->ok($data);
    }

    public function partner_check_balance_post()
    {
        $this->validate_jwt();
        $data = json_decode(file_get_contents('php://input'), true);
        $data = $this->ma->partner_check_balance($data['user_id']);
        $this->ok($data);
    }

    public function add_prescription_photo_post()
    {
        $this->validate_jwt();
        $data = file_get_contents('php://input');

        $user_data = $this->ma->is_valid_user_id($this->post('user_id'));
  
        if ($user_data) {
            $config['upload_path'] = UPLOAD_PATH_PRESCRIPTION;
            $config['allowed_types'] = 'jpeg|jpg|png';
            $config['max_size'] = 4096;
            $config['overwrite'] = true;

            $this->load->library('upload', $config);
            if (!empty($_FILES['prescription_img']['name'])) {
                $config['file_name'] = 'img_prescription_'.$this->post('booking_id');
                $this->upload->initialize($config);
                if (!$this->upload->do_upload('prescription_img')) {
                    $err = array("result" => $this->upload->display_errors());
                    $this->bad_req($err);
                }

                $up = $this->upload->data();
                $data['user_id'] = $this->post('user_id');
                $data['booking_id'] = $this->post('booking_id');

                $data['prescription_img'] = $up['file_name'];

                if ($this->ma->add_prescription_photo($data)) {
                    $this->success('prescription_img added successfully');
                } else {
                    $this->bad_req('An error was occured');
                }
            } else {
                $this->bad_req('File can not empty');
            }
        } else {
            $this->bad_req('Account does not exist');
        }
    }

    public function list_new_partner_get()
    {
      $data = $this->ma->list_new_partner();
      $this->ok($data);
    }

    public function list_all_partner_get()
    {
      $data = $this->ma->list_all_partner();
      $this->ok($data);
    }

    public function reject_partner_register_post()
    {

        $user_data = $this->ma->is_valid_user_id($data['user_id']);
  
        if ($user_data) {
            if ($this->ma->reject_partner_register($data)) {
                $this->success('Partner Register Rejected successfully');
            } else {
                $this->bad_req('An error was occured');
            }
        } else {
            $this->bad_req('Account does not exist');
        }
    }

    public function suspend_partner_post()
    {

        $user_data = $this->ma->is_valid_user_id($data['user_id']);
  
        if ($user_data) {
            if ($this->ma->suspend_partner($data)) {
                $this->success('Partner Suspended successfully');
            } else {
                $this->bad_req('An error was occured');
            }
        } else {
            $this->bad_req('Account does not exist');
        }
    }

    public function create_clinic_schedule_post()
    {
        $this->validate_jwt();
        $data = json_decode(file_get_contents('php://input'), true);

        $user_data = $this->ma->is_valid_user_id($data['user_id']);

        if ($user_data) {
            if ($this->ma->create_clinic_schedule($data)) {
                $this->success('Clinic schedule added');
            } else {
                $this->bad_req('An error was occured');
            }
        } else {
            $this->bad_req('Account does not exist');
        }
    }

    public function clinic_schedule_update_post()
    {
        $this->validate_jwt();
        $data = json_decode(file_get_contents('php://input'), true);

        $user_data = $this->ma->is_valid_user_id($data['user_id']);

        if ($user_data) {
            if ($this->ma->clinic_schedule_update($data)) {
                $this->success('Clinic schedule updated');
            } else {
                $this->bad_req('An error was occured');
            }
        } else {
            $this->bad_req('Account does not exist');
        }
    }

}
