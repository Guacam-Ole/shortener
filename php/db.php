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

    function GetIdStats($pdo, $id) {
        $dayofmonthQuery=$pdo->prepare("SELECT COUNT(*) as count , YEAR(accessdate) as year, MONTH(accessdate) as month, DAY(accessdate) as day FROM calculatedaccess WHERE id=:id GROUP BY YEAR(accessdate), MONTH(accessdate), DAY(accessdate)  ORDER BY day, month, year ");
        $dayofmonthQuery->bindParam(':id',$id);
        $dayofmonthQuery ->execute();
        $row = $dayofmonthQuery->fetchAll();

        return $row;
    }

    function GetAllIdStats($pdo) {
        $dayofmonthQuery=$pdo->prepare("SELECT COUNT(*) as count , YEAR(accessdate) as year, MONTH(accessdate) as month, DAY(accessdate) as day FROM calculatedaccess GROUP BY YEAR(accessdate), MONTH(accessdate), DAY(accessdate)  ORDER BY day, month, year ");
        $dayofmonthQuery ->execute();
        $row = $dayofmonthQuery->fetchAll();

        return $row;
    }

    function GetTokenStats($pdo, $token) {
        $dayofmonthQuery=$pdo->prepare("SELECT COUNT(*) as count , YEAR(accessdate) as year, MONTH(accessdate) as month, DAY(accessdate) as day FROM customaccess WHERE token=:token  GROUP BY YEAR(accessdate), MONTH(accessdate), DAY(accessdate) ORDER BY day, month, year ");

        $dayofmonthQuery->bindParam(':token',$token);
        $dayofmonthQuery ->execute();
        $row = $dayofmonthQuery->fetchAll();

        return $row;
    }

    function GetAllTokenStats($pdo) {
        $dayofmonthQuery=$pdo->prepare("SELECT COUNT(*) as count , YEAR(accessdate) as year, MONTH(accessdate) as month, DAY(accessdate) as day FROM customaccess GROUP BY YEAR(accessdate), MONTH(accessdate), DAY(accessdate) ORDER BY day, month, year ");
        $dayofmonthQuery ->execute();
        $row = $dayofmonthQuery->fetchAll();

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