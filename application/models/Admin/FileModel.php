<?php

class FileModel extends MY_Model
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
    public $exclude_folders = array(
        '.',
        '..',
        'node_modules\\',
        'node_modules/',
        'vendor\\',
        'vendor/',
        'startCodeIgniter-CSM-master\\',
        'application\\',
        'application/',
        'bin\\',
        'bin/',
        '.vscode\\',
        'resources\\',
        'resources/',
        'temp\\',
        'backups\\',
        'backups/',
        'themes/',
        'public/vendors/',
    );

    /**
     * Rutas de assets internos que el file manager indexó (vendors, tema demo).
     * No son uploads del editor; pedirlas como thumbnails llena la consola de 404.
     */
    public $exclude_file_path_prefixes = array(
        './public/vendors/',
        './public/js/',
        './themes/',
        './vendor/',
        './node_modules/',
        './application/',
        './bin/',
        './resources/',
        './graphify-out/',
    );

    public $exclude_file_types = array(
        "bladec",
        "aspx",
        "aspx.cs",
    );

    public $hasMany = [
        'history' => ['file_id', 'Admin/FileActivityModel', 'FileActivityModel'],
    ];

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('directory');
        $this->load->model('Admin/SiteConfigModel');
    }

    public function map_files()
    {
        $directorio = directory_map($this->current_dir . $this->current_folder);
        if (!is_array($directorio)) {
            $directorio = array();
        }
        foreach ($this->exclude_folders as $value) {
            unset($directorio[$value]);
        }
        $curdir = $this->current_dir . $this->current_folder;
        $this->save_dir($directorio, $curdir);

        $this->load->model('Admin/SiteConfigModel');
        $ok = $this->SiteConfigModel->update_data(array('config_name' => 'LAST_UPDATE_FILEMANAGER'), array('config_value' => date("Y-m-d H:i:s")), 'site_config');
        invalidate_site_config_cache();
        return $ok;
    }

    /**
     * DB rows for a path plus real subdirectories (Linux indexer never stored folders).
     *
     * @param string $file_path
     * @return array
     */
    public function listPath($file_path)
    {
        $result = $this->where(array('file_path' => $file_path, 'status' => 1));
        $items = array();
        $seen_folders = array();
        if ($result) {
            foreach ($result as $row) {
                $items[] = $row;
                if (isset($row->file_type) && $row->file_type === 'folder') {
                    $seen_folders[$row->file_name] = true;
                }
            }
        }
        foreach ($this->scan_disk_folders($file_path) as $folder) {
            if (!isset($seen_folders[$folder['file_name']])) {
                $items[] = $folder;
            }
        }
        return $items;
    }

    /**
     * @param string $file_path
     * @return array
     */
    private function scan_disk_folders($file_path)
    {
        $relative = preg_replace('#^\./#', '', str_replace('\\', '/', (string) $file_path));
        $absolute = rtrim(FCPATH . $relative, '/\\') . DIRECTORY_SEPARATOR;
        if (!is_dir($absolute)) {
            return array();
        }
        $folders = array();
        $entries = @scandir($absolute);
        if ($entries === false) {
            return array();
        }
        foreach ($entries as $name) {
            if ($name === '.' || $name === '..' || $name[0] === '.') {
                continue;
            }
            if (!is_dir($absolute . $name)) {
                continue;
            }
            $key = $name . '/';
            if (in_array($key, $this->exclude_folders, true) || in_array($name . '\\', $this->exclude_folders, true)) {
                continue;
            }
            $next = (substr($file_path, -1) === '/') ? $file_path . $name . '/' : $file_path . '/' . $name . '/';
            if ($this->is_excluded_path($next)) {
                continue;
            }
            $folders[] = array(
                'file_id' => 0,
                'file_name' => $name,
                'file_path' => $file_path,
                'file_type' => 'folder',
                'parent_name' => $file_path,
                'featured' => 0,
                'status' => 1,
            );
        }
        usort($folders, function ($a, $b) {
            return strcasecmp($a['file_name'], $b['file_name']);
        });
        return $folders;
    }

    /**
     * @param array $dir_maped
     * @param string $curdir
     */
    private function save_dir($dir_maped, $curdir)
    {
        if (!is_array($dir_maped)) {
            return;
        }
        foreach ($dir_maped as $key => $value) {
            if (is_string($key) && in_array($key, $this->exclude_folders, true)) {
                continue;
            }
            $next_dir = is_array($value) ? $curdir . $key : $curdir;
            if (is_array($value) && $this->is_excluded_path($next_dir)) {
                continue;
            }
            $this->save_file($value, $key, $curdir);
            if (is_array($value)) {
                $this->save_dir($value, $next_dir);
            }
        }
    }

    /**
     * @param string $path
     * @return bool
     */
    private function is_excluded_path($path)
    {
        $path = str_replace('\\', '/', (string) $path);
        if ($path === '' || $path === './') {
            return false;
        }
        if (substr($path, -1) !== '/') {
            $path .= '/';
        }
        foreach ($this->exclude_file_path_prefixes as $prefix) {
            if (strpos($path, $prefix) === 0) {
                return true;
            }
        }
        return false;
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

        if ($insert_array['file_name'] === '' || $insert_array['file_name'] === false || $insert_array['file_name'] === null) {
            return;
        }

        if (!in_array($insert_array["file_type"], $this->exclude_file_types)) {
            $result = $this->get_data(array('file_name' => $insert_array['file_name'], 'file_path' => $insert_array['file_path']), "1");
            if (!$result) {
                $this->set_data($insert_array, $this->table);
            }
        }
    }

    public function get_filter_files($column, $filters, $limit = '', $order = array())
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('status', 1);
        $limit ? $this->db->limit($limit) : null;
        if ($order) {
            $this->db->order_by($order[0], $order[1]);
        } else {
            $this->db->order_by($this->primaryKey, 'ASC');
        }
        $this->db->group_start();
        $this->db->like($column, $filters[0]);
        for ($i = 1; $i < count($filters); $i++) {
            $this->db->or_like($column, $filters[$i]);
        }
        $this->db->group_end();
        foreach ($this->exclude_file_path_prefixes as $prefix) {
            $this->db->not_like('file_path', $prefix, 'after');
        }
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $this->keep_existing_files($query->result_array());
        }
        return array();
    }

    /**
     * Drop rows whose file is gone from disk so the picker does not 404 on stale index entries.
     */
    private function keep_existing_files($rows)
    {
        $existing = array();
        foreach ($rows as $row) {
            if (isset($row['file_type']) && $row['file_type'] === 'folder') {
                $existing[] = $row;
                continue;
            }
            if (is_file($this->resolveDiskPath($row['file_path'], $row['file_name'], $row['file_type']))) {
                $existing[] = $row;
            }
        }
        return $existing;
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
            'share_link' => "admin/files/shared_file/" . $file_key,
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
            'file_type' => 'folder',
            'parent_name' => $this->get_substr_file_parent_name($dir_name),
            'user_id' => userdata('user_id'),
            'shared_user_group_id' => userdata('usergroup_id'),
            'share_link' => "admin/files/shared_file/" . $file_key,
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
        if (is_array($folder)) {
            return '';
        }
        $folder = str_replace('\\', '/', (string) $folder);
        return trim($folder, '/');
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
        if (!is_string($folder) || $folder === '') {
            return false;
        }
        $last = substr($folder, -1);
        return ($last === '/' || $last === '\\' || strpos($folder, '\\') !== false);
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

    /**
     * Absolute path on disk. Stored file_path is ./relative/ from FCPATH.
     *
     * @param string $file_path
     * @param string $file_name
     * @param string $file_type
     * @return string
     */
    public function resolveDiskPath($file_path, $file_name, $file_type)
    {
        $relative = $file_path . $file_name . '.' . $file_type;
        return FCPATH . preg_replace('#^\./#', '', $relative);
    }

    /**
     * True when the indexed file exists, is readable, and stays under FCPATH.
     *
     * @return bool
     */
    public function isReadableFile()
    {
        $root = realpath(FCPATH);
        if ($root === false) {
            return false;
        }
        $absolute = $this->resolveDiskPath($this->file_path, $this->file_name, $this->file_type);
        if (!is_file($absolute) || !is_readable($absolute)) {
            return false;
        }
        $resolved = realpath($absolute);
        if ($resolved === false) {
            return false;
        }
        $prefix = $root . DIRECTORY_SEPARATOR;
        return ($resolved === $root || strpos($resolved, $prefix) === 0);
    }

    /**
     * Whether the indexed row still exists on disk (directory or file).
     *
     * @return bool
     */
    public function existsOnDisk()
    {
        if ($this->file_type === 'folder') {
            $relative = preg_replace('#^\./#', '', $this->file_path . $this->file_name);
            return is_dir(FCPATH . $relative);
        }
        return is_file($this->resolveDiskPath($this->file_path, $this->file_name, $this->file_type));
    }

    public function getFileFrontPath()
    {
        try {
            // Quitar ./ y agregar / inicial para ruta absoluta desde document root
            $path = substr($this->file_path . $this->getFileFullName(), 2);
            return '/' . $path;
        } catch (\Throwable $th) {
            return "";
        }
    }

    public function getFileFullName()
    {
        try {
            return $this->file_name . '.' . $this->file_type;
        } catch (\Throwable $th) {
            return '';
        }
    }

}