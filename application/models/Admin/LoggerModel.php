<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class LoggerModel extends MY_Model
{
    public $table = 'logger';
    public $primaryKey = 'logger_id';
    public $searchable = array('type', 'token', 'comment');

    public function __construct()
    {
        parent::__construct();
    }

    public function filter_results($collection = [])
    {
        $this->load->model('Admin/UserModel');
        foreach ($collection as $key => &$value) {
            $this->attach_log_target($value);

            if (isset($value->user_id) && $value->user_id) {
                $user = new UserModel();
                $user->find($value->user_id);
                $value->{'user'} = $user->as_data();
            }
        }
        return $collection;
    }

    /**
     * @param object $value
     * @return void
     */
    protected function attach_log_target($value)
    {
        if (empty($value->type) || empty($value->type_id)) {
            return;
        }

        $map = array(
            'pages' => array('model' => 'Admin/PageModel', 'class' => 'PageModel', 'label' => 'title', 'url' => 'admin/pages/editar/'),
            'config' => array('model' => 'Admin/SiteConfigModel', 'class' => 'SiteConfigModel', 'label' => 'config_name', 'url' => 'admin/configuration/site'),
            'events' => array('model' => 'Admin/EventModel', 'class' => 'EventModel', 'label' => 'name', 'url' => 'admin/events/edit/'),
            'albumes' => array('model' => 'Admin/AlbumModel', 'class' => 'AlbumModel', 'label' => 'name', 'url' => 'admin/gallery/edit/'),
            'videos' => array('model' => 'Admin/VideoModel', 'class' => 'VideoModel', 'label' => 'name', 'url' => 'admin/videos/edit/'),
            'fragments' => array('model' => 'Admin/FragmentModel', 'class' => 'FragmentModel', 'label' => 'name', 'url' => 'admin/fragments/edit/'),
            'users' => array('model' => 'Admin/UserModel', 'class' => 'UserModel', 'label' => 'username', 'url' => 'admin/users/edit/'),
            'siteforms' => array('model' => 'Admin/SiteFormModel', 'class' => 'SiteFormModel', 'label' => 'name', 'url' => 'admin/siteforms/edit/'),
            'menus' => array('model' => 'Admin/MenuModel', 'class' => 'MenuModel', 'label' => 'name', 'url' => 'admin/menus/edit/'),
            'categories' => array('model' => 'Admin/CategorieModel', 'class' => 'CategorieModel', 'label' => 'name', 'url' => 'admin/categories/edit/'),
            'categorie' => array('model' => 'Admin/CategorieModel', 'class' => 'CategorieModel', 'label' => 'name', 'url' => 'admin/categories/edit/'),
            'custom_model' => array('model' => 'Admin/CustomModelModel', 'class' => 'CustomModelModel', 'label' => 'name', 'url' => 'admin/custommodels/edit/'),
            'files' => array('url' => 'admin/files'),
            'cache' => array('url' => 'admin/configuration/system'),
        );

        $type = $value->type;
        if (!isset($map[$type])) {
            return;
        }
        $spec = $map[$type];
        $id = $value->type_id;
        if (!empty($spec['url'])) {
            $value->{'type_link'} = (substr($spec['url'], -1) === '/')
                ? base_url($spec['url'] . $id)
                : base_url($spec['url']);
        }
        if (empty($spec['model']) || empty($spec['class'])) {
            return;
        }
        $this->load->model($spec['model']);
        $class = $spec['class'];
        $model = new $class();
        if (!$model->find($id)) {
            return;
        }
        $labelField = isset($spec['label']) ? $spec['label'] : '';
        if ($labelField && isset($model->{$labelField}) && $model->{$labelField} !== '') {
            $value->{'type_description'} = $model->{$labelField};
        } elseif ($type === 'config' && !empty($model->config_label)) {
            $value->{'type_description'} = $model->config_label;
        }
    }
}
