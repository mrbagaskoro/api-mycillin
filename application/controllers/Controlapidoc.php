<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . '/libraries/REST_Controller.php';
require_once APPPATH . '/libraries/JWT.php';
require_once APPPATH . '/libraries/ExpiredException.php';
require_once APPPATH . '/libraries/BeforeValidException.php';
require_once APPPATH . '/libraries/SignatureInvalidException.php';

//uncomment di bawah ini atau gunakan autoload yang di config->config->composer_autoload default ada di composer_autoload
//require_once FCPATH . 'vendor/autoload.php';

use Restserver\Libraries\REST_Controller;

use \Firebase\JWT\JWT;

class Controlapidoc extends REST_Controller{

  private $secret_key = 'traksindo_maju_jaya_selalu';

  public function __construct(){
    parent::__construct();
  }

  //method untuk not found 404
  public function success($pesan){
    $this->response(['result' => [
      'status'=>TRUE,
      'message'=>$pesan
    ]],REST_Controller::HTTP_OK);
  }

  //method untuk not found 404
  public function not_found($pesan){
    $this->response(['result' => [
      'status'=>FALSE,
      'message'=>$pesan
    ]],REST_Controller::HTTP_NOT_FOUND);
  }

  //method untuk bad request 400
  public function bad_req($pesan){
    $this->response(['result' => [
      'status'=>FALSE,
      'message'=>$pesan
    ]],REST_Controller::HTTP_BAD_REQUEST);
  }

  //method untuk not auth 400
  public function not_auth($pesan){
    $this->response(['result' => [
      'status'=>FALSE,
      'message'=>$pesan
    ]],REST_Controller::HTTP_UNAUTHORIZED);
  }

  //method untuk melihat token pada user
  public function generate_jwt_post(){
    $this->load->model('modelapidoc','ma');

    $date = new DateTime();

    $now = new DateTime();
    $now->add(new DateInterval('PT60S'));
    $date_time = $now->format('Y-m-d H:i:s');

    $data = json_decode(file_get_contents('php://input'), true);

    $user_data = $this->ma->is_valid_user($data['email']);

    if ($user_data) {

      if ($data['password'] == $this->encrypt->decode($user_data->user_password) && $user_data->user_status == '1') {
        $payload = [
                    'iat'  => $date->getTimestamp(),         // Issued at: time when the token was generated
                    'jti'  => $user_data->user_email_address,                 // Json Token Id: an unique identifier for the token
                    'iss'  => $_SERVER['HTTP_HOST'],       // Issuer
                    'aud'  => $this->input->ip_address(),       // Audience
                    'sub'  => 'generate_token',       // Subject
                    'nbf'  => $date->getTimestamp() + 5,        // Not before
                    'exp'  => $date->getTimestamp() + 2592000,           // Expire
                    'data' => [                  // Data related to the signer user
                            'email'   => $user_data->user_email_address, // userid from the users table
                            'full_name' => $user_data->user_full_name, // User name
                        ]
                    ];

        $output = ['result' => [
                                'status' => TRUE,
                                'message' =>'login success',
                                'data' => ['email' => $user_data->user_email_address,
                                  'full_name' => $user_data->user_full_name,
                                  'mobile_number' => $user_data->user_mobile_number,
                                  'dob' => $user_data->user_date_of_birth,
                                  'gender' => $user_data->user_gender,
                                  'weight' => $user_data->user_weight
                                ],
                                'token' => 'Bearer '.JWT::encode($payload,$this->secret_key)
                              ]
                            ];

        $this->response($output,REST_Controller::HTTP_OK);

      } else if ($user_data->user_status == '2') {
        $this->not_auth('user inactive');
      } else if ($user_data->user_status == '0') {
        $this->not_auth('user deleted');
      } else {
        //$this->failed_token($email, $password);
        $this->not_auth('invalid login');
      }

    } else {
      //$this->failed_token($email, $password);
      $this->not_auth('invalid login');
    }

  }

//method untuk mengecek token setiap melakukan post, put, etc
  public function validate_jwt(){
    $this->load->model('ma');

    $jwt = $this->input->get_request_header('Authorization');

    $token = str_replace('Bearer ', '', $jwt);

    try {

      $decode = JWT::decode($token,$this->secret_key,array('HS256'));
      //melakukan pengecekan database, jika email tersedia di database maka return true
		if ($user_data = $this->ma->is_valid_user($decode->data->email)) {		  
			if ($user_data->user_status == '1') {
				return true;
			} else if ($user_data->user_status == '2') {
			  $this->not_auth('user inactive');
			} else {
			  $this->not_auth('user deleted');
			}
		} else {
			$this->failed_token('invalid token');
		}

    } catch (\Exception $e) {

      //catch (\Firebase\JWT\SignatureInvalidException $e) {
      //print "Error!: " . $e->getMessage();

      $this->failed_token('invalid token');
    }

  }

  //method untuk jika view token diatas fail
  public function failed_token($pesan){
    /*$this->response(['result' => [
      'status'=>FALSE,
      'email'=>$email,
      'password'=>$password,
      'message'=>'Invalid credentials!'
      ]],REST_Controller::HTTP_BAD_REQUEST);*/

    $message = array('status'=>FALSE,'message'=>$pesan);
    $this->output->set_status_header(401)->set_content_type('application/json','utf-8')->set_output(json_encode($message, JSON_PRETTY_PRINT))->_display();
    exit();
  }

}