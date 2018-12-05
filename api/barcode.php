<?php
$data = json_decode(file_get_contents('php://input'), true);
$barcode = $data["barcode"];

$params = array ('VAL' => $barcode, 'TYPE' => '1', 'lang' => 'el');
$query = http_build_query ($params);

// Create Http context details
$contextData = array ( 
    'http' => array (
            'method' => 'POST',
            'header' => "Connection: close\r\n".
                        "Content-Length: ".strlen($query)."\r\n".
                        "Content-type: application/x-www-form-urlencoded\r\n",
            'content'=> $query 
        ), 
    "ssl" => array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
            ),  
);

// Create context resource for our request
$context = stream_context_create ($contextData);

// Read page rendered as result of your POST request
$result =  file_get_contents (
    'https://www.i520.gr/index.php',  // page url
    false,
    $context);

// Server response is now stored in $result variable so you can process it
$dom = new DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTML($result);
    $xpath = new DOMXpath($dom);
    // Get result
    $elements = $xpath->query("//tr[@class='altrow']/td[@align='left']/em");
    if ($elements->length != 0) {
        
        echo json_encode([
                "found" => true,
                "description" => $elements[0]->nodeValue
            ]);
    } else {
        echo json_encode([
                "found" => false,
                "description" => ""
            ]);
    }
?>