<?php
 
 
$colors = array(
        'out_octet' => '#DD8E45',
        'in_octet' => '#4596DD',
        'in_discard' => '#DD8E45',
        'in_error' => '#4596DD',
        'out_discard' => '#D0DD45',
        'out_error' => '#DD457C',
        );

# We're shortening these because we don't know what the full device name will be.
$shortnames=array();
foreach ($NAME as $key => $name){
    $shortnames[$key] = preg_replace( '/[^_]*_/' ,'' , $name, 1 );

}
 
 
$opt[1] = "-l 0 --vertical-label \"Network Usage\"  --title \"Server $hostname\" ";
$def[1] = "" ;
$ds_name[2] = "Throughput";
$def[1] .= "DEF:var1=$rrdfile:$DS[1]:AVERAGE " ;
$def[1] .= "CDEF:inbit=var1  ";
$def[1] .= "LINE1:inbit".$colors[$shortnames[1]].": " ;
$def[1] .= "AREA:inbit".$colors[$shortnames[1]]."55:\"$shortnames[1]\t\" " ;

$def[1] .= "GPRINT:inbit:AVERAGE:\"%.0lf kbits Average\" ";
$def[1] .= "GPRINT:inbit:MAX:\"%.0lf kbits Max\" ";
$def[1] .= "GPRINT:inbit:LAST:\"%.0lf kbits Last\\n\" ";

$def[1] .= "DEF:var2=$rrdfile:$DS[2]:AVERAGE " ;
$def[1] .= "CDEF:outbit=var2  ";
$def[1] .= "LINE2:outbit".$colors[$shortnames[2]].": " ;
$def[1] .= "AREA:outbit".$colors[$shortnames[2]]."55:\"$shortnames[2]\t\" " ;
#
$def[1] .= "GPRINT:outbit:AVERAGE:\"%.0lf kbits Average\" ";
$def[1] .= "GPRINT:outbit:MAX:\"%.0lf kbits Max\" ";
$def[1] .= "GPRINT:outbit:LAST:\"%.0lf kbits Last\\n\" ";
 
 
 
 
$opt[2] = "-l 0 --vertical-label \"Network Errors\"  --title \"Error Packets for $hostname\" ";
$def[2] = "" ;
$ds_name[2] = "Throughput";
$keys = array(3,4,5,6);
foreach( $keys as $key){
    $def[2] .= "DEF:var$key=$rrdfile:$DS[$key]:AVERAGE " ;
    $def[2] .= "LINE$key:var$key".$colors[$shortnames[$key]].":$shortnames[$key]  " ;

    $def[2] .= "GPRINT:var$key:AVERAGE:\"%5.0lf Average\" ";
    $def[2] .= "GPRINT:var$key:MAX:\"%5.0lf Max\" ";
    $def[2] .= "GPRINT:var$key:LAST:\"%5.0lf Last\\n\" ";
}
 
 
?>

