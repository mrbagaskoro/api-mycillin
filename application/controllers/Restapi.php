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

class Restapi extends REST_Controller{

  private $secretkey = 'traksindo maju jaya terus';

  public function __construct(){
    parent::__construct();
    $this->load->library('form_validation');
  }

  public function tes_get(){
    echo 'Tes';
  }

  //method untuk not found 404
  public function notfound($pesan){
    $this->response([
      'status'=>FALSE,
      'message'=>$pesan
    ],REST_Controller::HTTP_NOT_FOUND);

  }

  //method untuk bad request 400
  public function badreq($pesan){
    $this->response([
      'status'=>FALSE,
      'message'=>$pesan
    ],REST_Controller::HTTP_BAD_REQUEST);
  }

  //method untuk melihat token pada user
  public function viewtoken_post(){
    $this->load->model('loginmodel');

    $date = new DateTime();

    $nama = $this->post('nama',TRUE);
    $pass = $this->post('password',TRUE);

    $dataadmin = $this->loginmodel->is_valid($nama);

    if ($dataadmin) {

      if (password_verify($pass,$dataadmin->password)) {

        /*$payload['id'] = $dataadmin->id;
        $payload['nama'] = $dataadmin->nama;
        $payload['iat'] = $date->getTimestamp(); //waktu di buat
        $payload['exp'] = $date->getTimestamp() + 60; //satu bulan*/

        $payload = [
                    'iat'  => $date->getTimestamp(),         // Issued at: time when the token was generated
                    'jti'  => $dataadmin->id,                 // Json Token Id: an unique identifier for the token
                    'iss'  => $_SERVER['HTTP_HOST'],       // Issuer
                    'aud'  => $this->input->ip_address(),       // Audience
                    'sub'  => 'generatetoken',       // Subject
                    'nbf'  => $date->getTimestamp() + 5,        // Not before
                    'exp'  => $date->getTimestamp() + 3600,           // Expire
                    'data' => [                  // Data related to the signer user
                            'userId'   => $dataadmin->id, // userid from the users table
                            'userName' => $dataadmin->nama, // User name
                        ]
                    ];

        $output = ['result' => [
                                'status' => TRUE,
                                'message' =>'token generated',
                                'token' => JWT::encode($payload,$this->secretkey)
                              ]
                            ];

        $this->response($output,REST_Controller::HTTP_OK);

      }else {
        $this->viewtokenfail($nama,$pass);

      }

    } else {
      $this->viewtokenfail($nama,$pass);
    }

  }

  //method untuk jika view token diatas fail
  public function viewtokenfail($nama,$pass){
    $this->response([
      'status'=>FALSE,
      'nama'=>$nama,
      'password'=>$pass,
      'message'=>'Invalid credentials!'
      ],REST_Controller::HTTP_BAD_REQUEST);
  }

//method untuk mengecek token setiap melakukan post, put, etc
  public function cektoken(){
    $this->load->model('loginmodel');

    $jwt = $this->input->get_request_header('Authorization');

    try {

      $decode = JWT::decode($jwt,$this->secretkey,array('HS256'));
      //melakukan pengecekan database, jika nama tersedia di database maka return true
      if ($this->loginmodel->is_valid_num($decode->data->userName)>0) {
        return TRUE;
      }

    } catch (Exception $e) {
      /*$this->response([
      'status'=>FALSE,
      'message'=>'Invalid token!'
      ],REST_Controller::HTTP_BAD_REQUEST);

      exit();*/

      //catch (\Firebase\JWT\SignatureInvalidException $e) {
      //print "Error!: " . $e->getMessage();

      $message = array('status'=>FALSE,'message'=>$e);
      $this->output->set_status_header(200)->set_content_type('application/json','utf-8')->set_output(json_encode($message, JSON_PRETTY_PRINT))->_display();
      exit();
    }


  }

}
