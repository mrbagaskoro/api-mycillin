<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'controllers/Restapi.php';

class Apianggota extends Restapi{

  public function __construct(){
    parent::__construct();
    $this->load->model('mymodel');
    //mengecek token pada class Restapi, di mana jika token invalid maka akan melakukan exit
    $this->cektoken();
  }

  function anggota_get(){

    $id = (int) $this->get('id',TRUE);
    //jika user menambahkan id maka akan di select berdasarkan id, jika tidak maka akan di select seluruhnya
    $data = ($id) ? $this->mymodel->selectanggotawhere($id) : $this->mymodel->selectanggota();


    if ($data) {
      //mengembalikan respon http ok 200 dengan data dari select di atas
      $this->response($data,Restapi::HTTP_OK);
    }else {
        $this->notfound('Anggota Tidak Di Temukan');

    }

  }

  function anggota_post(){

    $data = [
      'npm'=>$this->post('npm',TRUE),
      'nama'=>$this->post('nama',TRUE),
      'jurusan'=>$this->post('jurusan',TRUE)
    ];

    $this->form_validation->set_rules('npm','NPM','trim|required|max_length[20]|is_unique[anggota.npm]');
    $this->form_validation->set_rules('nama','Nama','trim|required|max_length[50]');
    $this->form_validation->set_rules('jurusan','Jurusan','trim|required|max_length[20]');

    if (!$this->form_validation->run()) {
      //mengembalikan respon bad request dengan validasi error
      $this->badreq($this->validation_errors());
    }else {
      //jika berhasil di masukan maka akan di respon kembali sesuai dengan data yang di masukan
      if ($this->mymodel->insertanggota($data)) {
        $this->response($data,Restapi::HTTP_CREATED);
      }

    }

  }

  function anggota_put(){
    $id = (int) $this->get('id',TRUE);

    //mendapatkan data json yang kemudian dilakukan json decode
    $data = json_decode(file_get_contents('php://input'),TRUE);

    $this->form_validation->set_data($data);
    $this->form_validation->set_rules('npm','NPM','trim|max_length[20]|is_unique[anggota.npm]');
    $this->form_validation->set_rules('nama','Nama','trim|max_length[50]');
    $this->form_validation->set_rules('jurusan','Jurusan','trim|max_length[20]');

    if (!$this->form_validation->run()) {
      //mengembalikan respon bad request dengan validasi error
      $this->badreq($this->validation_errors());
    }else {
      if ($this->mymodel->updateanggota($id,$data)) {
        $this->response($data,Restapi::HTTP_OK);
      }else {
        $this->badreq('gagal update anggota');
      }
    }


  }

  function anggota_delete(){
    $id = (int) $this->get('id',TRUE);

    if ($this->mymodel->deleteanggota($id)) {
      $this->response([
        'id'=>$id,
        'status'=>'deleted'
      ],Restapi::HTTP_OK);
    }else {
        $this->badreq('Failed To Delete ID '.$id);
    }

  }
}