<?php

namespace WP_Ultimo\Dependencies;

$path = '../tmp/';
$tempfilename = $_REQUEST['filename'] . '.pdf';
if (\strstr($tempfilename, '/') || \strstr($tempfilename, '\\')) {
    throw new MpdfException('Output filename can not not contain \\ or /');
}
$name = $_REQUEST['opname'];
$dest = $_REQUEST['dest'];
if ($tempfilename && \file_exists($path . $tempfilename)) {
    // mPDF 5.3.17
    if ($dest === 'I') {
        if (\PHP_SAPI != 'cli') {
            \header('Content-Type: application/pdf');
            \header('Content-disposition: inline; filename="' . $name . '"');
            \header('Cache-Control: public, must-revalidate, max-age=0');
            \header('Pragma: public');
            \header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
            \header('Last-Modified: ' . \gmdate('D, d M Y H:i:s') . ' GMT');
        }
    } elseif ($dest === 'D') {
        if (\headers_sent()) {
            throw new MpdfException('Some data has already been output to browser, can\'t send PDF file');
        }
        \header('Content-Description: File Transfer');
        \header('Content-Transfer-Encoding: binary');
        \header('Cache-Control: public, must-revalidate, max-age=0');
        \header('Pragma: public');
        \header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
        \header('Last-Modified: ' . \gmdate('D, d M Y H:i:s') . ' GMT');
        \header('Content-Type: application/force-download');
        \header('Content-Type: application/octet-stream', \false);
        \header('Content-Type: application/download', \false);
        \header('Content-Type: application/pdf', \false);
        \header('Content-disposition: attachment; filename="' . $name . '"');
    }
    $filesize = \filesize($path . $tempfilename);
    if (empty($_SERVER['HTTP_ACCEPT_ENCODING'])) {
        // don't use length if server using compression
        \header('Content-Length: ' . $filesize);
    }
    $fd = \fopen($path . $tempfilename, 'rb');
    \fpassthru($fd);
    \fclose($fd);
    \unlink($path . $tempfilename);
    // ====================== DELETE OLD FILES - Housekeeping =========================================
    // Clear any files in directory that are >24 hrs old
    $interval = 86400;
    if ($handle = \opendir(\dirname($path . 'dummy'))) {
        while (\false !== ($file = \readdir($handle))) {
            if (\filemtime($path . $file) + $interval < \time() && $file != ".." && $file != "." && \substr($file, -3) == 'pdf') {
                \unlink($path . $file);
            }
        }
        \closedir($handle);
    }
    exit;
}
