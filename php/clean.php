<?php
 require('helper.php');
 require('db.php');
 $pdo = Connect();
echo "<pre>";
 $rows=GetAllCustom($pdo);
 $evilCount=0;
 foreach ($rows as $row) {
    $target=$row["target"];
    $id=$row["id"];
    if (IsEvilUrl($target,false)) {
        echo "\nEVIL: ".$id." ".$target;
        $evilCount++;
    }
 }

 echo "\n\n".$evilCount." evil URLs in custom";


 $rows=GetAllCalculated($pdo);
 $evilCount=0;
 foreach ($rows as $row) {
    $target=$row["target"];
    $id=$row["id"];
    if (IsEvilUrl($target,false)) {
        echo "\nEVIL: ".$id." ".$target;
        $evilCount++;
    }
 }

 echo "\n\n".$evilCount." evil URLs in calculated";
echo "</pre>";
 ?>
