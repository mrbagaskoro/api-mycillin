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

class Controlapi extends REST_Controller{

  private $secret_key = 'traksindo_maju_jaya_selalu';

  public function __construct(){
    parent::__construct();
  }

  //method untuk success 200
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

  //method untuk not auth 401
  public function not_auth($pesan){
    $this->response(['result' => [
      'status'=>FALSE,
      'message'=>$pesan
    ]],REST_Controller::HTTP_UNAUTHORIZED);
  }

  //method untuk not auth 401
  public function ok($pesan){
    $this->response(['result' => [
      'status'=>TRUE,
      'data'=>$pesan
    ]],REST_Controller::HTTP_OK);
  }

  //method untuk melihat token pada user
  public function generate_jwt_post(){
    $this->load->model('modelpatient','mod');

    $date = new DateTime();

    $now = new DateTime();
    $now->add(new DateInterval('PT60S'));
    $date_time = $now->format('Y-m-d H:i:s');

    $data = json_decode(file_get_contents('php://input'), true);

    $user_data = $this->mod->is_valid_user($data['email']);

    //$user_full = $this->mod->is_valid_user_id($user_data->user_id);

    if ($user_data) {
      $user_full = $this->mod->is_valid_user_id($user_data->user_id);
      if ($data['password'] == $this->encrypt->decode($user_data->password) && $user_data->status_id == '01') {
        $payload = [
                    'iat'  => $date->getTimestamp(),         // Issued at: time when the token was generated
                    'jti'  => $user_data->email,                 // Json Token Id: an unique identifier for the token
                    'iss'  => $_SERVER['HTTP_HOST'],       // Issuer
                    'aud'  => $this->input->ip_address(),       // Audience
                    'sub'  => 'generate_token',       // Subject
                    'nbf'  => $date->getTimestamp() + 5,        // Not before
                    'exp'  => $date->getTimestamp() + 2592000,           // Expire
                    'data' => [                  // Data related to the signer user
                            'email'   => $user_data->email, // userid from the users table
                            'full_name' => $user_full->full_name, // User name
                            'user_id' => $user_full->user_id
                        ]
                    ];

        $output = ['result' => [
                                'status' => TRUE,
                                'message' =>'login success',
                                'data' => [
                                          'email' => $user_data->email,
                                          'full_name' => $user_full->full_name, // User name
                                          'user_id' => $user_full->user_id
                                ],
                                'token' => 'Bearer '.JWT::encode($payload,$this->secret_key)
                              ]
                            ];

        $this->response($output,REST_Controller::HTTP_OK);

      } else if ($user_data->status_id == '02') {
        //$this->not_auth('user inactive');
        $output = ['result' => [
                                'status' => FALSE,
                                'message' =>'user inactive',
                                'data' => [
                                          'email' => '',
                                          'full_name' => '', // User name
                                          'user_id' => ''
                                ],
                                'token' => ''
                              ]
                            ];
        $this->response($output,REST_Controller::HTTP_UNAUTHORIZED);
      } else if ($user_data->status_id == '03') {
        //$this->not_auth('user incomplete');
        $output = ['result' => [
                                'status' => FALSE,
                                'message' =>'user incomplete',
                                'data' => [
                                          'email' => '',
                                          'full_name' => '', // User name
                                          'user_id' => ''
                                ],
                                'token' => ''
                              ]
                            ];
        $this->response($output,REST_Controller::HTTP_UNAUTHORIZED);
      } else if ($user_data->status_id == '00') {
        //$this->not_auth('user deleted');
        $output = ['result' => [
                                'status' => FALSE,
                                'message' =>'user deleted',
                                'data' => [
                                          'email' => '',
                                          'full_name' => '', // User name
                                          'user_id' => ''
                                ],
                                'token' => ''
                              ]
                            ];
        $this->response($output,REST_Controller::HTTP_UNAUTHORIZED);
      } else {
        //$this->failed_token($email, $password);
        //$this->not_auth('invalid login');
        $output = ['result' => [
                                'status' => FALSE,
                                'message' =>'invalid login',
                                'data' => [
                                          'email' => '',
                                          'full_name' => '', // User name
                                          'user_id' => ''
                                ],
                                'token' => ''
                              ]
                            ];
        $this->response($output,REST_Controller::HTTP_UNAUTHORIZED);
      }
    } else {
      //$this->failed_token($email, $password);
      //$this->not_auth('invalid login');
      $output = ['result' => [
                                'status' => FALSE,
                                'message' =>'invalid login',
                                'data' => [
                                          'email' => '',
                                          'full_name' => '', // User name
                                          'user_id' => ''
                                ],
                                'token' => ''
                              ]
                            ];
      $this->response($output,REST_Controller::HTTP_UNAUTHORIZED);
    }
  }

//method untuk mengecek token setiap melakukan post, put, etc
  public function validate_jwt(){
    $this->load->model('modelpatient','mod');

    $jwt = $this->input->get_request_header('Authorization');

    $token = str_replace('Bearer ', '', $jwt);

    try {

      $decode = JWT::decode($token,$this->secret_key,array('HS256'));
      //melakukan pengecekan database, jika email tersedia di database maka return true
  		if ($user_data = $this->mod->is_valid_user_id($decode->data->user_id)) {		  
  			if ($user_data->status_id == '01') {
  				return true;
  			} else if ($user_data->status_id == '02') {
  			  $this->not_auth('user inactive');
  			} else if ($user_data->status_id == '03') {
          $this->not_auth('user incomplete');
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

  public function register_fb_post(){
    $this->load->model('modelpatient','mod');

    $date = new DateTime();

    $now = new DateTime();
    $now->add(new DateInterval('PT60S'));
    $date_time = $now->format('Y-m-d H:i:s');

    $data['user_id'] = $this->post('fb_id');
    //$data['profile_photo'] = $this->post('avatar_img');
    $data['full_name'] = $this->post('fb_name');
    $data['email'] = $this->post('fb_email');
    $data['password'] = $this->encrypt->encode('Mycillin_2017');
    $data['created_by'] = $this->post('fb_id');
    $data['created_date'] = date("Y-m-d H:i:s");
    $data['status_id'] = '01';

    $user_data = $this->mod->is_valid_user_id($data['user_id']);

    $payload = [
                'iat'  => $date->getTimestamp(),         // Issued at: time when the token was generated
                'jti'  => $data['email'],                 // Json Token Id: an unique identifier for the token
                'iss'  => $_SERVER['HTTP_HOST'],       // Issuer
                'aud'  => $this->input->ip_address(),       // Audience
                'sub'  => 'generate_token',       // Subject
                'nbf'  => $date->getTimestamp() + 5,        // Not before
                'exp'  => $date->getTimestamp() + 2592000,           // Expire
                'data' => [                  // Data related to the signer user
                        'email'   => $data['email'], // userid from the users table
                        'full_name' => $data['full_name'], // User name
                        'user_id' => $data['user_id']
                    ]
                ];

    /*$config['upload_path'] = UPLOAD_PATH_PROFILE;
    $config['allowed_types'] = 'jpeg|jpg|png';
    $config['max_size'] = 4096;
    $config['overwrite'] = true;

    $this->load->library('upload', $config);
    if (!empty($_FILES['fb_img']['name'])) {
      $config['file_name'] = 'img_'.$this->post('user_id');
      $this->upload->initialize($config);
      if (!$this->upload->do_upload('fb_img')) {
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
    }*/

    if ($user_data != NULL) {
      if ($user_data->status_id == '01') {
        $output = ['result' => [
                                'status' => TRUE,
                                'message' =>'login success',
                                'data' => [
                                          'email'   => $data['email'], // userid from the users table
                                          'full_name' => $data['full_name'], // User name
                                          'user_id' => $data['user_id']
                                ],
                                'token' => 'Bearer '.JWT::encode($payload,$this->secret_key)
                              ]
                            ];

        $this->response($output,REST_Controller::HTTP_OK);

      } else if ($user_data->status_id == '02') {
        $output = ['result' => [
                                'status' => FALSE,
                                'message' =>'user inactive',
                                'data' => [
                                          'email' => '',
                                          'full_name' => '', // User name
                                          'user_id' => ''
                                ],
                                'token' => ''
                              ]
                            ];
        $this->response($output,REST_Controller::HTTP_UNAUTHORIZED);
      } else if ($user_data->status_id == '03') {
        $output = ['result' => [
                                'status' => FALSE,
                                'message' =>'user incomplete',
                                'data' => [
                                          'email' => '',
                                          'full_name' => '', // User name
                                          'user_id' => ''
                                ],
                                'token' => ''
                              ]
                            ];
        $this->response($output,REST_Controller::HTTP_UNAUTHORIZED);
      } else if ($user_data->status_id == '00') {
        $output = ['result' => [
                                'status' => FALSE,
                                'message' =>'user deleted',
                                'data' => [
                                          'email' => '',
                                          'full_name' => '', // User name
                                          'user_id' => ''
                                ],
                                'token' => ''
                              ]
                            ];
        $this->response($output,REST_Controller::HTTP_UNAUTHORIZED);
      } else {
        $output = ['result' => [
                                'status' => FALSE,
                                'message' =>'invalid login',
                                'data' => [
                                          'email' => '',
                                          'full_name' => '', // User name
                                          'user_id' => ''
                                ],
                                'token' => ''
                              ]
                            ];
        $this->response($output,REST_Controller::HTTP_UNAUTHORIZED);
      }
    } else {
      $user_data = $this->mod->register_fb($data);
      if ($user_data) {
        $output = ['result' => [
                                'status' => TRUE,
                                'message' =>'login success',
                                'data' => [
                                          'email'   => $data['email'], // userid from the users table
                                          'full_name' => $data['full_name'], // User name
                                          'user_id' => $data['user_id']
                                ],
                                'token' => 'Bearer '.JWT::encode($payload,$this->secret_key)
                              ]
                            ];

        $this->response($output,REST_Controller::HTTP_OK);
      } else {
        $output = ['result' => [
                                'status' => FALSE,
                                'message' =>'login fail, please try again',
                                'data' => [
                                          'email' => '',
                                          'full_name' => '', // User name
                                          'user_id' => ''
                                ],
                                'token' => ''
                              ]
                            ];
        $this->response($output,REST_Controller::HTTP_UNAUTHORIZED);
      }
    }
  }
}