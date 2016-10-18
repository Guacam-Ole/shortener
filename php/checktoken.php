<?php
/*    error_reporting(E_ALL);
    ini_set('display_errors', 1);*/

    require('helper.php');
    require('db.php');

    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    $token= htmlspecialchars($request->token);

    if (!isset($token)) {
        Error("Token missing", false);
    }
    if (strlen($token)<5) {
        Error("Token too short", false);
    }

    if (!preg_match("/^[^0][a-zA-Z0-9]*$/", $token ))  {
        Error("Wrong pattern",false);
    };

    $pdo = Connect();
    Success(CheckToken($pdo,$token),$token);
?>