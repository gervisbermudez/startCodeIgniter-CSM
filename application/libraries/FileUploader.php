<?php
defined('BASEPATH') or exit('No direct script access allowed');

class FileUploader
{
    public function __construct()
    {
    }

    public function upload()
    {
        $CI = &get_instance();
        $CI->load->model('Admin/FileModel');
        $fileModel = new FileModel();

        $curDir = isset($_POST['curDir']) ? $_POST['curDir'] : $fileModel->uploads_root;
        $targetDirRel = $fileModel->resolveUploadTargetDir($curDir);
        if (!$fileModel->isAllowedLibraryPath($targetDirRel)) {
            $targetDirRel = $fileModel->resolveUploadTargetDir($fileModel->uploads_root);
        }
        if (!$fileModel->ensureDirectory($targetDirRel)) {
            return array(
                'error' => 'Failed to create upload directory: ' . $targetDirRel,
            );
        }

        $targetDir = rtrim($fileModel->relativeToAbsolute($targetDirRel), '/\\');
        $fileBlob = 'fileBlob';
        if (!isset($_FILES[$fileBlob]) || !isset($_POST['uploadToken'])) {
            return array(
                'error' => 'No file found',
            );
        }

        $file = $_FILES[$fileBlob]['tmp_name'];
        $fileName = isset($_POST['fileName']) ? $_POST['fileName'] : '';
        $fileId = isset($_POST['fileId']) ? preg_replace('/[^a-zA-Z0-9_-]/', '', (string) $_POST['fileId']) : '';
        $index = isset($_POST['chunkIndex']) ? $_POST['chunkIndex'] : 0;
        $totalChunks = isset($_POST['chunkCount']) ? (int) $_POST['chunkCount'] : 1;
        if ($fileId === '') {
            $fileId = random_string('alnum', 12);
        }

        $info = pathinfo($fileName);
        $base = isset($info['filename']) ? slugify($info['filename']) : 'file';
        $ext = isset($info['extension']) ? strtolower($info['extension']) : '';
        if ($base === '') {
            $base = 'file';
        }
        $processedFileName = $ext !== '' ? ($base . '-' . $fileId . '.' . $ext) : ($base . '-' . $fileId);
        $targetFileBase = $targetDir . DIRECTORY_SEPARATOR . $processedFileName;
        $targetFile = $targetFileBase;
        if ($totalChunks > 1) {
            $targetFile .= '_' . str_pad((string) $index, 4, '0', STR_PAD_LEFT);
        }

        if (!is_writable($targetDir)) {
            return array(
                'error' => 'Upload directory is not writable: ' . $targetDirRel,
            );
        }

        if (!move_uploaded_file($file, $targetFile)) {
            return array(
                'error' => 'Error uploading chunk ' . $index,
            );
        }

        if ($totalChunks > 1) {
            $chunks = glob($targetFileBase . '_*');
            if (is_array($chunks) && count($chunks) == $totalChunks) {
                sort($chunks, SORT_STRING);
                $this->combineChunks($chunks, $targetFileBase);
            }
        } else {
            @chmod($targetFileBase, 0644);
        }

        return array(
            'chunkIndex' => $index,
            'append' => true,
            'savedFileName' => $processedFileName,
            'savedDir' => $targetDirRel,
        );
    }

    private function combineChunks($chunks, $targetFile)
    {
        $handle = fopen($targetFile, 'wb');
        if ($handle === false) {
            return;
        }
        foreach ($chunks as $file) {
            fwrite($handle, file_get_contents($file));
        }
        fclose($handle);
        foreach ($chunks as $file) {
            @unlink($file);
        }
        @chmod($targetFile, 0644);
    }
}
