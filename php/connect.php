<?php
function Connect() {
        $dbHost="localhost";
        $dbName="<DATABASE>";
        $dbUser="<USER>";
        $dbPass="<PASSWORD>";

        return new PDO('mysql:host='.$dbHost.';dbname='.$dbName, $dbUser, $dbPass);
    }
?>