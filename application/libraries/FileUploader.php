<?php
defined('BASEPATH') or exit('No direct script access allowed');

class FileUploader
{

    public function __construct(){

    }

    // main upload function used above
    // upload the bootstrap-fileinput files
    // returns associative array
    public function upload()
    {
        $preview = $config = $errors = [];
        $targetDir = $_POST['curDir'];
        if (!file_exists($targetDir)) {
            @mkdir($targetDir);
        }
        $fileBlob = 'fileBlob'; // the parameter name that stores the file blob
        if (isset($_FILES[$fileBlob]) && isset($_POST['uploadToken'])) {
            $token = $_POST['uploadToken']; // gets the upload token
            $file = $_FILES[$fileBlob]['tmp_name']; // the path for the uploaded file chunk
            $fileName = $_POST['fileName']; // you receive the file name as a separate post data
            $fileSize = $_POST['fileSize']; // you receive the file size as a separate post data
            $fileId = $_POST['fileId']; // you receive the file identifier as a separate post data
            $index = $_POST['chunkIndex']; // the current file chunk index
            $totalChunks = $_POST['chunkCount']; // the total number of chunks for this file
            $targetFile = $targetDir . '/' . $fileName; // your target file path
            if ($totalChunks > 1) { // create chunk files only if chunks are greater than 1
                $targetFile .= '_' . str_pad($index, 4, '0', STR_PAD_LEFT);
            }
            $thumbnail = 'unknown.jpg';
            if (move_uploaded_file($file, $targetFile)) {
                // get list of all chunks uploaded so far to server
                $chunks = glob("{$targetDir}/{$fileName}_*");
                // check uploaded chunks so far (do not combine files if only one chunk received)
                $allChunksUploaded = $totalChunks > 1 && count($chunks) == $totalChunks;
                if ($allChunksUploaded) { // all chunks were uploaded
                    $outFile = $targetDir . '/' . $fileName;
                    // combines all file chunks to one file
                    $this->combineChunks($chunks, $outFile);
                }
                // if you wish to generate a thumbnail image for the file
                $targetUrl = $this->getThumbnailUrl($targetFile, $fileName);
                // separate link for the full blown image file
                $zoomUrl = './uploads/' . $fileName;
                return [
                    'chunkIndex' => $index, // the chunk index processed
                    'append' => true,
                    'post' => $_POST,
                ];
            } else {
                return [
                    'error' => 'Error uploading chunk ' . $_POST['chunkIndex'],
                ];
            }
        }
        return [
            'error' => 'No file found',
        ];
    }

    // combine all chunks
    // no exception handling included here - you may wish to incorporate that
    private function combineChunks($chunks, $targetFile)
    {
        // open target file handle
        $handle = fopen($targetFile, 'a+');

        foreach ($chunks as $file) {
            fwrite($handle, file_get_contents($file));
        }

        // you may need to do some checks to see if file
        // is matching the original (e.g. by comparing file size)

        // after all are done delete the chunks
        foreach ($chunks as $file) {
            @unlink($file);
        }

        // close the file handle
        fclose($handle);
    }

    // generate and fetch thumbnail for the file
    private function getThumbnailUrl($path, $fileName)
    {
        // assuming this is an image file or video file
        // generate a compressed smaller version of the file
        // here and return the status
        $sourceFile = $path . '/' . $fileName;
        $targetFile = $path . '/thumbs/' . $fileName;
        //
        // generateThumbnail: method to generate thumbnail (not included)
        // using $sourceFile and $targetFile
        //
        /* if (generateThumbnail($sourceFile, $targetFile) === true) {
    return 'http://localhost/uploads/thumbs/' . $fileName;
    } else {
    return 'http://localhost/uploads/' . $fileName; // return the original file
    } */
    }
}
