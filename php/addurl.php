<?php
  /*  error_reporting(E_ALL);
    ini_set('display_errors', 1);*/

    require('helper.php');
    require('db.php');

    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);

    if ($request->debug) {
        print_r($request);
    }


    $first=htmlspecialchars($request->checks->first);
    $second=htmlspecialchars($request->checks->second);

    if (!$first || $second) {
        Error("I hate spammers",false);
    }

    if (!isset($request) || !isset($request->target) || !isset($request->checks) || !isset($request->token) || !isset($request->expiration)) {
        die();
    }
    if (!isset($request->target->value)) {
        Error("Target mising");
    }

    $target=htmlspecialchars($request->target->value);

    CheckUrl($target);

    if (stripos($target,'hurz.me')!==false || stripos($target,'fade.at')!==false || stripos($target,'rx8.to')!==false) {
        Error("nice try");
        }
    $expiration=$request->expiration;
    $token=$request->token;

    if (filter_var($target, FILTER_VALIDATE_URL) === FALSE) {
        die('Not a valid URL');
    }

    if (!substr(strtolower($target), 0, 4) === 'http') {
        die('Not a valid URL');
    }


    if ($token->enabled) {
        if (!$expiration->enabled || !isset($expiration->days) || !isset($expiration->hours) || !isset($expiration->minutes)) {
            Error ("nice try. Expiration required on custom url");
        }
        if (!isset($token->value)) {
            Error ("token missing");
        }
    }

    if ($expiration->enabled && (!isset($expiration->days) || !isset($expiration->hours) || !isset($expiration->minutes))) {
        die ("expiration error");
    }


    $pdo = Connect();

    if (!$expiration->enabled) {
        // Infinite insert
        $insertQuery=$pdo->prepare("INSERT INTO calculated (target) VALUES (:target)");
    } else {
        // Expiration
        $now = new DateTime();

        if (!is_numeric($expiration->days) || !is_numeric($expiration->hours) || !is_numeric($expiration->minutes)) {
            die ("expiration must be numeric");
        }
        if ($expiration->days<0 || $expiration->hours<0 || $expiration->minutes<0) {
            die("McFly? Anybody home? The Flux Compensator is broken. No chance to go back in time");
        }
        if ($expiration->days==0 && $expiration->hours==0 && $expiration->minutes<5) {
            die("The minimum is 5 Minutes");
        }

        $expire=$now->add(new DateInterval('P'.$expiration->days.'DT'.$expiration->hours.'H'.$expiration->minutes.'M'));
        $expireStr=$expire->format("Y-m-d H:i:s");
        if ($request->debug) {
            print_r($expire);
            print_r($expireStr);
        }

        if ($token->enabled) {
            $insertQuery=$pdo->prepare("INSERT INTO custom (target, expire, token) VALUES (:target, :expire, :token)");
            $insertQuery->bindParam(':expire',$expireStr);
            $insertQuery->bindParam(':token',$token->value);
        } else {
           $insertQuery=$pdo->prepare("INSERT INTO calculated (target, expire) VALUES (:target, :expire)");
            $insertQuery->bindParam(':expire',$expireStr);
        }
    }
    $insertQuery->bindParam(':target',$request->target->value);
    $insertQuery->execute();
    $newId=$pdo->lastInsertId();
    if ($newId==0) {
        Error("Already in use",true);
    } else {
        if ($token->enabled) {
            Success("Saved",$token->value);
        } else {
            Success("Saved",EncodeUrl($newId));
        }
    }

?>