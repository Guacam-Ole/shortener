<?php


    require ("connect.php");


    function CheckToken($pdo, $token) {
        $countQuery = $pdo->prepare("SELECT COUNT(*) AS numEntries FROM custom WHERE token=:token");
        $countQuery->bindParam(':token',$token);
        $countQuery ->execute();
        $row = $countQuery->fetch();
        return $row["numEntries"];
     }

    function GetCustomTokenUrl($pdo, $token) {
        $tokenQuery=$pdo->prepare('SELECT target, expire FROM custom WHERE token=:token');
        $tokenQuery->bindParam(':token',$token);
        $tokenQuery ->execute();
        $row = $tokenQuery->fetch();
        return $row;
    }

    function GetIdUrl($pdo, $id) {
        $tokenQuery=$pdo->prepare('SELECT target, expire  FROM calculated WHERE id=:id');
        $tokenQuery->bindParam(':id',$id);
        $tokenQuery ->execute();
        $row = $tokenQuery->fetch();
        return $row;
    }

    function ResetKeys($pdo) {
        $query=$pdo->prepare('SELECT currentMonth FROM housekeeping');
        $query->execute();
        $row=$query->fetch();
        $dbMonth=$row["currentMonth"];
        $realMonth=date('n');
        if ($realMonth!=$dbMonth) {
            $updateHouseKeeping=$pdo->prepare("UPDATE housekeeping SET  currentMonth=:month");
            $updateHouseKeeping->bindParam(':month', $realMonth);
            $updateHouseKeeping->execute();

            $updateKeys=$pdo->prepare("UPDATE userkeys SET  currentCount=0");
            $updateKeys->execute();
        }
    }

    function GetCount($pdo, $key, $name) {
        ResetKeys($pdo);
        $query=$pdo->prepare("SELECT currentCount, maxCount FROM userkeys WHERE userkey=:usrkey AND username=:usrname");
        $query->bindParam(':usrkey',$key);
        $query->bindParam(':usrname',$name);
        $query->execute();
        $row=$query->fetch();
        $currentCount=$row["currentCount"];
        $maxCount=$row["maxCount"];
        if ($currentCount<$row["maxCount"]) return $currentCount;
        return -1;
    }

    function Increment($pdo, $key, $name) {
        $oldCount=GetCount($pdo, $key, $name);
        if ($oldCount>=0) {
            $oldCount++;
            $update=$pdo->prepare("UPDATE userkeys SET currentCount=:count WHERE userkey=:usrkey AND username=:usrname");
            $update->bindParam(':count', $oldCount);
            $update->bindParam(':usrname', $name);
            $update->bindParam(':usrkey', $key);
            $update->execute();
        }
    }

    function GetIdStats($pdo, $id) {

        $dayofmonthQuery=$pdo->prepare("SELECT COUNT(*) as count , YEAR(accessdate) as year, MONTH(accessdate) as month, DAY(accessdate) as day FROM calculatedaccess WHERE id=:id AND accessdate >= (CURDATE() - INTERVAL 1 MONTH ) GROUP BY YEAR(accessdate), MONTH(accessdate), DAY(accessdate)  ORDER BY year, month, day ");
        $dayofmonthQuery->bindParam(':id',$id);
        $dayofmonthQuery ->execute();
        $row = $dayofmonthQuery->fetchAll();
        return $row;
    }

    function GetIdStatsMonth($pdo, $id) {
        $query=$pdo->prepare("SELECT COUNT(*) as count , YEAR(accessdate) as year, MONTH(accessdate) as month, DAY(accessdate) as day FROM calculatedaccess WHERE id=:id AND accessdate >= (CURDATE() - INTERVAL 1 YEAR ) GROUP BY YEAR(accessdate), MONTH(accessdate)  ORDER BY year, month ");
        $query->bindParam(':id',$id);
        $query ->execute();
        $row = $query->fetchAll();

        return $row;
    }

    function GetIdStatsYear($pdo, $id) {
        $query=$pdo->prepare("SELECT COUNT(*) as count , YEAR(accessdate) as year, MONTH(accessdate) as month, DAY(accessdate) as day FROM calculatedaccess WHERE id=:id GROUP BY YEAR(accessdate)  ORDER BY year ");
        $query->bindParam(':id',$id);
        $query ->execute();
        $row = $query->fetchAll();
        
        return $row;
    }

    function GetAllIdStats($pdo) {
        $dayofmonthQuery=$pdo->prepare("SELECT COUNT(*) as count , YEAR(accessdate) as year, MONTH(accessdate) as month, DAY(accessdate) as day FROM calculatedaccess WHERE accessdate >= (CURDATE() - INTERVAL 1 MONTH ) GROUP BY YEAR(accessdate), MONTH(accessdate), DAY(accessdate)  ORDER BY year, month, day ");
        $dayofmonthQuery ->execute();
        $row = $dayofmonthQuery->fetchAll();

        return $row; 
    }

    function GetAllIdStatsMonth($pdo) {
        $query=$pdo->prepare("SELECT COUNT(*) as count , YEAR(accessdate) as year, MONTH(accessdate) as month FROM calculatedaccess WHERE accessdate >= (CURDATE() - INTERVAL 1 YEAR ) GROUP BY YEAR(accessdate), MONTH(accessdate)  ORDER BY year, month ");
        $query ->execute();
        $row = $query->fetchAll();

        return $row;
    }

    function GetAllIdStatsYear($pdo) {
        $query=$pdo->prepare("SELECT COUNT(*) as count , YEAR(accessdate) as year FROM calculatedaccess GROUP BY YEAR(accessdate)  ORDER BY year ");
        $query ->execute();
        $row = $query->fetchAll();
        
        return $row;
    }

    function GetTokenStats($pdo, $token) {

//        echo "SELECT COUNT(*) as count , YEAR(accessdate) as year, MONTH(accessdate) as month, DAY(accessdate) as day FROM customaccess WHERE token=:token AND accessdate >= (CURDATE() - INTERVAL 1 MONTH )  GROUP BY YEAR(accessdate), MONTH(accessdate), DAY(accessdate) ORDER BY year, month, day ";
        $dayofmonthQuery=$pdo->prepare("SELECT COUNT(*) as count , YEAR(accessdate) as year, MONTH(accessdate) as month, DAY(accessdate) as day FROM customaccess WHERE token=:token AND accessdate >= (CURDATE() - INTERVAL 1 MONTH )  GROUP BY YEAR(accessdate), MONTH(accessdate), DAY(accessdate) ORDER BY year, month, day ");

        $dayofmonthQuery->bindParam(':token',$token);
        $dayofmonthQuery ->execute();
        $row = $dayofmonthQuery->fetchAll();

        return $row;
    }

    function GetTokenStatsMonth($pdo, $token) {
        $query=$pdo->prepare("SELECT COUNT(*) as count , YEAR(accessdate) as year, MONTH(accessdate) as month  FROM customaccess WHERE  token=:token  AND accessdate >= (CURDATE() - INTERVAL 1 YEAR ) GROUP BY YEAR(accessdate), MONTH(accessdate)  ORDER BY year, month ");
        $query->bindParam(':token',$token);
        $query ->execute();
        $row = $query->fetchAll();

        return $row;
    }

    function GetTokenStatsYear($pdo, $token) {
        $query=$pdo->prepare("SELECT COUNT(*) as count , YEAR(accessdate) as year  FROM customaccess WHERE  token=:token GROUP BY YEAR(accessdate)  ORDER BY year ");
        $query->bindParam(':token',$token);
        $query ->execute();
        $row = $query->fetchAll();
        
        return $row;
    }

    function GetAllTokenStats($pdo) {
        $dayofmonthQuery=$pdo->prepare("SELECT COUNT(*) as count , YEAR(accessdate) as year, MONTH(accessdate) as month, DAY(accessdate) as day FROM customaccess WHERE accessdate >= (CURDATE() - INTERVAL 1 MONTH ) GROUP BY YEAR(accessdate), MONTH(accessdate), DAY(accessdate) ORDER BY year, month, day ");
        $dayofmonthQuery ->execute();
        $row = $dayofmonthQuery->fetchAll();

        return $row;
    }

  function GetAllTokenStatsMonth($pdo) {
        $query=$pdo->prepare("SELECT COUNT(*) as count , YEAR(accessdate) as year, MONTH(accessdate) as month FROM customaccess WHERE accessdate >= (CURDATE() - INTERVAL 1 YEAR ) GROUP BY YEAR(accessdate), MONTH(accessdate)  ORDER BY year, month ");
        $query ->execute();
        $row = $query->fetchAll();

        return $row;
    }

    function GetAllTokenStatsYear($pdo) {
        $query=$pdo->prepare("SELECT COUNT(*) as count , YEAR(accessdate) as year FROM customaccess GROUP BY YEAR(accessdate)  ORDER BY year ");
        $query ->execute();
        $row = $query->fetchAll();
        
        return $row;
    }

    function GetAllCalculated($pdo) {
        $query=$pdo->prepare("SELECT id,target FROM calculated ");
        $query ->execute();
        $row = $query->fetchAll();
        
        return $row;
    }

    function GetAllCustom($pdo) {
        $query=$pdo->prepare("SELECT id,target FROM custom ");
        $query ->execute();
        $row = $query->fetchAll();
        
        return $row;
    }

    function AddCountId($pdo, $id) {
        $countQuery=$pdo->prepare("INSERT INTO calculatedaccess (id) VALUES (:id)");
        $countQuery->bindParam(':id', $id);
        $countQuery->execute();
    }

    function AddCountCustom($pdo, $token) {
        $countQuery=$pdo->prepare("INSERT INTO customaccess (token) VALUES (:token)");
        $countQuery->bindParam(':token', $token);
        $countQuery->execute();
    }

    function CleanDb($pdo) {
        // delete expired entries
        $now=date("Y-m-d H:i:s");
        $deleteQuery=$pdo->prepare("DELETE FROM custom WHERE expire<:now");
        $deleteQuery->bindParam(':now', $now);
        $deleteQuery->execute();

        $deleteQuery=$pdo->prepare("DELETE FROM calculated WHERE expire<:now");
        $deleteQuery->bindParam(':now', $now);
        $deleteQuery->execute();
     }
?>
