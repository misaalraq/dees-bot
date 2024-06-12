<?php
/* @RiyanCoday 24/05/2024 */
/* seed tools */
error_reporting(0);
date_default_timezone_set('Asia/Jakarta');
function postCoday($url, $headers) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

function getCoday($url, $headers) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

echo "============================================================================\n";
echo "1. Complete all task\n";
echo "2. Upgrade Tree\n";
echo "3. Upgrade Storage\n";
echo "4. Checkin Daily\n";
echo "============================================================================\n";
$pilih = readline("Masukan Pilihan : ");

$tokens = file('data.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($tokens as $index => $token) {
    $acc = $index + 1;
    $headers = array(
        "accept: application/json, text/plain, */*",
        "origin: https://cf.seeddao.org",
        "referer: https://cf.seeddao.org/",
        'telegram-data: '.$token,
        "origin: https://cf.seeddao.org"
    );
    $date = date('d-m-Y H:i:s');
	if($pilih == 1){
    $getId = getCoday("https://seeddao.org/api/v1/tasks/progresses", $headers);
    $data = json_decode($getId, true);
$ids = [];
foreach ($data['data'] as $task) {
        $ids[] = $task['id'];
}
foreach ($ids as $id) {

        $completeTask = postCoday("https://seeddao.org/api/v1/tasks/".$id, $headers);
        $jsT = json_decode($completeTask, true);
            $completeTaskk = getCoday("https://seeddao.org/api/v1/tasks/notification/".$jsT['data'], $headers);
			$jsC = json_decode($completeTaskk, true);
		if($jsC['data']['data']['completed'] == "true"){
            echo "Account $acc => Reward: ".number_format($jsC['data']['data']['reward_amount'] / 1000000000, 6, '.', '')."" . PHP_EOL;
        } else {
            echo "Account $acc => $completeTask" . PHP_EOL;
        }
    }
	}else if ($pilih == 2) {
        $upgrade = postCoday("https://seeddao.org/api/v1/seed/mining-speed/upgrade", $headers);
        echo "Account $acc => $upgrade" . PHP_EOL;
    }else if ($pilih == 3) {
        $upgrade = postCoday("https://seeddao.org/api/v1/seed/storage-size/upgrade", $headers);
        echo "Account $acc => $upgrade" . PHP_EOL;
    }else if ($pilih == 4) {
        $checkin = postCoday("https://seeddao.org/api/v1/login-bonuses", $headers);
		$jsC = json_decode($checkin, true);
		$day = $jsC['data']['no'];
		$amount = $jsC['data']['amount'];
		if(isset($amount)){
        echo "Account $acc => Success Checkin ".$day." Reward: ".number_format($amount / 1000000000, 6, '.', '')."" . PHP_EOL;
		}else{
		 echo "Account $acc => $checkin" . PHP_EOL;
		}
    }else{
	exit();
	}
    echo "========" . PHP_EOL;
}
?>
