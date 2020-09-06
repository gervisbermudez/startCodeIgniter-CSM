<?php
/**
 * The Files model
 */

function filter_dir($dir)
{
    if ($dir == '.' || $dir == '..' || $dir == 'node_modules\\') {
        return false;
    }
    return true;
}

class Files_model extends MY_Model
{
    public $table = 'file';
    public $root_dir = './';
    public $current_dir = './';
    public $current_folder = '';
    public $primaryKey = 'file_id';
    public $computed = array(
        'file_full_path' => 'getFileFullPath',
        'file_front_path' => 'getFileFrontPath',
        'file_full_name' => 'getFileFullName',
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('directory');
        $this->load->helper('string');

    }

    public function map_files()
    {
        $directorio = directory_map($this->current_dir . $this->current_folder);
        unset($directorio['node_modules\\']);
        unset($directorio['vendor\\']);

        $this->delete_data(array('status' => 1), $this->table);
        $curdir = $this->current_dir . $this->current_folder;
        $this->save_dir($directorio, $curdir);
        $this->load->model('Admin/Site_config');

        if (!$this->Site_config->get_data(array('config_name' => 'map_dir', 'config_value' => $this->current_dir), 'site_config')) {
            return $this->Site_config->update_data(array('config_name' => 'map_dir'), array('config_value' => $this->current_dir), 'site_config');
        } else {
            return $this->Site_config->set_data(array('config_name' => 'map_dir', 'config_value' => $this->current_dir, 'user_id' => userdata('user_id')), 'site_config');
        }
    }

    /**
     * @param array $dir_maped
     */
    private function save_dir($dir_maped, $curdir)
    {
        foreach ($dir_maped as $key => $value) {
            $this->save_file($value, $key, $curdir);
            if (is_array($value)) {
                $this->save_dir($value, $curdir . $key);
            }
        }

    }

    public function save_file($value, $key, $dir)
    {
        $insert_array = array();
        if ($this->is_folder($value)) {
            if (is_array($value)) {
                $insert_array = $this->get_array_save_folder($key, $dir);
            } else {
                $insert_array = $this->get_array_save_folder($value, $dir);
            }
        } else {
            $insert_array = $this->get_array_save_file($value, $dir);
        }
        if (!$this->get_data(array('file_name' => $insert_array['file_name'], 'file_path' => $insert_array['file_path']), $this->table, '', '')) {
            $this->set_data($insert_array, $this->table);
        }

    }

    public function get_filter_files($column, $filters)
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where_in($column, $filters);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }

        return array();

    }

    public function get_array_save_file($file_name, $dir_name)
    {
        $file_key = random_string('alnum', 16);
        $insert_array = array(
            'rand_key' => $file_key,
            'file_name' => $this->get_substr_file_name($file_name),
            'file_path' => $this->get_file_path($dir_name),
            'file_type' => $this->get_substr_file_ext($file_name),
            'parent_name' => $this->get_substr_file_parent_name($dir_name),
            'user_id' => userdata('user_id'),
            'shared_user_group_id' => userdata('usergroup_id'),
            'share_link' => "admin/archivos/shared_file/" . $file_key,
        );

        return $insert_array;

    }

    public function get_file_parent($dir)
    {
        if ($dir == $this->root_dir) {
            return $this->root_dir;
        } else {
            return substr($dir, strpos($dir, '/') + 1);
        }
    }

    public function get_array_save_folder($folder, $dir_name)
    {
        $file_key = random_string('alnum', 16);
        $insert_array = array(
            'rand_key' => $file_key,
            'file_name' => $this->get_substr_folder_name($folder),
            'file_path' => $this->get_file_path($dir_name),
            'file_type' => $this->get_substr_file_ext($folder),
            'parent_name' => $this->get_substr_file_parent_name($dir_name),
            'user_id' => userdata('user_id'),
            'shared_user_group_id' => userdata('usergroup_id'),
            'share_link' => "admin/archivos/shared_file/" . $file_key,
        );

        return $insert_array;

    }

    public function get_file_path($dir)
    {
        if ($dir == $this->root_dir) {
            return $this->root_dir;
        } else {
            return str_replace('\\', '/', $dir);
        }
    }

    private function get_substr_file_parent_name($folder)
    {
        if ($folder == $this->root_dir) {
            return $folder;
        } else {
            $folder = str_replace('\\', '/', $folder);
            $folder = substr($folder, 0, -1);
            return substr($folder, strrpos($folder, '/') + 1);

        }
    }

    private function get_substr_folder_name($folder)
    {
        if ($this->is_folder($folder)) {
            $substr = substr($folder, 0, strpos($folder, '\\'));
            return $substr;
        }
        return false;
    }

    private function get_substr_file_name($file)
    {
        if ($this->is_file($file)) {
            $substr = substr($file, 0, strpos($file, '.'));
            return $substr;
        }
        return false;
    }

    private function get_substr_file_ext($file)
    {
        if ($this->is_file($file)) {
            $substr = substr($file, strpos($file, '.') + 1);
            return $substr;
        }

        if ($this->is_folder($file)) {
            return 'folder';

        }

        return 'file';
    }

    private function is_folder($folder)
    {
        if (is_array($folder)) {
            return true;
        }
        if (strpos($folder, '\\') !== false) {
            return true;
        }
        return false;
    }

    private function is_file($file)
    {
        if (is_array($file)) {
            return false;
        } else {
            return strpos($file, '.');
        }
        return false;
    }

    public function getFileFullPath()
    {
        if ($this->map) {
            return $this->file_path . $this->getFileFullName();
        }
        return '';
    }

    public function getFileFrontPath()
    {
        if ($this->map) {
            return substr($this->file_path . $this->getFileFullName(), 2);
        }
        return '';
    }

    public function getFileFullName()
    {
        if ($this->map) {
            return $this->file_name . '.' . $this->file_type;
        }
        return '';
    }

}
