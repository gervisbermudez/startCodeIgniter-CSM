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

// Admin routes - English aliases
$route['admin/users/add(/?|)'] = 'admin/UsersController/agregar';
$route['admin/users/edit/(:num)(/?|)'] = 'admin/UsersController/edit/$1';
$route['admin/pages/new(/?|)'] = 'admin/PagesController/nueva';
$route['admin/pages/edit/(:num)(/?|)'] = 'admin/PagesController/editar/$1';
$route['admin/siteforms/new(/?|)'] = 'admin/SiteFormsController/nuevo';
$route['admin/siteforms/edit/(:num)(/?|)'] = 'admin/SiteFormsController/editar/$1';
$route['admin/menus/new(/?|)'] = 'admin/MenusController/nuevo';
$route['admin/menus/edit/(:num)(/?|)'] = 'admin/MenusController/editar/$1';
$route['admin/categories/new(/?|)'] = 'admin/CategoriesController/nueva';
$route['admin/categories/edit/(:num)(/?|)'] = 'admin/CategoriesController/editar/$1';
$route['admin/events/add(/?|)'] = 'admin/EventsController/agregar';
$route['admin/events/edit/(:num)(/?|)'] = 'admin/EventsController/editar/$1';
$route['admin/gallery/new(/?|)'] = 'admin/GalleryController/nuevo';
$route['admin/gallery/edit/(:num)(/?|)'] = 'admin/GalleryController/editar/$1';
$route['admin/videos/new(/?|)'] = 'admin/VideosController/nuevo';
$route['admin/videos/edit/(:num)(/?|)'] = 'admin/VideosController/editar/$1';
$route['admin/custommodels/new(/?|)'] = 'admin/CustomModelsController/nuevo';
$route['admin/custommodels/edit/(:num)(/?|)'] = 'admin/CustomModelsController/editar/$1';
$route['admin/fragments/new(/?|)'] = 'admin/FragmentsController/nueva';
$route['admin/fragments/edit/(:num)(/?|)'] = 'admin/FragmentsController/editar/$1';


$route['admin/pages/(.+)'] = 'admin/PagesController/$1';
$route['admin/pages'] = 'admin/PagesController';
$route['admin/users/(.+)'] = 'admin/UsersController/$1';
$route['admin/users'] = 'admin/UsersController';
$route['admin/videos/(.+)'] = 'admin/VideosController/$1';
$route['admin/videos'] = 'admin/VideosController';
$route['admin/custommodels/(.+)'] = 'admin/CustomModelsController/$1';
$route['admin/custommodels'] = 'admin/CustomModelsController';
$route['admin/menus/(.+)'] = 'admin/MenusController/$1';
$route['admin/menus'] = 'admin/MenusController';
$route['admin/gallery/(.+)'] = 'admin/GalleryController/$1';
$route['admin/gallery'] = 'admin/GalleryController';
$route['admin/files/(.+)'] = 'admin/FilesController/$1';
$route['admin/files'] = 'admin/FilesController';
$route['admin/notes/(.+)'] = 'admin/NotesController/$1';
$route['admin/notes'] = 'admin/NotesController';
$route['admin/siteforms/(.+)'] = 'admin/SiteFormsController/$1';
$route['admin/siteforms'] = 'admin/SiteFormsController';
$route['admin/calendar/(.+)'] = 'admin/CalendarController/$1';
$route['admin/calendar'] = 'admin/CalendarController';
$route['admin/fragments/(.+)'] = 'admin/FragmentsController/$1';
$route['admin/fragments'] = 'admin/FragmentsController';
$route['admin/events/(.+)'] = 'admin/EventsController/$1';
$route['admin/events'] = 'admin/EventsController';
$route['admin/categories/(.+)'] = 'admin/CategoriesController/$1';
$route['admin/categories'] = 'admin/CategoriesController';

// Cache routes
$route['admin/cache/clear-page/(:num)'] = 'admin/Cache/clear_page/$1';
$route['admin/cache/clear-blog/(:num)'] = 'admin/Cache/clear_blog/$1';
$route['admin/cache/clear-all'] = 'admin/Cache/clear_all';
$route['admin/cache/info'] = 'admin/Cache/info';
$route['admin/cache/(.+)'] = 'admin/Cache/$1';

// Configuration routes
$route['admin/config/toggle-debug'] = 'admin/ConfigurationController/toggle_debug';
$route['admin/config/(.+)'] = 'admin/ConfigurationController/$1';
$route['admin/config'] = 'admin/ConfigurationController';
$route['admin/configuration/(.+)'] = 'admin/ConfigurationController/$1';
$route['admin/configuration'] = 'admin/ConfigurationController';

// Siteforms routes
$route['admin/siteforms/export/(.+)'] = 'admin/SiteFormsController/export/$1';
$route['admin/siteforms/stats/(.+)'] = 'admin/SiteFormsController/stats/$1';
$route['admin/siteforms/(.+)'] = 'admin/SiteFormsController/$1';
$route['admin/siteforms'] = 'admin/SiteFormsController';

// API v1 routes
$route['api/v1/analytics/(.+)'] = 'api/v1/AnalyticsController/$1';
$route['api/v1/analytics'] = 'api/v1/AnalyticsController';
$route['api/v1/dashboard/(.+)'] = 'api/v1/DashboardController/$1';
$route['api/v1/dashboard'] = 'api/v1/DashboardController';
$route['api/v1/users/(.+)'] = 'api/v1/UsersController/$1';
$route['api/v1/users'] = 'api/v1/UsersController';
$route['api/v1/pages/(.+)'] = 'api/v1/PagesController/$1';
$route['api/v1/pages'] = 'api/v1/PagesController';
$route['api/v1/videos/(.+)'] = 'api/v1/VideosController/$1';
$route['api/v1/videos'] = 'api/v1/VideosController';
$route['api/v1/files/(.+)'] = 'api/v1/FilesController/$1';
$route['api/v1/files'] = 'api/v1/FilesController';
$route['api/v1/fragments/(.+)'] = 'api/v1/FragmentsController/$1';
$route['api/v1/fragments'] = 'api/v1/FragmentsController';
$route['api/v1/menus/(.+)'] = 'api/v1/MenusController/$1';
$route['api/v1/menus'] = 'api/v1/MenusController';
$route['api/v1/notes/(.+)'] = 'api/v1/NotesController/$1';
$route['api/v1/notes'] = 'api/v1/NotesController';
$route['api/v1/categories/(.+)'] = 'api/v1/CategoriesController/$1';
$route['api/v1/categories'] = 'api/v1/CategoriesController';
$route['api/v1/events/(.+)'] = 'api/v1/EventsController/$1';
$route['api/v1/events'] = 'api/v1/EventsController';
$route['api/v1/albumes/(.+)'] = 'api/v1/AlbumesController/$1';
$route['api/v1/albumes'] = 'api/v1/AlbumesController';
$route['api/v1/siteforms/(.+)'] = 'api/v1/SiteformsController/$1';
$route['api/v1/siteforms'] = 'api/v1/SiteformsController';
$route['api/v1/models/(.+)'] = 'api/v1/ModelsController/$1';
$route['api/v1/models'] = 'api/v1/ModelsController';
$route['api/v1/search/(.+)'] = 'api/v1/SearchController/$1';
$route['api/v1/search'] = 'api/v1/SearchController';
$route['api/v1/config/(.+)'] = 'api/v1/ConfigController/$1';
$route['api/v1/config'] = 'api/v1/ConfigController';
$route['api/v1/login/(.+)'] = 'api/v1/LoginController/$1';
$route['api/v1/login'] = 'api/v1/LoginController';

//Blog pages
$route['feed'] = 'PageController/blogFeed';
$route['blog'] = 'PageController/blog_list';
$route['blog/search'] = 'PageController/blog_list_search/';
$route['blog/author/(:any)'] = 'PageController/blog_list_author/$1';
$route['blog/tag/(:any)'] = 'PageController/blog_list_tag/$1';
$route['blog/categorie/(:any)'] = 'PageController/blog_list_categorie/$1';
$route['blog/(:any)'] = 'PageController/get_blog/$1';
