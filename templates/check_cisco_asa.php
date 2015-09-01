<?php
 
 
$colors = array(
        'status' => '#35B5DD',
        'InOctets' => '#45C5DD',
		'OutOctets' => '#FFaa00',
		'InErrors'  => '#FF1611',
		'OutErrors' => '#DDDD11',
        );
 
$opt[1] = "-T 55 -l 0 --vertical-label \"Bandwidth Used\"  --title \"Traffic Utilization\" ";
$ds_name[1] = "Traffic Details";
$def[1] = "";
$keys = array(2,3);
foreach( $keys  as $key){
        $def[1] .= "DEF:var$key=$rrdfile:$DS[$key]:AVERAGE " ;
        $def[1] .= "LINE1:var$key".$colors[$NAME[$key]].": " ;
        $def[1] .= "AREA:var$key".$colors[$NAME[$key]]."55:\"$NAME[$key] \t\" " ;

        $def[1] .= "CDEF:bit$key=var$key,1024,/,1024,/  ";
        $def[1] .= "GPRINT:bit$key:AVERAGE:\"%.2lf MB Average\" ";
        $def[1] .= "GPRINT:bit$key:MAX:\"%.2lf MB Max\" ";
        $def[1] .= "GPRINT:bit$key:LAST:\"%.2lf MB Last\\n\" ";

}

$opt[2] = "-T 55 -l 0 --vertical-label \"Error Count\"  --title \"Errors\" ";
$ds_name[2] = "Errors";
$def[2] = "";
$keys = array(4,5);
foreach( $keys  as $key){
        $def[2] .= "DEF:var$key=$rrdfile:$DS[$key]:AVERAGE " ;
        $def[2] .= "LINE1:var$key".$colors[$NAME[$key]].":\"$NAME[$key] \t\" " ;

        $def[2] .= "CDEF:bit$key=var$key,1024,/,1024,/  ";
        $def[2] .= "GPRINT:bit$key:AVERAGE:\"%.0lf Average\" ";
        $def[2] .= "GPRINT:bit$key:MAX:\"%.0lf Max\" ";
        $def[2] .= "GPRINT:bit$key:LAST:\"%.0lf Last\\n\" ";

}

 
$opt[3] = "-l 0  --title \"Uptime\" --vertical-label \"% available\" ";
$ds_name[3] = "Uptime";
$def[3]  = "DEF:var1=$rrdfile:$DS[1]:AVERAGE " ;
$def[3] .= "CDEF:uptime1=var1,100,* ";
$def[3] .= "LINE2:uptime1".$colors[$NAME[1]].": " ;
$def[3] .= "AREA:uptime1".$colors[$NAME[1]]."55:\"$NAME[1] \" " ;
$def[3] .= "GPRINT:uptime1:AVERAGE:\"%3.0lf%% Average\\n\" ";
$def[3] .= "GPRINT:uptime1:MAX:\"%3.0lf%% Max\\n\" ";
$def[3] .= "GPRINT:uptime1:LAST:\"%3.0lf%% Last\\n\" ";
 
 
 
 
?>
