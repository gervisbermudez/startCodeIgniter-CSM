<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use Tightenco\Collect\Support\Collection;

class Note extends MY_Model
{
    public $primaryKey = 'note_id';
    public $softDelete = true;
    public $hasOne = [
        'user' => ['user_id', 'Admin/User', 'user'],
    ];

    public $computed = ["json_content" => "get_json_content", "attachments" => "get_file_attachments"];

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

    public function get_json_content()
    {
        return json_decode($this->json_content);
    }

    public function get_file_attachments()
    {
        return [];
    }

    public function filter_results($collection = [])
    {
        $this->load->model('Admin/User');
        foreach ($collection as $key => &$value) {
            if (isset($value->user_id)) {
                $user = new User();
                $user->find($value->user_id);
                $value->{'user'} = $user;
                $value->{'model_type'} = "note";
            }
        }

        foreach ($collection as $key => &$value) {
            if (isset($value->json_content)) {
                $value->{'json_content'} = json_decode($value->json_content);
            }

            $value->{'attachments'} = [];

        }
        
        return $collection;
    }

    public function get_cloud_tags()
    {
        $sql = 'SELECT * FROM '. $this->table;
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $tags = [];
            foreach ($query->result() as $value) {
                $tags = array_merge(json_decode(strtolower ( $value->_value) ), $tags);
            }
            return array_unique($tags);
        }
        return false;
    }
}
