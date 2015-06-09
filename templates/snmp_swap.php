<?php
 
#FIXME not sure what's upw ith the capitalization here, some require it, others don't. 
$colors = array(
        'Swap_space' => '#DD8E45',
        'Swap_Space' => '#DD8E45',
        );
 
 
 
$opt[1] = "-l 0 --vertical-label \"Swap Usage\"  --title \"Server $hostname\" ";
$def[1] = "" ;
$ds_name[2] = "Throughput";
$def[1] .= "DEF:swap=$rrdfile:$DS[1]:AVERAGE " ;
$def[1] .= "LINE1:swap".$colors[$NAME[1]].": " ;
$def[1] .= "AREA:swap".$colors[$NAME[1]]."55:\"$NAME[1]\t\" " ;

$def[1] .= "GPRINT:swap:AVERAGE:\"%.0lf Mb Average\" ";
$def[1] .= "GPRINT:swap:MAX:\"%.0lf Mb Max\" ";
$def[1] .= "GPRINT:swap:LAST:\"%.0lf Mb Last\\n\" ";

 
 
?>
