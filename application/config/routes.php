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
$route['api/banner']['GET']                     = 'userpatient/get_banner_apps';

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

$route['api/login_fb']['POST']                  = 'controlpatient/register_fb';
$route['api/login']['POST']                     = 'controlpatient/generate_jwt';

/*---------------------- List Parameter ------------------*/
$route['api/list_relation']['GET']				= 'listparam/list_mst_relation';
$route['api/list_cancel_reason']['GET']			= 'listparam/list_mst_cancel_reason';
$route['api/list_cancel_reason_partner']['GET']	= 'listparam/list_mst_cancel_reason_partner';
$route['api/list_insr_provider']['GET']			= 'listparam/list_mst_insr_provider';
$route['api/list_payment_methode']['GET']		= 'listparam/list_mst_payment_methode';
$route['api/list_service_type']['GET']			= 'listparam/list_mst_service_type';
$route['api/list_partner_type']['GET']			= 'listparam/list_mst_partner_type';
$route['api/list_spesialisasi']['POST']			= 'listparam/list_mst_spesialisasi';
$route['api/list_dosis_obat']['GET']			= 'listparam/list_mst_dosis_obat';
$route['api/list_prescription_type']['GET']		= 'listparam/list_mst_prescription_type';
$route['api/list_use_instruction']['GET']		= 'listparam/list_mst_use_instruction';
$route['api/list_satuan_obat']['GET']			= 'listparam/list_mst_satuan_obat';
$route['api/valid_promo_code']['POST']			= 'listparam/valid_promo_code';

/*---------------------- doctor --------------------------*/
//$route['api/update_loc_doctor']['POST']         = 'medicalhost/update_loc_doc';
//$route['api/get_loc_doctor']['GET']             = 'medicalhost/get_loc_doc';
$route['api/forgot_password_partner']['POST']   = 'userpartner/forgot_password';
$route['api/register_partner']['POST']          = 'userpartner/register_partner';
$route['api/login_doctor']['POST']              = 'controlpartner/generate_jwt';
$route['api/activation_partner']['GET']         = 'userpartner/confirm_account';
$route['api/complete_account_partner']['POST']  = 'userpartner/complete_account_partner';
$route['api/toggle_status_partner']['POST']     = 'userpartner/toggle_status_partner';
$route['api/detail_user_partner']['POST']       = 'userpartner/detail_user';
$route['api/change_password_partner']['POST']   = 'userpartner/change_password_partner';
$route['api/token_fcm']['POST']                 = 'userpartner/token_fcm';
$route['api/token_fcm_patient']['POST']         = 'userpatient/token_fcm_patient';
$route['api/detail_token_fcm']['POST']          = 'userpartner/detail_token_fcm';
$route['api/detail_token_fcm_patient']['POST']  = 'userpatient/detail_token_fcm_patient';
$route['api/detail_partner']['POST']            = 'userpartner/detail_partner';
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
$route['api/list_medical_record']['POST']         = 'userpatient/list_medical_record';
$route['api/detail_medical_record']['POST']       = 'userpatient/detail_medical_record';
$route['api/detail_prescription']['POST']         = 'userpatient/detail_prescription';
$route['api/list_medical_record1']['POST']         = 'userpartner/list_medical_record1';
$route['api/detail_medical_record1']['POST']       = 'userpartner/detail_medical_record1';
$route['api/detail_prescription1']['POST']         = 'userpartner/detail_prescription1';
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
$route['api/partner_activation']['POST']          = 'userpartner/partner_activation';
    
/*---------------------- Route Process ------------------*/
$route['api/find_partner']['POST']		  		  = 'userpatient/find_partner';
$route['api/find_healthcare']['POST']		  	  = 'userpatient/find_healthcare';
$route['api/find_clinic']['POST']		  	  	  = 'userpatient/find_clinic';
$route['api/find_consultation']['POST']		  	  = 'userpatient/find_consultation';

$route['api/service_price']['POST']				  = 'userpatient/service_price';
$route['api/add_request']['POST']          		  = 'userpatient/add_request';
$route['api/partner_confirmation']['POST']        = 'userpartner/partner_confirmation'; /*dilanjutkan FCM ut enabble button conf. user*/
$route['api/user_booking_confirmation']['POST']   = 'userpatient/user_booking_confirmation';
$route['api/partner_task_completed']['POST']      = 'userpartner/partner_task_completed';
$route['api/user_cancel_transaction']['POST']     = 'userpatient/user_cancel_transaction';
$route['api/partner_cancel_transaction']['POST']  = 'userpartner/partner_cancel_transaction';
$route['api/user_rating_feedback']['POST']     	  = 'userpatient/user_rating_feedback';
$route['api/rating_fill_checking']['POST']        = 'userpatient/rating_fill_checking';
$route['api/detail_partner_information']['POST']  = 'userpatient/detail_partner_information';
$route['api/add_prescription']['POST']     		  = 'userpartner/add_prescription';
$route['api/user_booking_consultation']['POST']   = 'userpatient/user_booking_consultation';

/*---------------------- virtual account ------------------*/
$route['api/partner_top_up']['POST']      		  = 'userpartner/partner_top_up';
$route['api/partner_check_transaction']['POST']   = 'userpartner/partner_check_transaction';
$route['api/partner_check_balance']['POST']       = 'userpartner/partner_check_balance';
$route['api/user_check_transaction']['POST'] 	  = 'userpartner/user_check_transaction';
$route['api/user_check_balance']['POST']     	  = 'userpartner/user_check_balance';
