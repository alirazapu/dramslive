<?php
////echo "http://50.0.0.8/apimobile/request/request.php?key=".base64_encode($key)."&port=9991&ref=".$uid."&".$search_type."=" . $search_value;

include 'sub_key.inc';
/*$arrContextOptions = array(
                "ssl" => array(
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ),
            );


$url = file_get_contents("http://50.0.0.8/apimobile/request/request.php?key=" . urlencode(base64_encode($key))."&port=9991&ref=".urlencode($uid)."&".urlencode($search_type)."=" . urlencode($search_value), false, stream_context_create($arrContextOptions));
            $content = $url; //file_get_contents($url);
            $test_array = json_decode($content, true);*/

$arrContextOptions = array(
    "ssl" => array(
        "verify_peer" => false,
        "verify_peer_name" => false,
    ),
);

// URL Construction
$url = "http://50.0.0.8/apimobile/request/request.php?key=" . urlencode(base64_encode($key)) .
       "&port=9991&ref=" . urlencode($uid) .
       "&" . urlencode($search_type) . "=" . urlencode($search_value);
//echo $url; exit;
// Get Response
$response = file_get_contents($url, false, stream_context_create($arrContextOptions));

if ($response === FALSE) {
    // Handle error
    die("Error fetching data from API");
}

$content = $response;
$test_array = json_decode($content, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    // Handle JSON error
    die("JSON Decode Error: " . json_last_error_msg());
}

            /*
             search_types:
             * msisdn
             * cnic
             * imsi
             * foreigner_profile
              ); */