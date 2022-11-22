<?php


    if (!isset($_GET["token"])) {
        die("wrong turn");
    }


    $token=htmlspecialchars($_GET["token"]);

    require('helper.php');
    require('db.php');

    if (strlen($token)==0) {
        die("notoken");
    }
    if (strlen($token)>250) {
        die ("yours is way too long! (don't hear that often, do you?)");
    }



    $pdo = Connect();
    CleanDb($pdo);

    if (strlen($token)<5) {
        // calculated token
        $id=DecodeUrl($token);

        $row=GetIdUrl($pdo, $id);
        $target=$row["target"];
        $expire=$row["expire"];

        if (!isset($target)) {
            header("Location:http:/404.html");
            exit();
        }
        AddCountId($pdo, $id);
    } else {
        // custom token
        $row=GetCustomTokenUrl($pdo, $token);
        $target=$row["target"];
        $expire=$row["expire"];
        if (!isset($target)) {
            header("Location:http:/404.html");
            exit();
        }
        AddCountCustom($pdo, $token);
    }

    if (IsEvilUrl($target, false)) {
        header("Location: https://github.com/OleAlbers/shortener/blob/master/docs/evil.de.md", true, 301);
        die();
    }

    if ($expire===NULL) {
        header("Location:".$target, true, 301);
    } else {
        header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
        header("Location:".$target, true, 302);
    }

?>