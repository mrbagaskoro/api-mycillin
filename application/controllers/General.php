<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class General extends CI_Controller {

  public function __construct() {
    parent::__construct();
  }

  public function index(){
    $this->load->view('syarat_ketentuan');
  }

  public function syarat_ketentuan(){
    $this->load->view('syarat_ketentuan');
  }

  public function ketentuan_penggunaan(){
    $this->load->view('ketentuan_penggunaan');
  }

  public function kebijakan_privasi(){
    $this->load->view('kebijakan_privasi');
  }

  public function aktivasi(){
    $act=$this->uri->segment(3);
    $data['user']=$this->uri->segment(4);
    if ($act=='success') {
      $data['status']='Congratulation, your account successfully activated.';
      $this->load->view('aktivasi', $data);
    } else if ($act=='activated') {
      $data['status']='Your account already activated.';
      $this->load->view('aktivasi', $data);
    } else if ($act=='expired') {
      $data['status']='Activation link expired.';
      $this->load->view('aktivasi', $data);
    } else if ($act=='failed') {
      $data['status']='Oops, activation failed. Please contact us!';
      $this->load->view('aktivasi', $data);
    } else {
      
    }
  }
}