<?php
    //  $debug=false;
    // if (isset($_GET['debug'])) {
    //         $debug=true;
    // }
    if (!isset($_GET["token"])) {
        die("wrong turn");
    }
    $token=htmlspecialchars($_GET["token"]);

     require('helper.php');
     require('db.php');

     if (strlen($token)==0) {
             die("notoken");
         }

         $allStats=new stdClass();

         if (strlen($token)>250) {
             die ("yours is way too long! (don't hear that often, do you?)");
         }
         $pdo = Connect();

         if (strlen($token)<5) {
             // calculated token

            $id=DecodeUrl($token);
            $allStats->url=GetIdUrl($pdo, $id);
            $allStats->days=GetIdStats($pdo, $id);
            $allStats->months=GetIdStatsMonth($pdo, $id);
            $allStats->years=GetIdStatsYear($pdo,$id);
         } else {
             // custom token
             if ($token==='calculated') {
                $allStats->days=GetAllIdStats($pdo);
                $allStats->months=GetAllIdStatsMonth($pdo);
                $allStats->years=GetAllIdStatsYear($pdo);
             } else if ($token==='custom') {
                $allStats->days=GetAllTokenStats($pdo);
                $allStats->months=GetAllTokenStatsMonth($pdo);
                $allStats->years=GetAllTokenStatsYear($pdo);
             } else {
                $allStats->url=GetCustomTokenUrl($pdo, $token);
                $allStats->days=GetTokenStats($pdo, $token);
                $allStats->months=GetTokenStatsMonth($pdo, $token);
                $allStats->years=GetTokenStatsYear($pdo,$token);
             }
         }

         echo json_encode($allStats);

?>