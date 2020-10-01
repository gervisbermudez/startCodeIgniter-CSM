<?php
include './vendor/autoload.php';

use VIPSoft\Unzip\Unzip;

$zipFilePath = "https://github.com/gervisbermudez/startCodeIgniter-CSM/archive/master.zip";
file_put_contents("Tmpfile.zip", fopen($zipFilePath, 'r'));
$unzipper = new Unzip();
$filenames = $unzipper->extract("Tmpfile.zip", __DIR__);

// Get array of all source files
$files = scandir("startCodeIgniter-CSM-master");
// Identify directories
$source = "startCodeIgniter-CSM-master/";
$destination = "./";

// Function to remove folders and files
function rrmdir($dir)
{
    if (is_dir($dir)) {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file != "." && $file != "..") {
                rrmdir("$dir/$file");
            }
        }

        rmdir($dir);
    } else if (file_exists($dir)) {
        unlink($dir);
    }

}

// Function to Copy folders and files
function rcopy($src, $dst)
{
    if (file_exists($dst)) {
        rrmdir($dst);
    }

    if (is_dir($src)) {
        mkdir($dst);
        $files = scandir($src);
        foreach ($files as $file) {
            if ($file != "." && $file != "..") {
                rcopy("$src/$file", "$dst/$file");
            }
        }

    } else if (file_exists($src)) {
        copy($src, $dst);
    }

}

function recurse_copy($src, $dst)
{
    $dir = opendir($src);
    @mkdir($dst);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..') && ($file != "config.php") && ($file != "database.php")) {
            if (is_dir($src . '/' . $file)) {
                recurse_copy($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

recurse_copy($source, $destination);
unlink("./Tmpfile.zip");
