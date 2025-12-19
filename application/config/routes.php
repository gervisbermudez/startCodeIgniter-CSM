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
$route['admin'] = 'admin/AdminController';
$route['admin/login'] = 'admin/LoginController';
$route['admin/offline'] = 'admin/AdminController/offline';
$route['admin/search'] = 'admin/AdminController/search';
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
$route['admin/Fragments/'] = 'admin/FragmentsController';

// Admin routes - English aliases
$route['admin/pages'] = 'admin/PagesController';
$route['admin/pages/(:any)'] = 'admin/PagesController/$1';
$route['admin/users'] = 'admin/UsersController';
$route['admin/users/(:any)'] = 'admin/UsersController/$1';
$route['admin/videos'] = 'admin/VideosController';
$route['admin/videos/(:any)'] = 'admin/VideosController/$1';
$route['admin/customModels'] = 'admin/CustomModelsController';
$route['admin/customModels/(:any)'] = 'admin/CustomModelsController/$1';
$route['admin/CustomModels'] = 'admin/CustomModelsController';
$route['admin/CustomModels/(:any)'] = 'admin/CustomModelsController/$1';
$route['admin/menus'] = 'admin/MenusController';
$route['admin/menus/(:any)'] = 'admin/MenusController/$1';
$route['admin/gallery'] = 'admin/GalleryController';
$route['admin/gallery/(:any)'] = 'admin/GalleryController/$1';
$route['admin/files'] = 'admin/FilesController';
$route['admin/files/(:any)'] = 'admin/FilesController/$1';
$route['admin/notes'] = 'admin/NotesController';
$route['admin/notes/(:any)'] = 'admin/NotesController/$1';
$route['admin/siteForms'] = 'admin/SiteFormsController';
$route['admin/siteForms/(:any)'] = 'admin/SiteFormsController/$1';
$route['admin/SiteForms'] = 'admin/SiteFormsController';
$route['admin/SiteForms/(:any)'] = 'admin/SiteFormsController/$1';
$route['admin/calendar'] = 'admin/CalendarController';
$route['admin/calendar/(:any)'] = 'admin/CalendarController/$1';
$route['admin/fragments'] = 'admin/FragmentsController';
$route['admin/fragments/(:any)'] = 'admin/FragmentsController/$1';
$route['admin/Fragments'] = 'admin/Fragments';
$route['admin/Fragments/(:any)'] = 'admin/Fragments/$1';
$route['admin/events'] = 'admin/EventsController';
$route['admin/events/(:any)'] = 'admin/EventsController/$1';
$route['admin/categories'] = 'admin/CategoriesController';
$route['admin/categories/(:any)'] = 'admin/CategoriesController/$1';
$route['admin/configuration'] = 'admin/ConfigurationController';
$route['admin/configuration/(:any)'] = 'admin/ConfigurationController/$1';

// API v1 routes
$route['api/v1/dashboard/(:any)'] = 'api/v1/DashboardController/$1';
$route['api/v1/dashboard'] = 'api/v1/DashboardController';
$route['api/v1/users/(:any)'] = 'api/v1/UsersController/$1';
$route['api/v1/users'] = 'api/v1/UsersController';
$route['api/v1/pages/(:any)'] = 'api/v1/PagesController/$1';
$route['api/v1/pages'] = 'api/v1/PagesController';
$route['api/v1/files/(:any)'] = 'api/v1/FilesController/$1';
$route['api/v1/files'] = 'api/v1/FilesController';
$route['api/v1/fragments/(:any)'] = 'api/v1/FragmentsController/$1';
$route['api/v1/fragments'] = 'api/v1/FragmentsController';
$route['api/v1/menus/(:any)'] = 'api/v1/MenusController/$1';
$route['api/v1/menus'] = 'api/v1/MenusController';
$route['api/v1/notes/(:any)'] = 'api/v1/NotesController/$1';
$route['api/v1/notes'] = 'api/v1/NotesController';
$route['api/v1/categories/(:any)'] = 'api/v1/CategoriesController/$1';
$route['api/v1/categories'] = 'api/v1/CategoriesController';
$route['api/v1/events/(:any)'] = 'api/v1/EventsController/$1';
$route['api/v1/events'] = 'api/v1/EventsController';
$route['api/v1/albumes/(:any)'] = 'api/v1/AlbumesController/$1';
$route['api/v1/albumes'] = 'api/v1/AlbumesController';
$route['api/v1/siteforms/(:any)'] = 'api/v1/SiteformsController/$1';
$route['api/v1/siteforms'] = 'api/v1/SiteformsController';
$route['api/v1/models/(:any)'] = 'api/v1/ModelsController/$1';
$route['api/v1/models'] = 'api/v1/ModelsController';
$route['api/v1/search/(:any)'] = 'api/v1/SearchController/$1';
$route['api/v1/search'] = 'api/v1/SearchController';
$route['api/v1/config/(:any)'] = 'api/v1/ConfigController/$1';
$route['api/v1/config'] = 'api/v1/ConfigController';
$route['api/v1/login/(:any)'] = 'api/v1/LoginController/$1';
$route['api/v1/login'] = 'api/v1/LoginController';

//Blog pages
$route['feed'] = 'PageController/blogFeed';
$route['blog'] = 'PageController/blog_list';
$route['blog/search'] = 'PageController/blog_list_search/';
$route['blog/author/(:any)'] = 'PageController/blog_list_author/$1';
$route['blog/tag/(:any)'] = 'PageController/blog_list_tag/$1';
$route['blog/categorie/(:any)'] = 'PageController/blog_list_categorie/$1';
$route['blog/(:any)'] = 'PageController/get_blog/$1';
