<?php
// non-symfony routine to export 
// Stations table as PHP

$DBH = new PDO("mysql:host=localhost;dbname=roger", 'root');
$STH = $DBH->query("SELECT * FROM Station");
$STH->setFetchMode(PDO::FETCH_OBJ);

echo "<?php\n";
echo "\$stations = array(\n";
while ($row = $STH->fetch()) {
    $crs = $row->stn_crs;
    $name = $row->stn_name;
    echo "'$crs' => \"".addslashes($name)."\",\n";    
}
echo ");\n";
