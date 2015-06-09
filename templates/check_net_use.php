<?php
 
 
$colors = array(
        'OutOctets' => '#DD8E45',
        'InOctets' => '#4596DD',
        );
 
 
 
$opt[1] = "-l 0 --vertical-label \"Network Usage\"  --title \"Server $hostname\" ";
$def[1] = "" ;
$ds_name[2] = "Throughput";
$def[1] .= "DEF:var1=$rrdfile:$DS[1]:AVERAGE " ;
$def[1] .= "CDEF:inbit=var1,1024,/  ";
$def[1] .= "LINE1:inbit".$colors[$NAME[1]].": " ;
$def[1] .= "AREA:inbit".$colors[$NAME[1]]."55:\"$NAME[1]\t\" " ;

$def[1] .= "GPRINT:inbit:AVERAGE:\"%.0lf kbits Average\" ";
$def[1] .= "GPRINT:inbit:MAX:\"%.0lf kbits Max\" ";
$def[1] .= "GPRINT:inbit:LAST:\"%.0lf kbits Last\\n\" ";

$def[1] .= "DEF:var2=$rrdfile:$DS[2]:AVERAGE " ;
$def[1] .= "CDEF:outbit=var2,1024,/  ";
$def[1] .= "LINE2:outbit".$colors[$NAME[2]].": " ;
$def[1] .= "AREA:outbit".$colors[$NAME[2]]."55:\"$NAME[2]\t\" " ;
#
$def[1] .= "GPRINT:outbit:AVERAGE:\"%.0lf kbits Average\" ";
$def[1] .= "GPRINT:outbit:MAX:\"%.0lf kbits Max\" ";
$def[1] .= "GPRINT:outbit:LAST:\"%.0lf kbits Last\\n\" ";
 
 
?>
