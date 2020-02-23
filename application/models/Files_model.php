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
    public $table = 'files';
    public $root_dir = './';
    public $current_dir = './';
    public $current_folder = '';

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('directory');
        $this->load->helper('string');

    }

    public function map_files()
    {
        echo '<pre>';
        $directorio = directory_map($this->current_dir . $this->current_folder, 2);
        print_r($directorio);
        echo '<br>';
        echo '=====================================================';
        echo '<br>';

        $curdir = $this->current_dir . $this->current_folder;
        $this->save_dir($directorio, $curdir);
    }

    /**
     * @param array $dir_maped
     */
    private function save_dir($dir_maped, $curdir)
    {
        foreach ($dir_maped as $key => $value) {
            print_r($value);
            echo '<br />';
            $this->save_file($value, $key, $curdir);
            if (is_array($value)) {
                $this->save_dir($value, $curdir . substr($key, 0, -1));
            }
        }

    }

    public function save_file($value, $key, $dir)
    {
        $insert_array = array();
        if ($this->is_folder($value)) {
            echo 'is folder: ';
            echo '<br />';
            if (is_array($value)) {
                $insert_array = $this->get_array_save_folder($key, $dir);
            } else {
                $insert_array = $this->get_array_save_folder($value, $dir);
            }
        } else {
            echo 'is file: ';
            echo '<br />';
            $insert_array = $this->get_array_save_file($value, $dir);

        }
        echo 'insert_array: ';
        print_r($insert_array);
        echo '<br />';

        if (!$this->get_data(array('file_name' => $insert_array['file_name'], 'file_path' => $insert_array['file_path']), $this->table, '', '')) {
            $this->set_data($insert_array, $this->table);
        }

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
            'user_id' => userdata('id'),
            'shared_user_group_id' => userdata('usergroup'),
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
        echo $folder;
        echo '<br />';
        $file_key = random_string('alnum', 16);
        $insert_array = array(
            'rand_key' => $file_key,
            'file_name' => $this->get_substr_folder_name($folder),
            'file_path' => $dir_name,
            'file_type' => $this->get_substr_file_ext($folder),
            'parent_name' => $this->get_substr_file_parent_name($dir_name),
            'user_id' => userdata('id'),
            'shared_user_group_id' => userdata('usergroup'),
            'share_link' => "admin/archivos/shared_file/" . $file_key,
        );

        return $insert_array;

    }

    public function get_file_path($dir)
    {
        if ($dir == $this->root_dir) {
            return $this->root_dir;
        } else {
            return $dir;
        }
    }

    private function get_substr_file_parent_name($folder)
    {
        if ($folder == $this->root_dir) {
            return $folder;
        } else {

            if (substr($folder, -1) == '/') {
                $folder = substr($folder, 0, -1);
            }

            return $substr = substr($folder, strrpos($folder, '/') + 1);
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

    public function get_all()
    {

    }

}
