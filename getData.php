<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valid_user = 'user';
    $valid_pass = 'password';

    if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_USER'] !== $valid_user || $_SERVER['PHP_AUTH_PW'] !== $valid_pass) {
        header('WWW-Authenticate: Basic realm="My Realm"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'xxx'; // error Autentikasi // echo 'Autentikasi diperlukan!';
        exit;
    }

    if (isset($_POST['siteList']) || isset($_POST['domain'])) {

        $dirpth = '/sites/d';
        // $dirpth = dirname(__FILE__);
        $file = $dirpth . '/domains.txt';

        if ($_POST['req'] === 'add') {
            $siteList = $_POST['siteList'];

            // Membaca isi file domains.txt
            $content = file_get_contents($file);

            // Memeriksa jika baris terakhir tidak kosong
            if (!empty($content) && substr($content, -1) !== PHP_EOL) {
                $siteList = PHP_EOL . $siteList; // Tambahkan baris baru sebelum konten baru
            }

            // $tulisData = file_put_contents('domains.txt', $siteList, FILE_APPEND);
            $tulisData = file_put_contents($file, $siteList, FILE_APPEND);
            // $result = file_get_contents('domains.txt');
            if ($tulisData !== false) {
                $response = 'done'; // success
            } else {
                $response = 'fail'; // failed
            }
        } else if ($_POST['req'] === 'del') {

            // $domData = $_POST['domain'];
            $domain = $_POST['domain'];
            // $xdomData = explode('_', $domData);
            // $domain = $xdomData[0];
            // $platform = $xdomData[1];
            // $ip = $xdomData[2];
            // $iurl = $xdomData[3];
            $contents = file_get_contents($file);

            // Memisahkan konten menjadi array dengan memisahkan baris-baris
            $lines = explode("\n", $contents);
            $lines = array_map('trim', $lines); // Menghapus spasi di awal dan akhir setiap baris
            // Mencari indeks baris yang mengandung domain yang akan dihapus
            $lineIndex = array_search($domain, $lines);

            // Jika domain ditemukan, hapus baris tersebut
            if ($lineIndex !== false) {
                unset($lines[$lineIndex]);

                // Menggabungkan kembali baris-baris yang tersisa menjadi string
                $updatedContents = implode("\n", $lines);

                // Menulis kembali konten ke file
                $hapusData = file_put_contents($file, $updatedContents);
                if ($hapusData !== false) {
                    $response = "done";
                } else {
                    $response = "write failed [server]";
                }
            } else {
                echo "konten: \n" . $contents . "\n";
                $response = 'fail:' . $domain;
            }
        } else {
            $response = 'no req';
        }
    } else {
        $response = 'not set';
    }
} else {
    $response = 'error';
}

header('Content-Type: application/json');
echo json_encode($response);
