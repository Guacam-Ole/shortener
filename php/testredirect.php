<?php
 error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $hasRedirect=true;
    $url=$_GET["url"];
    $count=0;
    $maxCount=3;
    $badwords=["hurz.me","fade.to","rx.at"];



    while ($hasRedirect && $count<$maxCount) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Must be set to true so that PHP follows any "Location:" header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $a = curl_exec($ch); // $a will contain all headers

        $newurl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL); // This is what you need, it will return you the last effective URL
        $hasRedirect=$newurl!=$url;

        echo "url:'".$url."'\n";
        echo "newurl:'".$newurl."'\n";
        echo "hr:".$hasRedirect."\n";

        foreach($badwords as $badword) {
            if (stripos($newurl,$badword)!==false) {
                die ("Evil redirect");
            }

        }
        $url=$newurl;
         $count++;
    }
    if ($hasRedirect) {
        die("Neeeneeeneeene");
    }

    echo "Jo. Passt:".$url;


?>