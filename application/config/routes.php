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

$route['api/tes']['GET']                        = 'guesshost/test';
$route['api/tes_email']['GET']                  = 'guesshost/test_mail';

$route['api/register']['POST']                  = 'userpatient/register_user';
$route['api/activation']['GET']                 = 'userpatient/confirm_account';

$route['api/login']['POST']                     = 'controlapi/generate_jwt';

$route['api/forgot_password']['POST']           = 'userpatient/forgot_password';
$route['api/change_password']['POST']           = 'userpatient/change_password';
$route['api/complete_account']['POST']          = 'userpatient/complete_account';
$route['api/change_profile_photo']['POST']          = 'userpatient/change_photo';

$route['api/add_family']['POST']          = 'userpatient/add_member';
$route['api/update_family']['POST']          = 'userpatient/update_member';
$route['api/list_family']['POST']          = 'userpatient/list_member';
$route['api/delete_family']['POST']          = 'userpatient/delete_member';

$route['web/registration/(:any)']['GET']          = 'userpatient/register/rfid/$1';

//$route['api/update_loc']['POST']         		= 'userpatient/update_loc';

/*---------------------- List Parameter ------------------*/
$route['api/list_relation']['GET']				= 'listparam/list_mst_relation';

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

/*---------------------- List Articles ------------------*/
$route['articles/syarat_ketentuan']['GET']				= 'general/syarat_ketentuan';
$route['articles/ketentuan_penggunaan']['GET']				= 'general/ketentuan_penggunaan';
$route['articles/kebijakan_privasi']['GET']				= 'general/kebijakan_privasi';