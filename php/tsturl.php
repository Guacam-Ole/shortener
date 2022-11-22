<?php
    require("helper.php");
    $target=$_GET["url"];
    
    if (IsEvilUrl($target, true)) {
        echo "Diese URL ist böse!";
    }
?>