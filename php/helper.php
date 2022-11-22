<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


    function Success($data, $url=null) {
        echo '{ "success": true, "value":"'.$data.'", "url":"'.$url.'"}';
    }

    function Error($error, $isinUse=false, $isbot=false) {
            die ('{ "success": false, "error":"'.$error.'", "isinuse":"'.$isinUse.'", "isbot":"'.$isbot.'"}');
    }

 /* ShortUrl (geklaut bei SO) */

    $alphabet = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $base = strlen($alphabet);

    


    function EncodeUrl($num) {
        global $alphabet, $base;

        $str = '';
        while ($num > 0) {
            $str = $alphabet[$num % $base] . $str;
            $num = floor($num / $base);
        }
        return $str;
    }


    function DecodeUrl($str) {
        global $alphabet, $base;
        $num = 0;
        for ($i = 0; $i < strlen($str); $i++) {
            $num = $num * $base + strpos($alphabet,$str[$i]);
        }
        return $num;
    }

    function IsEvilUrl($tsturl, $test) {
        $key="<ENTER YOUR KEY HERE>"; 
    
        $url = 'https://safebrowsing.googleapis.com/v4/threatMatches:find?key='.$key;
        $data = array(
            'client' => array('clientId'=> 'oles non-existing company', 'clientVersion'=>'0.1'), 
            'threatInfo' => array(
                'threatTypes'=>array('MALWARE', 'SOCIAL_ENGINEERING','UNWANTED_SOFTWARE'),
                'platformTypes'=> array('ANY_PLATFORM','ALL_PLATFORMS', 'ANDROID','WINDOWS','IOS','OSX','LINUX'),
                'threatEntryTypes'=> array('URL'),
                'threatEntries' => array('url'=>$tsturl)        
            ),
        );
        $data_json=json_encode($data);
        $ch=curl_init();
    
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response  = curl_exec($ch);
        curl_close($ch);
    
        if ($test) {
            echo $response;
        }
           
        if (strpos($response, 'matches')!==false) {
           // die ("evil");
            return true;

        } else {
            //die ("oki");
            return false;
        }
    }

    function CheckUrl($url) {
        $hasRedirect=true;

        $count=0;
        $maxCount=3;
        $badwords=["hurz.me","fade.to","rx.at"];

        while ($hasRedirect && $count<$maxCount) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $a = curl_exec($ch);

            $newurl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            $hasRedirect=$newurl!=$url;

            foreach($badwords as $badword) {
                if (stripos($newurl,$badword)!==false) {
                    Error("Redirect-loops not allowed");
                }
            }
            $url=$newurl;
            $count++;
        }
        if ($hasRedirect) {
            Error("too many redirects");
        }
    }

?>