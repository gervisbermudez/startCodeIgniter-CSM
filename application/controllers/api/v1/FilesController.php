<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require APPPATH . 'libraries/REST_Controller.php';

class FilesController extends REST_Controller
{
    const ZIP_MAX_FILES = 50;
    const ZIP_MAX_BYTES = 83886080;

    public function __construct()
    {
        parent::__construct();
        $this->output->enable_profiler(false);
        $this->lang->load('rest_lang', 'english');
        if (!$this->verify_request()) {
            $this->response([
                'code' => REST_Controller::HTTP_UNAUTHORIZED,
            ], REST_Controller::HTTP_UNAUTHORIZED);
            exit();
        }
        $this->load->database();
        $this->load->model('Admin/FileModel', 'File');
        $this->load->model('Admin/FileActivityModel');
        $this->load->helper('download');
    }

    public function index_get($file_id = null)
    {
        if (!$this->require_file_permision('SELECT_FILES')) {
            return;
        }

        $file = new FileModel();
        if ($file_id) {
            $file_path = $this->input->get('path');
            $result = $file_path
                ? $file->find_with(['file_path' => $file_path, 'file_id' => $file_id])
                : $file->find($file_id);
            if ($result) {
                $this->response_ok($file);
                return;
            }
            $this->response_error(lang('not_found_error'));
            return;
        }

        $file_path = $this->input->get('path');
        if ($file_path) {
            $file_path = $file->clampToUploads($file_path);
            $result = $file->listPath($file_path);
            $this->response_ok($result ? $result : array());
            return;
        }

        $result = $file->listPath($file->uploads_root);
        $this->response_ok($result ? $result : array());
    }

    public function index_post()
    {
        $this->response(array('Metodo no permitido'), REST_Controller::HTTP_METHOD_NOT_ALLOWED);
    }

    public function index_put()
    {
        $this->response(array('Metodo no permitido'), REST_Controller::HTTP_METHOD_NOT_ALLOWED);
    }

    public function delete_post($file_id = null)
    {
        if (!$this->require_file_permision('DELETE_FILE')) {
            return;
        }

        if (!$file_id) {
            $this->response_error(lang('not_found_error'), array(), REST_Controller::HTTP_NOT_FOUND, REST_Controller::HTTP_NOT_FOUND);
            return;
        }

        $file = new FileModel();
        if (!$file->find($file_id)) {
            $this->response_error(lang('not_found_error'));
            return;
        }

        if (!$this->canMutateFile($file)) {
            $this->response_error(lang('not_found_error'), array(), REST_Controller::HTTP_FORBIDDEN, REST_Controller::HTTP_FORBIDDEN);
            return;
        }

        $absolute = $file->file_type === 'folder'
            ? $file->relativeToAbsolute($file->file_path . $file->file_name)
            : $file->resolveDiskPath($file->file_path, $file->file_name, $file->file_type);

        if (is_file($absolute)) {
            @unlink($absolute);
        } elseif (is_dir($absolute)) {
            $this->deleteDirectory($absolute);
        }

        $file->delete();
        $this->response_ok(true);
    }

    public function featured_file_post()
    {
        if (!$this->require_file_permision('UPDATE_FILE')) {
            return;
        }

        $post_file = $this->input->post('file');
        if (!is_array($post_file) || empty($post_file['file_id'])) {
            $this->response_error(lang('not_found_error'));
            return;
        }

        $file = new FileModel();
        if (!$file->find($post_file['file_id'])) {
            $this->response_error(lang('not_found_error'));
            return;
        }

        $file->featured = !empty($post_file['featured']) ? 1 : 0;
        $result = $file->save();
        $file->logActivity(
            $file->file_id,
            'featured',
            $file->featured ? 'The file has been marked as featured' : 'The file has been removed as featured'
        );
        $this->response_ok($result);
    }

    public function reload_file_explorer_post($folder = null)
    {
        if (!$this->require_file_permision('SELECT_FILES')) {
            return;
        }

        $File = new FileModel();
        $File->current_dir = './';
        $File->current_folder = 'uploads/';
        $mapped = $File->map_files();

        $this->db->select('file_id, file_path, file_name, file_type');
        $this->db->from('file');
        $this->db->like('file_path', './uploads/', 'after');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $probe = new FileModel();
                $probe->file_id = $row->file_id;
                $probe->file_path = $row->file_path;
                $probe->file_name = $row->file_name;
                $probe->file_type = $row->file_type;
                $probe->map = true;
                if (!$probe->existsOnDisk()) {
                    $File->find($row->file_id);
                    $File->delete();
                }
            }
        }

        $this->response_ok(['result' => $mapped]);
    }

    public function move_file_post()
    {
        if (!$this->require_file_permision('UPDATE_FILE')) {
            return;
        }

        $file = $this->input->post('file');
        $newPath = $this->input->post('newPath');
        if (!is_array($file) || empty($file['rand_key'])) {
            $this->response_error(lang('not_found_error'));
            return;
        }

        $file_model = new FileModel();
        if (!$file_model->find_with(array('rand_key' => $file['rand_key']))) {
            $this->response_error(lang('not_found_error'));
            return;
        }
        if (!$this->canMutateFile($file_model)) {
            $this->response_error(lang('not_found_error'), array(), REST_Controller::HTTP_FORBIDDEN, REST_Controller::HTTP_FORBIDDEN);
            return;
        }

        $newPath = $file_model->clampToUploads($newPath);
        if (!$file_model->ensureDirectory($newPath)) {
            $this->response_error(lang('not_found_error'));
            return;
        }

        $oldPath = $file_model->file_path;
        $from = $this->diskLocation($file_model);
        $to = $this->diskLocationAt($file_model, $newPath, $file_model->file_name);
        $renamed = @rename($from, $to);
        if (!$renamed) {
            $this->response_error(lang('not_found_error'));
            return;
        }

        $file_model->file_path = $newPath;
        $file_model->save();
        $file_model->logActivity($file_model->file_id, 'move', 'The file was moved to ' . $newPath);
        $this->response_ok(true);
    }

    public function copy_file_post()
    {
        if (!$this->require_file_permision('UPDATE_FILE')) {
            return;
        }

        $file = $this->input->post('file');
        $newPath = $this->input->post('newPath');
        if (!is_array($file) || empty($file['rand_key'])) {
            $this->response_error(lang('not_found_error'));
            return;
        }

        $file_model = new FileModel();
        if (!$file_model->find_with(array('rand_key' => $file['rand_key']))) {
            $this->response_error(lang('not_found_error'));
            return;
        }
        if (!$this->canMutateFile($file_model)) {
            $this->response_error(lang('not_found_error'), array(), REST_Controller::HTTP_FORBIDDEN, REST_Controller::HTTP_FORBIDDEN);
            return;
        }

        $newPath = $file_model->clampToUploads($newPath);
        if (!$file_model->ensureDirectory($newPath)) {
            $this->response_error(lang('not_found_error'));
            return;
        }

        $base = $file_model->file_name;
        $ext = $file_model->file_type;
        $new_file_name = $ext === 'folder' ? $base : ($base . '.' . $ext);
        $from = $this->diskLocation($file_model);
        $destRelative = $newPath . $new_file_name;
        $i = 1;
        while (file_exists($file_model->relativeToAbsolute($destRelative))) {
            $new_file_name = $ext === 'folder'
                ? $base . '(' . $i . ')'
                : $base . '(' . $i . ').' . $ext;
            $destRelative = $newPath . $new_file_name;
            $i++;
        }

        $copied = @copy($from, $file_model->relativeToAbsolute($destRelative));
        if (!$copied) {
            $this->response_error(lang('not_found_error'));
            return;
        }

        $insert_array = $file_model->get_array_save_file($new_file_name, $newPath);
        if ($ext === 'folder') {
            $insert_array = $file_model->get_array_save_folder($new_file_name, $newPath);
        }
        $result = $file_model->set_data($insert_array);
        if (!empty($insert_array) && $this->db->insert_id()) {
            $file_model->logActivity($this->db->insert_id(), 'copy', 'The file was copied to ' . $newPath);
        }
        $this->response_ok($result);
    }

    public function make_dir_post()
    {
        if (!$this->require_file_permision('CREATE_FILE')) {
            return;
        }

        $path = $this->input->post('path');
        $new_folder_name = $this->input->post('new_folder_name');
        $folder = new FileModel();
        $path = $folder->clampToUploads($path);
        $new_folder_name = $folder->sanitizeBaseName($new_folder_name);
        if ($new_folder_name === '' || $new_folder_name === '.' || $new_folder_name === '..') {
            $this->response_error(lang('not_found_error'));
            return;
        }

        $fullRelative = rtrim($path, '/') . '/' . $new_folder_name . '/';
        if (!$folder->isAllowedLibraryPath($fullRelative)) {
            $this->response_error(lang('not_found_error'), array(), REST_Controller::HTTP_FORBIDDEN, REST_Controller::HTTP_FORBIDDEN);
            return;
        }

        if (is_dir($folder->relativeToAbsolute($fullRelative))) {
            $this->response_error(lang('not_found_error'));
            return;
        }

        if (!$folder->ensureDirectory($fullRelative)) {
            $this->response_error(lang('not_found_error'));
            return;
        }

        $file_key = random_string('alnum', 16);
        $folder->rand_key = $file_key;
        $folder->file_name = $new_folder_name;
        $folder->file_path = $path;
        $folder->file_type = 'folder';
        $folder->parent_name = $path;
        $folder->user_id = userdata('user_id');
        $folder->shared_user_group_id = userdata('usergroup_id');
        $folder->share_link = 'admin/files/shared_file/' . $file_key;
        $folder->date_create = date('Y-m-d H:i:s');
        $folder->date_update = date('Y-m-d H:i:s');
        $folder->featured = 0;
        $folder->status = 1;
        $folder->save();
        $this->response_ok($folder);
    }

    public function rename_file_post()
    {
        if (!$this->require_file_permision('UPDATE_FILE')) {
            return;
        }

        $file = $this->input->post('file');
        if (!is_array($file) || empty($file['rand_key']) || empty($file['new_name'])) {
            $this->response_error(lang('not_found_error'));
            return;
        }

        $file_model = new FileModel();
        if (!$file_model->find_with(array('rand_key' => $file['rand_key']))) {
            $this->response_error(lang('not_found_error'));
            return;
        }
        if (!$this->canMutateFile($file_model)) {
            $this->response_error(lang('not_found_error'), array(), REST_Controller::HTTP_FORBIDDEN, REST_Controller::HTTP_FORBIDDEN);
            return;
        }

        $new_name = $file_model->sanitizeBaseName($file['new_name']);
        $new_name = pathinfo($new_name, PATHINFO_FILENAME);
        if ($new_name === '') {
            $this->response_error(lang('not_found_error'));
            return;
        }

        $from = $this->diskLocation($file_model);
        $to = $this->diskLocationAt($file_model, $file_model->file_path, $new_name);
        if ($from !== $to && !@rename($from, $to)) {
            $this->response_error(lang('not_found_error'));
            return;
        }

        $old = $file_model->file_name;
        $file_model->file_name = $new_name;
        $file_model->save();
        $file_model->logActivity(
            $file_model->file_id,
            'rename',
            'The file ' . $old . ' was renamed to ' . $new_name
        );
        $this->response_ok(true);
    }

    public function filter_files_get()
    {
        if (!$this->require_file_permision('SELECT_FILES')) {
            return;
        }

        $type = $this->input->get('type');
        $q = $this->input->get('q');
        if ($type || ($q !== null && $q !== '')) {
            $result = $this->File->filterLibrary((string) $type, (string) $q);
            $this->response_ok($result ? $result : array());
            return;
        }

        $filter_name = $this->input->get('filter_name');
        $filter_value = $this->input->get('filter_value');
        $legacy_types = array('images', 'docs', 'doc', 'audio', 'video', 'zip', 'archives', 'important', 'starred', 'trash', 'recent', 'recents');
        if ($filter_name && in_array($filter_name, $legacy_types, true)) {
            $result = $this->File->filterLibrary($filter_name, is_string($filter_value) ? $filter_value : '');
            $this->response_ok($result ? $result : array());
            return;
        }

        $result = $this->File->get_filter_files($filter_name, $filter_value, null, array('date_update', 'DESC'));
        $this->response_ok($result ? $result : array());
    }

    public function get_file_content_get()
    {
        if (!$this->require_file_permision('SELECT_FILES')) {
            return;
        }

        $file = $this->input->get('file');
        if (!is_array($file) || empty($file['file_id'])) {
            $this->response_error(lang('not_found_error'));
            return;
        }

        $file_model = new FileModel();
        if (!$file_model->find($file['file_id'])) {
            $this->response_error(lang('not_found_error'));
            return;
        }

        if (!$file_model->isReadableFile() || !$file_model->isTextFile()) {
            $this->response_error(lang('file_read_error'));
            return;
        }

        $string = file_get_contents($file_model->resolveDiskPath(
            $file_model->file_path,
            $file_model->file_name,
            $file_model->file_type
        ));

        if ($string === false) {
            $this->response_error(lang('file_read_error'));
            return;
        }

        $this->response_ok(
            array('message' => 'File content'),
            array('file_content' => $string)
        );
    }

    public function save_content_post()
    {
        if (!$this->require_file_permision('UPDATE_FILE')) {
            return;
        }

        $file_id = $this->input->post('file_id');
        $content = $this->input->post('content');
        if (!$file_id) {
            $this->response_error(lang('not_found_error'));
            return;
        }

        $file_model = new FileModel();
        if (!$file_model->find($file_id)) {
            $this->response_error(lang('not_found_error'));
            return;
        }
        if (!$this->canMutateFile($file_model) || !$file_model->isTextFile() || !$file_model->isReadableFile()) {
            $this->response_error(lang('file_read_error'));
            return;
        }

        $absolute = $file_model->resolveDiskPath($file_model->file_path, $file_model->file_name, $file_model->file_type);
        $uploads = realpath(FCPATH . 'uploads');
        $resolved = realpath($absolute);
        if ($uploads === false || $resolved === false || strpos($resolved, $uploads) !== 0) {
            $this->response_error(lang('file_read_error'));
            return;
        }
        if (!is_writable($absolute)) {
            @chmod($absolute, 0664);
        }
        $written = @file_put_contents($absolute, $content === null ? '' : $content);
        if ($written === false) {
            $this->response_error(lang('file_read_error'));
            return;
        }

        $file_model->date_update = date('Y-m-d H:i:s');
        $file_model->save();
        $file_model->logActivity($file_model->file_id, 'edit', 'The file content was updated');
        $this->response_ok(true);
    }

    public function download_get($file_id = null)
    {
        if (!$this->require_file_permision('SELECT_FILES')) {
            return;
        }

        $file_model = new FileModel();
        if (!$file_id || !$file_model->find($file_id) || $file_model->file_type === 'folder') {
            $this->response_error(lang('not_found_error'));
            return;
        }
        if (!$file_model->isReadableFile()) {
            $this->response_error(lang('file_read_error'));
            return;
        }

        $absolute = $file_model->resolveDiskPath($file_model->file_path, $file_model->file_name, $file_model->file_type);
        force_download($file_model->file_name . '.' . $file_model->file_type, file_get_contents($absolute));
    }

    public function download_zip_post()
    {
        if (!$this->require_file_permision('SELECT_FILES')) {
            return;
        }

        $ids = $this->input->post('file_ids');
        if (!is_array($ids)) {
            $ids = $ids ? array($ids) : array();
        }
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if (!$ids) {
            $this->response_error(lang('not_found_error'));
            return;
        }

        $added = 0;
        $bytes = 0;
        $zipPath = tempnam(sys_get_temp_dir(), 'stfiles');
        if ($zipPath === false) {
            $this->response_error(lang('file_read_error'));
            return;
        }
        @unlink($zipPath);
        $zipPath .= '.zip';
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $this->response_error(lang('file_read_error'));
            return;
        }

        foreach ($ids as $file_id) {
            if ($added >= self::ZIP_MAX_FILES) {
                break;
            }
            $file_model = new FileModel();
            if (!$file_model->find($file_id)) {
                continue;
            }
            if ($file_model->file_type === 'folder') {
                foreach ($file_model->listDescendantFiles($file_model) as $child) {
                    if ($added >= self::ZIP_MAX_FILES) {
                        break;
                    }
                    $this->addRowToZip($zip, $child, $added, $bytes);
                }
                continue;
            }
            $row = array(
                'file_path' => $file_model->file_path,
                'file_name' => $file_model->file_name,
                'file_type' => $file_model->file_type,
            );
            $this->addRowToZip($zip, $row, $added, $bytes);
        }

        $zip->close();
        if ($added < 1 || !is_file($zipPath)) {
            @unlink($zipPath);
            $this->response_error(lang('not_found_error'));
            return;
        }

        $data = file_get_contents($zipPath);
        @unlink($zipPath);
        force_download('files-' . date('Y-m-d') . '.zip', $data);
    }

    /**
     * @param mixed $permision
     * @return bool
     */
    protected function require_file_permision($permision)
    {
        if (!function_exists('has_permisions') || !has_permisions($permision)) {
            $this->response_error('You do not have permission to perform this action', array(), REST_Controller::HTTP_FORBIDDEN, REST_Controller::HTTP_FORBIDDEN);
            return false;
        }
        return true;
    }

    /**
     * @param FileModel $file
     * @return bool
     */
    private function canMutateFile($file)
    {
        $uploads = realpath(FCPATH . 'uploads');
        $legacyTrash = realpath(FCPATH . 'trash');
        if ($file->file_type === 'folder') {
            $abs = $file->relativeToAbsolute(rtrim($file->file_path, '/') . '/' . $file->file_name);
        } else {
            $abs = $file->resolveDiskPath($file->file_path, $file->file_name, $file->file_type);
        }
        $resolved = realpath($abs);
        if ($resolved !== false && $uploads !== false && strpos($resolved, $uploads) === 0) {
            return true;
        }
        if ($resolved !== false && $legacyTrash !== false && strpos($resolved, $legacyTrash) === 0) {
            return true;
        }
        $path = $file->file_type === 'folder'
            ? $file->file_path . $file->file_name . '/'
            : $file->file_path;
        return $file->isAllowedLibraryPath($path) || $file->isTrashPath($path);
    }

    /**
     * @param FileModel $file
     * @return string
     */
    private function diskLocation($file)
    {
        return $this->diskLocationAt($file, $file->file_path, $file->file_name);
    }

    /**
     * @param FileModel $file
     * @param string $dir
     * @param string $name
     * @return string
     */
    private function diskLocationAt($file, $dir, $name)
    {
        if ($file->file_type === 'folder') {
            return $file->relativeToAbsolute(rtrim($dir, '/') . '/' . $name);
        }
        return $file->resolveDiskPath($dir, $name, $file->file_type);
    }

    /**
     * @param ZipArchive $zip
     * @param array $row
     * @param int $added
     * @param int $bytes
     * @return void
     */
    private function addRowToZip($zip, $row, &$added, &$bytes)
    {
        $file = new FileModel();
        $absolute = $file->resolveDiskPath($row['file_path'], $row['file_name'], $row['file_type']);
        $resolved = realpath($absolute);
        $uploads = realpath(FCPATH . 'uploads');
        if ($resolved === false || $uploads === false || strpos($resolved, $uploads) !== 0) {
            return;
        }
        $size = @filesize($resolved);
        if ($size === false || ($bytes + $size) > self::ZIP_MAX_BYTES) {
            return;
        }
        $local = preg_replace('#^\./#', '', $row['file_path'] . $row['file_name'] . '.' . $row['file_type']);
        if ($zip->addFile($resolved, $local)) {
            $added++;
            $bytes += $size;
        }
    }

    /**
     * @param string $dir
     * @return void
     */
    private function deleteDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }
        $items = @scandir($dir);
        if ($items === false) {
            return;
        }
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                @unlink($path);
            }
        }
        @rmdir($dir);
    }
}
