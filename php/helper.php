<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


    function Success($data, $url=null) {
        echo '{ "success": true, "value":"'.$data.'", "url":"'.$url.'"}';
    }

    function Error($error, $isinUse=false) {
            die ('{ "success": false, "error":"'.$error.'", "isinuse":"'.$isinUse.'"}');
    }

 /* ShortUrl (geklaut bei SO) */

    $alphabet = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNPPQRSTUVWXYZ';
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