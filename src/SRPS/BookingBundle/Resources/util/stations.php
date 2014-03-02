<?php
// non-symfony routine to export 
// Stations table as PHP

$DBH = new PDO("mysql:host=localhost;dbname=roger", 'root');
$STH = $DBH->query("SELECT * FROM Station");
$STH->setFetchMode(PDO::FETCH_OBJ);

$stations = array();
while ($row = $STH->fetch()) {
    $crs = $row->stn_crs;
    $name = $row->stn_name;
    $stations[$crs] = $name;
}

$DB = new PDO("mysql:host=localhost;dbname=srpsbookings", 'root');
foreach ($stations as $crs => $name) {
    $STM = $DB->query("SELECT * FROM station WHERE crs='$crs'");
    if ($STM->columnCount()>0) {
        echo "$crs found\n";
    }
    else {
        echo "$crs not found\n";
        $sname = addslashes($name);
        if ($DB->exec("INSERT INTO station SET crs='$crs', name='$sname'")===false) {
            echo "insert failed $crs\n";
        }
    }
}
