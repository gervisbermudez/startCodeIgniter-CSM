<?php

class FileModel extends MY_Model
{
    public $table = 'file';
    public $root_dir = './';
    public $current_dir = './';
    public $current_folder = '';
    public $primaryKey = 'file_id';
    public $uploads_root = './uploads/';
    public $themes_root = './themes/';
    public $trash_path = './uploads/trash/';
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
        'public/vendors/',
    );

    /**
     * Rutas de assets internos que el file manager indexó (vendors).
     * Themes se pueden navegar en el explorer; estos prefijos solo ocultan
     * basura de vendors en filtros globales (picker de imágenes).
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

    /**
     * @param string $type
     * @return array
     */
    public static function typeExtensions($type)
    {
        $map = array(
            'images' => array('jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'),
            'docs' => array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'odt', 'csv', 'rtf'),
            'doc' => array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'odt', 'csv', 'rtf'),
            'audio' => array('mp3', 'aac', 'wav', 'ogg', 'm4a', 'flac'),
            'video' => array('mp4', 'webm', 'mov', 'avi', 'mkv'),
            'archives' => array('zip', 'rar', '7z', 'tar', 'gz'),
            'zip' => array('zip', 'rar', '7z', 'tar', 'gz'),
        );
        return isset($map[$type]) ? $map[$type] : array();
    }

    /**
     * @return array
     */
    public static function textExtensions()
    {
        return array('txt', 'md', 'css', 'js', 'json', 'svg', 'xml', 'html', 'csv', 'scss', 'log', 'htaccess');
    }

    /**
     * @param string $path
     * @return string
     */
    public function normalizeDirPath($path)
    {
        $path = str_replace('\\', '/', (string) $path);
        $path = trim($path);
        if ($path === '' || $path === '.' || $path === './') {
            return $this->uploads_root;
        }
        if (strpos($path, './') !== 0) {
            $path = './' . ltrim($path, '/');
        }
        $parts = array();
        foreach (explode('/', $path) as $seg) {
            if ($seg === '' || $seg === '.') {
                continue;
            }
            if ($seg === '..') {
                array_pop($parts);
                continue;
            }
            $parts[] = $seg;
        }
        $out = './' . implode('/', $parts);
        if ($out === './') {
            return $this->uploads_root;
        }
        if (substr($out, -1) !== '/') {
            $out .= '/';
        }
        return $out;
    }

    /**
     * @param string $path
     * @return bool
     */
    public function isUnderUploads($path)
    {
        $normalized = $this->normalizeDirPath($path);
        return $normalized === $this->uploads_root || strpos($normalized, $this->uploads_root) === 0;
    }

    /**
     * @param string $path
     * @return bool
     */
    public function isTrashPath($path)
    {
        $normalized = $this->normalizeDirPath($path);
        return strpos($normalized, $this->trash_path) === 0
            || strpos($normalized, './trash/') === 0;
    }

    /**
     * @param string $path
     * @return bool
     */
    public function isUnderThemes($path)
    {
        $normalized = $this->normalizeDirPath($path);
        return $normalized === $this->themes_root || strpos($normalized, $this->themes_root) === 0;
    }

    /**
     * Writable library paths: uploads (including trash) and site themes.
     *
     * @param string $path
     * @return bool
     */
    public function isAllowedLibraryPath($path)
    {
        return $this->isUnderUploads($path) || $this->isUnderThemes($path);
    }

    /**
     * @param string $path
     * @return string
     */
    public function clampToUploads($path)
    {
        $normalized = $this->normalizeDirPath($path);
        if ($this->isUnderUploads($normalized) || $this->isUnderThemes($normalized) || $this->isTrashPath($normalized)) {
            return $normalized;
        }
        return $this->uploads_root;
    }

    /**
     * @param string $curDir
     * @return string
     */
    public function resolveUploadTargetDir($curDir)
    {
        $curDir = $this->clampToUploads($curDir);
        if ($this->isUnderThemes($curDir)) {
            return $curDir;
        }
        if (preg_match('#/\d{4}-\d{2}-\d{2}/?$#', $curDir)) {
            return $curDir;
        }
        return rtrim($curDir, '/') . '/' . date('Y-m-d') . '/';
    }

    /**
     * @param string $relative
     * @return string
     */
    public function relativeToAbsolute($relative)
    {
        $relative = str_replace('\\', '/', (string) $relative);
        return rtrim(FCPATH, '/\\') . '/' . preg_replace('#^\./#', '', $relative);
    }

    /**
     * @param string $dirRelative
     * @param int $mode
     * @return bool
     */
    public function ensureDirectory($dirRelative, $mode = 0775)
    {
        $absolute = $this->relativeToAbsolute($dirRelative);
        if (is_dir($absolute)) {
            return true;
        }
        return @mkdir($absolute, $mode, true) || is_dir($absolute);
    }

    /**
     * @param string $name
     * @return string
     */
    public function sanitizeBaseName($name)
    {
        $name = str_replace(array('\\', '/'), '', (string) $name);
        $name = trim($name);
        $name = preg_replace('/\.\.+/', '.', $name);
        return $name;
    }

    /**
     * Apply indexer exclude prefixes to the current query.
     *
     * @return void
     */
    public function applyExcludedPathPrefixes($column = 'file_path')
    {
        foreach ($this->exclude_file_path_prefixes as $prefix) {
            $this->db->not_like($column, $prefix, 'after');
        }
    }

    public function map_files()
    {
        if ($this->current_folder === '' || $this->current_folder === './') {
            $this->current_folder = 'uploads/';
        }
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
        $seen_files = array();
        if ($result) {
            foreach ($result as $row) {
                $items[] = $row;
                if (isset($row->file_type) && $row->file_type === 'folder') {
                    $seen_folders[$row->file_name] = true;
                } else {
                    $seen_files[$row->file_name . '.' . $row->file_type] = true;
                }
            }
        }
        foreach ($this->scan_disk_folders($file_path) as $folder) {
            if (!isset($seen_folders[$folder['file_name']])) {
                $insert_array = $this->get_array_save_folder($folder['file_name'], $file_path);
                if (!empty($insert_array['file_name'])) {
                    $this->set_data($insert_array);
                    $folder['file_id'] = $this->db->insert_id();
                    $folder['rand_key'] = $insert_array['rand_key'];
                }
                $items[] = $folder;
            }
        }
        foreach ($this->scan_disk_files($file_path) as $file) {
            $disk_name = $file['file_name'] . '.' . $file['file_type'];
            if (!isset($seen_files[$disk_name])) {
                $insert_array = $this->get_array_save_file($disk_name, $file_path);
                if (!empty($insert_array['file_name'])) {
                    $this->set_data($insert_array);
                    $file['file_id'] = $this->db->insert_id();
                    $file['rand_key'] = $insert_array['rand_key'];
                }
                $items[] = $file;
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
     * Files on disk at this path that the indexer has not stored yet.
     *
     * @param string $file_path
     * @return array
     */
    private function scan_disk_files($file_path)
    {
        $relative = preg_replace('#^\./#', '', str_replace('\\', '/', (string) $file_path));
        $absolute = rtrim(FCPATH . $relative, '/\\') . DIRECTORY_SEPARATOR;
        if (!is_dir($absolute)) {
            return array();
        }
        $files = array();
        $entries = @scandir($absolute);
        if ($entries === false) {
            return array();
        }
        foreach ($entries as $name) {
            if ($name === '.' || $name === '..' || $name[0] === '.') {
                continue;
            }
            if (!is_file($absolute . $name)) {
                continue;
            }
            $ext = $this->get_substr_file_ext($name);
            if (in_array($ext, $this->exclude_file_types, true)) {
                continue;
            }
            $base = $this->get_substr_file_name($name);
            if ($base === false || $base === '') {
                continue;
            }
            $files[] = array(
                'file_id' => 0,
                'file_name' => $base,
                'file_path' => $file_path,
                'file_type' => $ext,
                'parent_name' => $file_path,
                'featured' => 0,
                'status' => 1,
            );
        }
        usort($files, function ($a, $b) {
            return strcasecmp($a['file_name'], $b['file_name']);
        });
        return $files;
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
        if ($this->isUnderThemes($path)) {
            return false;
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
        $allowed = array('file_name', 'file_type', 'file_path', 'featured', 'file_id');
        if (!in_array($column, $allowed, true)) {
            return array();
        }
        if (!is_array($filters)) {
            $filters = ($filters === null || $filters === '') ? array() : array($filters);
        }
        $clean = array();
        foreach ($filters as $value) {
            if ($value === null || $value === '') {
                continue;
            }
            if (is_string($value) && strpos($value, ',') !== false && $column === 'file_type') {
                foreach (explode(',', $value) as $part) {
                    $part = trim($part);
                    if ($part !== '') {
                        $clean[] = $part;
                    }
                }
                continue;
            }
            $clean[] = $value;
        }
        if (!$clean) {
            return array();
        }

        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('status', 1);
        if ($limit) {
            $this->db->limit($limit);
        }
        if ($order) {
            $this->db->order_by($order[0], $order[1]);
        } else {
            $this->db->order_by($this->primaryKey, 'ASC');
        }
        if ($column === 'featured') {
            $this->db->where('featured', (int) $clean[0]);
        } elseif ($column === 'file_type') {
            $this->db->where_in('file_type', $clean);
        } elseif ($column === 'file_path' && count($clean) === 1) {
            $this->db->group_start();
            $this->db->like('file_path', $clean[0], 'after');
            if (strpos($clean[0], 'trash') !== false) {
                $this->db->or_like('file_path', './trash/', 'after');
                $this->db->or_like('file_path', $this->trash_path, 'after');
            }
            $this->db->group_end();
        } else {
            $this->db->group_start();
            $this->db->like($column, $clean[0]);
            for ($i = 1; $i < count($clean); $i++) {
                $this->db->or_like($column, $clean[$i]);
            }
            $this->db->group_end();
        }
        $this->applyExcludedPathPrefixes();
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $this->keep_existing_files($query->result_array());
        }
        return array();
    }

    /**
     * Library search used by explorer + picker. Type filters keep non-excluded
     * indexed files so the image selector still finds public/img assets.
     *
     * @param string $type
     * @param string $q
     * @param int $limit
     * @return array
     */
    public function filterLibrary($type, $q = '', $limit = 250)
    {
        $type = strtolower(trim((string) $type));
        $q = trim((string) $q);

        if ($type === 'recent' || $type === 'recents') {
            return $this->listRecent($limit, $q);
        }

        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('status', 1);

        if ($type === 'important' || $type === 'starred') {
            $this->db->where('featured', 1);
        } elseif ($type === 'trash') {
            $this->db->group_start();
            $this->db->like('file_path', $this->trash_path, 'after');
            $this->db->or_like('file_path', './trash/', 'after');
            $this->db->group_end();
        } else {
            $ext = self::typeExtensions($type);
            if ($ext) {
                $this->db->where_in('file_type', $ext);
            } elseif ($type !== '') {
                return array();
            }
        }

        if ($q !== '') {
            $this->db->like('file_name', $q);
        }

        $this->applyExcludedPathPrefixes();
        $this->db->order_by('date_update', 'DESC');
        if ($limit) {
            $this->db->limit((int) $limit);
        }
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $this->keep_existing_files($query->result_array());
        }
        return array();
    }

    /**
     * @param int $limit
     * @param string $q
     * @return array
     */
    public function listRecent($limit = 20, $q = '')
    {
        $this->db->select('file.*');
        $this->db->from('file_activity');
        $this->db->join('file', 'file.file_id = file_activity.file_id');
        $this->db->where('file.status', 1);
        $this->db->where('file.file_type !=', 'folder');
        if ($q !== '') {
            $this->db->like('file.file_name', $q);
        }
        $this->applyExcludedPathPrefixes('file.file_path');
        $this->db->order_by('file_activity.date_create', 'DESC');
        $this->db->limit(max((int) $limit, 1) * 3);
        $query = $this->db->get();
        if ($query->num_rows() < 1) {
            return array();
        }
        $unique = array();
        $rows = array();
        foreach ($query->result_array() as $row) {
            $id = (int) $row['file_id'];
            if (isset($unique[$id])) {
                continue;
            }
            $unique[$id] = true;
            $rows[] = $row;
            if (count($rows) >= (int) $limit) {
                break;
            }
        }
        return $this->keep_existing_files($rows);
    }

    /**
     * Descendants of a folder (for zip).
     *
     * @param object|array $folder
     * @return array
     */
    public function listDescendantFiles($folder)
    {
        $path = is_array($folder) ? $folder['file_path'] : $folder->file_path;
        $name = is_array($folder) ? $folder['file_name'] : $folder->file_name;
        $prefix = rtrim($path, '/') . '/' . $name . '/';
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('status', 1);
        $this->db->where('file_type !=', 'folder');
        $this->db->like('file_path', $prefix, 'after');
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
        if (is_array($file) || !is_string($file) || $file === '') {
            return false;
        }
        $info = pathinfo($file);
        if (empty($info['filename'])) {
            return false;
        }
        return $info['filename'];
    }

    private function get_substr_file_ext($file)
    {
        if (is_array($file)) {
            return 'folder';
        }
        if ($this->is_folder($file)) {
            return 'folder';
        }
        $info = pathinfo((string) $file);
        if (!empty($info['extension'])) {
            return strtolower($info['extension']);
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
        if (is_array($file) || !is_string($file) || $file === '') {
            return false;
        }
        return strpos($file, '.') !== false;
    }

    /**
     * @param int $file_id
     * @param string $action
     * @param string $description
     * @return void
     */
    public function logActivity($file_id, $action, $description)
    {
        $this->load->model('Admin/FileActivityModel');
        $file_activity = new FileActivityModel();
        $file_activity->file_id = $file_id;
        $file_activity->user_id = userdata('user_id');
        $file_activity->action = $action;
        $file_activity->description = $description;
        $file_activity->date_create = date('Y-m-d H:i:s');
        $file_activity->status = 1;
        $file_activity->save();
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
            $relative = $this->file_path . $this->getFileFullName();
            $relative = preg_replace('#^\./#', '', str_replace('\\', '/', $relative));
            return '/' . ltrim($relative, '/');
        } catch (\Throwable $th) {
            return "";
        }
    }

    /**
     * @return bool
     */
    public function isTextFile()
    {
        return in_array(strtolower((string) $this->file_type), self::textExtensions(), true);
    }

    /**
     * @return bool
     */
    public function isImageFile()
    {
        return in_array(strtolower((string) $this->file_type), self::typeExtensions('images'), true);
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