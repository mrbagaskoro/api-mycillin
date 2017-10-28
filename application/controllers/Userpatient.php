<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'controllers/Controlapi.php';

class UserPatient extends Controlapi{

  public function __construct() {
    parent::__construct();
    $this->load->model('modelpatient','ma');

    $segment = $this->uri->segment(2);
    //echo $segment;
    /*if ($segment != 'tes') {
      # code...      
      $this->validate_jwt();
    }*/
  }

  public function test_get(){
    //echo 'token test';
    $ini = $this->input->get('id', TRUE);
    echo $ini;
  }

  public function change_avatar_post(){
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
          $this->bad_req('Changet photo failed');
        }
      } else {
        $this->bad_req('File can not empty');
      }
    } else {
      $this->bad_req('Account does not exist');
    }
  }

  public function complete_account_post(){
    $this->validate_jwt();
    $data = json_decode(file_get_contents('php://input'), true);

    $user_data = $this->ma->is_valid_user_id($data['user_id']);
  
    if ($user_data) {
      if ($this->ma->complete_account($data)){
        $this->success('Account completed');
      } else {
        $this->bad_req('An error was occured');
      }
    } else {
      $this->bad_req('Account does not exist');
    }
  }

  public function change_password_post(){
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
      } else{
        $this->bad_req('Current password does not match');
      }
    }
  }

  public function add_member_post(){
    $this->validate_jwt();
    $data = json_decode(file_get_contents('php://input'), true);

    $user_data = $this->ma->is_valid_user_id($data['user_id']);
  
    if ($user_data) {
      if ($this->ma->add_member($data)){
        $this->success('Member added successfully');
      } else {
        $this->bad_req('An error was occured');
      }
    } else {
      $this->bad_req('Account does not exist');
    }
  }

  public function update_member_post(){
    $this->validate_jwt();
    $data = json_decode(file_get_contents('php://input'), true);

    $user_data = $this->ma->is_valid_user_id($data['user_id']);
  
    if ($user_data) {
      if ($this->ma->update_member($data)){
        $this->success('Member updated successfully');
      } else {
        $this->bad_req('An error was occured');
      }
    } else {
      $this->bad_req('Account does not exist');
    }
  }

  public function list_member_post(){
    $this->validate_jwt();
    $data = json_decode(file_get_contents('php://input'), true);
    $data = $this->ma->list_member($data['user_id']);
    $this->ok($data);
  }

  public function delete_member_post(){
    $this->validate_jwt();
    $data = json_decode(file_get_contents('php://input'), true);
    $delete = $this->ma->delete_member($data);
    if ($delete) {
      $this->ok($delete);
    } else {
      $this->bad_req('An error was occured');
    }
  }

  public function confirm_account_get(){
    $user_id = $this->input->get('myid', TRUE);
    $token = $this->input->get('activation_code', TRUE);

    $user_data = $this->ma->is_valid_user_id($user_id);

    if ($user_data->status_id == '03') {
      if ($this->ma->is_valid_token($user_data->user_id,$token) != NULL) {
        if ($this->ma->confirm_account($user_data->user_id)) {
          $this->email->from(EMAIL_ADDR, 'Lucy@MyCillin', EMAIL_ADDR);
          $this->email->to($user_data->email);
          $this->email->subject('[noreply] MyCillin Account Activated');
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
                                                        <h1>Welcome to MyCillin</h1>
                                                      </td>
                                                    </tr>
                                                    <tr>
                                                      <td valign="top" style="padding-bottom:20px; background-color:#ffffff;">
                                                        <b>Hi '.$user_data->full_name.',</b><br>
                                                        Thank you for your confirmation. Your MyCillin account is ready to use. Please login using your email and start growing with us.
                                                      </td>
                                                    </tr>
                                                    <tr>
                                                      <td valign="top" style="padding-bottom:20px; background-color:#ffffff;">
                                                        <b>Your Account Information : </b><br>
                                                        Account name : '.$user_data->full_name.'<br>
                                                        Email : '.$user_data->email.'<br>
                                                      </td>
                                                    </tr>
                                                    <tr>
                                                      <td valign="top" style="padding-bottom:20px; background-color:#ffffff;">
                                                        <b>Questions?</b><br>
                                                        Feel free to contact us anytime for questions about our services.
                                                      </td>
                                                    </tr>
                                                    <tr>
                                                      <td style="padding-top:20px;background-color:#ffffff;">
                                                        Sincerely,<br>
                                                        MyCillin Team
                                                      </td>
                                                    </tr>
                                                  </table>
                                                </td>
                                              </tr>
                                              <tr>
                                                <td valign="top" class="footer-cell">
                                                  MyCillin<br>
                                                  © 2017 All Rights Reserved
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
          $this->not_auth('activation failed');
        }
      } else {
        $this->not_auth('activation expired');
      }
    } else if ($user_data->status_id == '01') {
      $this->not_auth('user already activated');
    } else if ($user_data->status_id == '02') {
      $this->not_auth('user inactive');
    } else {
      $this->not_auth('user deleted');
    }
  }

  function register_user_post(){
    $this->form_validation->set_rules('email','EMAIL','trim|max_length[50]|required');
    $this->form_validation->set_rules('name','NAME','trim|max_length[30]|required');
    $this->form_validation->set_rules('password','PASSWORD','trim|required');

    if ($this->form_validation->run()==FALSE) {
      $this->bad_req($this->validation_errors());
    } else {
      $user_id = random_string('alnum', 15);
      $data = [
        'email'=>$this->post('email',TRUE),
        'full_name'=>$this->post('name',TRUE),
        'password'=>$this->encrypt->encode($this->post('password')),
        'created_by'=>$this->post('email',TRUE),
        'created_date'=>date("Y-m-d H:i:s"),
        'user_id'=>$user_id,
        'status_id'=>'03'
      ];
      //jika berhasil di masukan maka akan di respon kembali sesuai dengan data yang di masukan
      $user_exist = $this->ma->is_valid_num_user($data['email']);
      if ($user_exist > 0) {
        $this->bad_req('email already registered');
      } else {
        if ($result = $this->ma->register_user($data)) {

          $this->email->from(EMAIL_ADDR, 'Lucy@MyCillin', EMAIL_ADDR);
          $this->email->to($data['email']);
          $this->email->subject('[noreply] MyCillin Account Activation');
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
                                                        Thanks for registering on MyCillin (www.mycillin.com). You are just one step more to becoming an active member on the world\'s leading penpal site!
                                                      </td>
                                                    <tr>
                                                      <td valign="top" style="padding-bottom:20px; background-color:#ffffff;">
                                                        To activate your account, click this button below :
                                                      </td>
                                                    </tr>
                                                    <tr>
                                                      <td>
                                                        <table cellspacing="0" cellpadding="0" width="100%" bgcolor="#ffffff">
                                                          <tr>
                                                            <td style="width:200px;background:#008000;">
                                                              <div><!--[if mso]>
                                                                <v:rect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="#" style="height:40px;v-text-anchor:middle;width:200px;" stroke="f" fillcolor="#008000">
                                                                  <w:anchorlock/>
                                                                  <center>
                                                                <![endif]-->
                                                                    <a href="'.base_url().'api/activation?myid='.$user_id.'&activation_code='.$result->token.'"
                                                              style="background-color:#008000;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:18px;line-height:40px;text-align:center;text-decoration:none;width:200px;-webkit-text-size-adjust:none;">Activate Account</a>
                                                                <!--[if mso]>
                                                                  </center>
                                                                </v:rect>
                                                              <![endif]--></div>
                                                            </td>
                                                            <td width="360" style="background-color:#ffffff; font-size:0; line-height:0;">&nbsp;</td>
                                                          </tr>
                                                        </table>
                                                      </td>
                                                    </tr>
                                                    <tr>
                                                      <td valign="top" style="padding-bottom:20px; background-color:#ffffff;">
                                                        or copy and paste this link into your browser :
                                                        <p>'.base_url().'api/activation?myid='.$user_id.'&activation_code='.$result->token.'</p>
                                                      </td>
                                                    </tr>
                                                    <tr>
                                                      <td valign="top" style="padding-bottom:20px; background-color:#ffffff;">
                                                        <br><b>Questions?</b><br>
                                                        Feel free to contact us anytime for questions about our services.
                                                      </td>
                                                    </tr>
                                                    <tr>
                                                      <td style="padding-top:10px;background-color:#ffffff;">
                                                        Sincerely,<br>
                                                        MyCillin Team
                                                      </td>
                                                    </tr>
                                                    <tr>
                                                      <td style="padding-top:20px;background-color:#ffffff;">
                                                        <p style="text-align: justify; color: grey; padding-left: 10px">This message was sent to '.$data['email'].'.
                                                        You are receiving this email because you have chosen to be notified when someone contacts you on your
                                                        MyCillin account. If you do not wish to receive this type of email from MyCillin in the future,
                                                        please click here to unsubscribe. MyCillin, Jl Baru Pemda Bambu Kuning No.15 Cibinong, West Java, Indonesia - 16922.</p>
                                                      </td>
                                                    </tr>
                                                  </table>
                                                </td>
                                              </tr>
                                              <tr>
                                                <td valign="top" class="footer-cell">
                                                  MyCillin.com<br>
                                                  © 2017 All Rights Reserved
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
  
  public function forgot_password_post(){
	$data = json_decode(file_get_contents('php://input'), true);

  $user_data = $this->ma->is_valid_user($data['email']);

  $user_full = $this->ma->is_valid_user_id($user_data->user_id);
	
	if ($user_data) {
		if ($user_data->status_id == '01') {
			if ($new_pass = $this->ma->forgot_password($user_full->user_id)){
				$this->email->from(EMAIL_ADDR, 'Lucy@MyCillin', EMAIL_ADDR);
			  $this->email->to($user_data->email);
			  $this->email->subject('[noreply] MyCillin Password Reset');
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
															<h1>Welcome to MyCillin</h1>
														  </td>
														</tr>
														<tr>
														  <td valign="top" style="padding-bottom:20px; background-color:#ffffff;">
															<b>Hi '.$user_full->full_name.',</b><br>
															You are requesting password reset, and this email contain your new password. Please login and change your password.
														  </td>
														</tr>
														<tr>
														  <td valign="top" style="padding-bottom:20px; background-color:#ffffff;">
															<b>Your Account Information : </b><br>
															Account name : '.$user_full->full_name.'<br>
															Email : '.$user_data->email.'<br>
															<b>Your New Password : '.$new_pass.'</b><br>
														  </td>
														</tr>
														<tr>
														  <td valign="top" style="padding-bottom:20px; background-color:#ffffff;">
															<b>Questions?</b><br>
															Feel free to contact us anytime for questions about our services.
														  </td>
														</tr>
														<tr>
														  <td style="padding-top:20px;background-color:#ffffff;">
															Sincerely,<br>
															MyCillin Team
														  </td>
														</tr>
													  </table>
													</td>
												  </tr>
												  <tr>
													<td valign="top" class="footer-cell">
													  MyCillin<br>
													  © 2017 All Rights Reserved
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
		} else if ($user_data->user_status == '02') {
		  $this->not_auth('user inactive');
		} else {
		  $this->not_auth('user deleted');
		}
	} else {
      //$this->failed_token($email, $password);
      $this->not_auth('invalid email');
    }
  }
}