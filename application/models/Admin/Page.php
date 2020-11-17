<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use Tightenco\Collect\Support\Collection;

class Page extends MY_model
{
    public $primaryKey = 'page_id';
    public $hasData = true;
    public $softDelete = true;
    public $hasOne = [
        'user' => ['user_id', 'Admin/User', 'user'],
        'pages_type' => ['page_type_id', 'Admin/Page_type', 'page_type'],
        'main_image' => ['mainImage', 'Admin/File', 'File'],
    ];

    public $page_data = [];

    /**
     * Page status:
     * 0 => deleted
     * 1 => published
     * 2 => draft
     * 3 => archived
     */

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Return all records found on a table or false if nothing is found
     * @return Collection
     */
    public function all()
    {
        $sql = 'SELECT p.*, pt.`page_type_name`, u.`username`, ug.`name`, ug.`level`, file_data.file as imagen_file
                FROM page p
                INNER JOIN user u ON p.`user_id` = u.`user_id`
                INNER JOIN usergroup ug ON ug.`usergroup_id` = u.`usergroup_id`
                LEFT JOIN (' . $this->get_select_json('file') . ') file_data ON file_data.file_id = p.mainImage
                INNER JOIN page_type pt ON pt.`page_type_id` = p.`page_type_id`
                WHERE p.status = 1
                ';
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $data = $query->result();
            foreach ($data as $key => &$value) {
                $data_values = json_decode($value->imagen_file);
                $value->imagen_file = $data_values;
            }
            return $this->filter_results(new Collection($data));
        }
        return false;
    }

    public function filter_results($collection = [])
    {
        $this->load->model('Admin/User');
        foreach ($collection as $key => &$value) {
            if (isset($value->user_id)) {
                $user = new User();
                $user->find($value->user_id);
                $value->{'user'} = $user;
                $value->{'model_type'} = "page";
            }
        }
        $this->load->model('Admin/File');
        foreach ($collection as $key => &$value) {
            if (isset($value->mainImage) && $value->mainImage) {
                $file = new File();
                $file->find($value->mainImage);
                $value->imagen_file = $file->as_data();
                $value->imagen_file->{'file_front_path'} = new stdClass();
                $value->imagen_file->{'file_front_path'} = $file->getFileFrontPath();
            }
        }
        return $collection;
    }
}
