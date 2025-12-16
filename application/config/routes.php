<?php
defined('BASEPATH') or exit('No direct script access allowed');

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
|    example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|    https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|    $route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|    $route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|    $route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:    my-controller/index    -> my_controller/index
|        my-controller/my-method    -> my_controller/my_method
 */
$route['default_controller'] = 'PageController/home';
$route['404_override'] = 'PageController';
$route['translate_uri_dashes'] = false;
$route['admin'] = 'admin/admin';
$route['admin/offline'] = 'admin/admin/offline';
$route['admin/search'] = 'admin/admin/search';
//Pages system
$route['admin/pages/preview'] = 'PageController/preview';
$route['sw.js'] = function() {
    header('Content-Type: application/javascript');
    readfile(FCPATH . 'public/sw.js');
    exit;
};
$route['sitemap\.xml'] = 'PageController/siteMap';
$route['sitemap'] = 'PageController/siteMap';
$route['form/submit'] = 'PageController/formsubmit';
$route['form/ajaxsubmit'] = 'PageController/formajaxsubmit';

$route['form/success'] = 'PageController/formsuccess';
$route['form/error'] = 'PageController/formerror';
$route['admin/Fragments/'] = 'admin/Fragments';

//Blog pages
$route['feed'] = 'PageController/blogFeed';
$route['blog'] = 'PageController/blog_list';
$route['blog/search'] = 'PageController/blog_list_search/';
$route['blog/author/(:any)'] = 'PageController/blog_list_author/$1';
$route['blog/tag/(:any)'] = 'PageController/blog_list_tag/$1';
$route['blog/categorie/(:any)'] = 'PageController/blog_list_categorie/$1';
$route['blog/(:any)'] = 'PageController/get_blog/$1';
