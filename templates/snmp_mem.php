<?php
 
 
 
 
 
$opt[0] = "-l 0 --vertical-label \"Memory Usage\"  --title \"Server $hostname\" ";
$def[0] = "" ;
$ds_name[0] = "Memory Utilization";


$def[0] .= "DEF:var1=$rrdfile:$DS[1]:AVERAGE " ;
$def[0] .= "CDEF:ramused=var1,1024,*  ";
$def[0] .= "LINE1:ramused#DD8ED5: " ;
$def[0] .= "AREA:ramused#DD8ED555:\"$NAME[1]\t\" " ;
$def[0] .= "GPRINT:var1:AVERAGE:\"%.0lf KB Average\" ";
$def[0] .= "GPRINT:var1:MAX:\"%.0lf KB Max\" ";
$def[0] .= "GPRINT:var1:LAST:\"%.0lf KB Last\\n\" ";


$def[0] .= "DEF:var2=$rrdfile:$DS[2]:AVERAGE " ;
$def[0] .= "CDEF:swapused=var2,1024,*  ";
$def[0] .= "LINE1:swapused#118E45: " ;
$def[0] .= "AREA:swapused#118E4555:\"$NAME[2]\t\" " ;
$def[0] .= "GPRINT:var2:AVERAGE:\"%.0lf KB Average\" ";
$def[0] .= "GPRINT:var2:MAX:\"%.0lf KB Max\" ";
$def[0] .= "GPRINT:var2:LAST:\"%.0lf KB Last\\n\" ";

$MAX[1]=$MAX[1]*1024;
$def[0] .= "LINE1:$MAX[1]#FF00FF:\"Max Ram Available\" " ;
$MAX[2]=$MAX[2]*1024;
$def[0] .= "LINE1:$MAX[2]#11FFFF:\"Max Swap Available\\n\" " ;

 
?>
