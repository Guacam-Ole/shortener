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

         if (strlen($token)<5) {
             // calculated token

            $id=DecodeUrl($token);
            $stats=GetIdStats($pdo, $id);
         } else {
             // custom token
             if ($token==='calculated') {
                $stats=GetAllIdStats($pdo);
             } else if ($token==='custom') {
                $stats=GetAllTokenStats($pdo);
             } else {
                $stats=GetTokenStats($pdo, $token);
             }
         }

         echo json_encode($stats);

?>