<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

/*--------------------------- my route -----------------------------*/
$route['api/register']['POST']                  = 'userpatient/register_user';
$route['api/activation']['GET']                 = 'userpatient/confirm_account';

$route['api/forgot_password']['POST']           = 'userpatient/forgot_password';
$route['api/change_password']['POST']           = 'userpatient/change_password';
$route['api/complete_account']['POST']          = 'userpatient/complete_account';
$route['api/change_avatar']['POST']          	= 'userpatient/change_avatar';
$route['api/get_avatar']['POST']         	 	= 'userpatient/get_avatar';

$route['api/add_family']['POST']          		= 'userpatient/add_member';
$route['api/update_family']['POST']          	= 'userpatient/update_member';
$route['api/list_family']['POST']          		= 'userpatient/list_member';
$route['api/delete_family']['POST']          	= 'userpatient/delete_member';

$route['web/registration/(:any)']['GET']        = 'userpatient/register/rfid/$1';

$route['api/login_fb']['POST']                  = 'controlapi/register_fb';
$route['api/login']['POST']                     = 'controlapi/generate_jwt';

/*---------------------- List Parameter ------------------*/
$route['api/list_relation']['GET']				= 'listparam/list_mst_relation';
$route['api/list_cancel_reason']['GET']			= 'listparam/list_mst_cancel_reason';
$route['api/list_insr_provider']['GET']			= 'listparam/list_mst_insr_provider';
$route['api/list_payment_methode']['GET']		= 'listparam/list_mst_payment_methode';
$route['api/list_service_type']['GET']			= 'listparam/list_mst_service_type';
$route['api/list_partner_type']['GET']			= 'listparam/list_mst_partner_type';
$route['api/list_spesialisasi']['GET']			= 'listparam/list_mst_spesialisasi';
$route['api/list_dosis_obat']['GET']			= 'listparam/list_mst_dosis_obat';
$route['api/list_prescription_type']['GET']		= 'listparam/list_mst_prescription_type';
$route['api/list_use_instruction']['GET']		= 'listparam/list_mst_use_instruction';
$route['api/list_satuan_obat']['GET']			= 'listparam/list_mst_satuan_obat';

/*---------------------- doctor --------------------------*/
$route['api/update_loc_doctor']['POST']         = 'medicalhost/update_loc_doc';
$route['api/get_loc_doctor']['GET']             = 'medicalhost/get_loc_doc';
$route['api/forgot_password_doctor']['POST']    = 'userpartner/forgot_password';
$route['api/register_partner']['POST']          = 'userpartner/register_partner';
$route['api/login_doctor']['POST']              = 'controlapidoc/generate_jwt';
$route['api/activation_partner']['GET']         = 'userpartner/confirm_account';
$route['api/complete_account_partner']['POST']  = 'userpartner/complete_account_partner';
$route['api/toggle_status_partner']['POST']     = 'userpartner/toggle_status_partner';
$route['api/detail_user_partner']['POST']       = 'userpartner/detail_user';
$route['api/change_password_partner']['POST']   = 'userpartner/change_password_partner';

$route['api/change_partner_avatar']['POST']     = 'userpartner/change_partner_avatar';
$route['api/get_partner_avatar']['POST']        = 'userpartner/get_partner_avatar';

$route['api/change_partner_doc']['POST']        = 'userpartner/change_partner_doc';
$route['api/get_partner_doc']['POST']           = 'userpartner/get_partner_doc';

$route['api/list_partner_booking']['POST']      = 'userpartner/list_partner_booking';


/*---------------------- List Articles ------------------*/
$route['articles/syarat_ketentuan']['GET']		= 'general/syarat_ketentuan';
$route['articles/ketentuan_penggunaan']['GET']	= 'general/ketentuan_penggunaan';
$route['articles/kebijakan_privasi']['GET']		= 'general/kebijakan_privasi';

/*---------------------- Route by Tommi ------------------*/
$route['api/list_medical_record']['POST']         = 'userpartner/list_medical_record';
$route['api/detail_medical_record']['POST']       = 'userpartner/detail_medical_record';
$route['api/detail_prescription']['POST']         = 'userpartner/detail_prescription';

$route['api/add_member_insurance']['POST']        = 'userpatient/add_member_insurance';
$route['api/update_member_insurance']['POST']     = 'userpatient/update_member_insurance';
$route['api/list_member_insurance']['POST']       = 'userpatient/list_member_insurance';
$route['api/delete_member_insurance']['POST']     = 'userpatient/delete_member_insurance';

$route['api/change_insurance_photocard']['POST']  = 'userpatient/change_insurance_photocard';
$route['api/get_insurance_photocard']['POST']     = 'userpatient/get_insurance_photocard';

$route['api/partner_loc_autoupdate']['POST']      = 'userpartner/partner_loc_autoupdate';

$route['api/list_dash_kunjungan']['GET']       	  = 'listparam/list_dash_kunjungan';
$route['api/list_dash_reservasi']['GET']       	  = 'listparam/list_dash_reservasi';
$route['api/list_dash_konsultasi']['GET']      	  = 'listparam/list_dash_konsultasi';
$route['api/list_todo_inprogress']['GET']      	  = 'listparam/list_todo_inprogress';
$route['api/list_todo_completed']['GET']      	  = 'listparam/list_todo_completed';