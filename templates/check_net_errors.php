<?php
 
 
$colors = array(
        'InDiscard' => '#DD8E45',
        'InErrors' => '#4596DD',
        'OutDiscard' => '#D0DD45',
        'OutErrors' => '#DD457C',
        );
 
 
 
$opt[1] = "-l 0 --vertical-label \"Network Errors\"  --title \"Error Packets for $hostname\" ";
$def[1] = "" ;
$ds_name[2] = "Throughput";
$keys = array(1,2,3,4);
foreach( $keys as $key){
    $def[1] .= "DEF:var$key=$rrdfile:$DS[$key]:AVERAGE " ;
    $def[1] .= "LINE$key:var$key".$colors[$NAME[$key]].":$NAME[$key] " ;

    $def[1] .= "GPRINT:var$key:AVERAGE:\"%.0lf Average\" ";
    $def[1] .= "GPRINT:var$key:MAX:\"%.0lf Max\" ";
    $def[1] .= "GPRINT:var$key:LAST:\"%.0lf Last\\n\" ";
}
 
 
?>
