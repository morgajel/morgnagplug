<?php
 
$opt[1] = "-T 55 -l 0 --vertical-label \"Apache Stats\"  --title \"Apache Statistics\" ";
$ds_name[1] = "Thread Statistics";
$def[1] = "";
 
$colors = array(
        'Waiting_for_Connection' => '#DD8E45',
        'Starting_Up' => '#DDAF45',
        'Reading_Request' => '#D0DD45',
        'Sending_Reply' => '#DD457C',
        'Keepalive_(read)' => '#45DD99',
        'DNS_Lookup' => '#45C5DD',
        'Closing_Connection' => '#4596DD',
        'Logging' => '#4557DD',
        'Gracefully_finishing' => '#A445DD',
        'Idle_cleanup' => '#DD45D8',
		'Open_slot' => '#FFaa00',
		'Requests_sec' => '#ff3300',
		'kB_per_sec' => '#33ff00',
		'kB_per_Request' => '#aaff00',
        );
 
$def[1] = "DEF:var12=$rrdfile:$DS[12]:AVERAGE " ;
$def[1] .= "LINE1:var12#00000080: " ;
$def[1] .= "AREA:var12#00FF6680:\"slots\t\" " ;
$def[1] .= "GPRINT:var12:AVERAGE:\"%.0lf Average\" ";
$def[1] .= "GPRINT:var12:MAX:\"%.0lf Max\" ";
$def[1] .= "GPRINT:var12:LAST:\"%.0lf Last\\n\" ";
 
$keys = array(4,3,2,5,6,7,8,9,10,11);
foreach( $keys  as $key){
    if ($key != 12){
        $def[1] .= "DEF:var$key=$rrdfile:$DS[$key]:AVERAGE " ;
        $def[1] .= "STACK:var$key".$colors[$NAME[$key]].":\"$NAME[$key]\t\" " ;
        $def[1] .= "GPRINT:var$key:AVERAGE:\"%.0lf Average\" ";
        $def[1] .= "GPRINT:var$key:MAX:\"%.0lf Max\" ";
        $def[1] .= "GPRINT:var$key:LAST:\"%.0lf Last\\n\" ";
    }
}
 
$opt[2] = "-l 0 --vertical-label \"Apache Requests\"  --title \"Server $hostname\" ";
$ds_name[2] = "Request per Second";
$def[2]  = "DEF:var1=$rrdfile:$DS[1]:AVERAGE " ;
$def[2] .= "LINE1:var1#000000:\"Requests per Second\" " ;
$def[2] .= "GPRINT:var1:AVERAGE:\"%.2lf Average\" ";
$def[2] .= "GPRINT:var1:MAX:\"%.2lf Max\" ";
$def[2] .= "GPRINT:var1:LAST:\"%.2lf Last\\n\" ";
 
 
 
$opt[3] = "-l 0 --vertical-label \"Apache Throughput\"  --title \"Server $hostname\" ";
$def[3] = "" ;
$keys = array(13,14);
$ds_name[2] = "Throughput";
$index=0;
foreach( $keys  as $key){
        $def[3] .= "DEF:var$key=$rrdfile:$DS[$key]:AVERAGE " ;
        $def[3] .= "LINE$index:var$key#00000080: " ;
        $def[3] .= "STACK:var$key".$colors[$NAME[$key]].":\"$NAME[$key]\t\" " ;
        $def[3] .= "GPRINT:var$key:AVERAGE:\"%.0lf Average\" ";
        $def[3] .= "GPRINT:var$key:MAX:\"%.0lf Max\" ";
        $def[3] .= "GPRINT:var$key:LAST:\"%.0lf Last\\n\" ";
$index++;
}
 
?>
