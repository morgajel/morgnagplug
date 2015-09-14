<?php
 
$opt[1] = "-T 55 -l 0 --vertical-label \"SNMP Stats\"  --title \"SNMP Statistics ($hostname) \" ";
$ds_name[1] = "SNMP Stats";
$def[1] = "";
 
$colors = array(
        'IO_Wait_Counter' => '#DD8E45',
#        'Starting_Up' => '#DDAF45',
#        'Reading_Request' => '#D0DD45',
#        'Sending_Reply' => '#DD457C',
#        'Keepalive_(read)' => '#45DD99',
#        'DNS_Lookup' => '#45C5DD',
#        'Closing_Connection' => '#4596DD',
#        'Logging' => '#4557DD',
#        'Gracefully_finishing' => '#A445DD',
#        'Idle_cleanup' => '#DD45D8',
#		'Open_slot' => '#FFaa00',
#		'Requests_sec' => '#ff3300',
#		'kB_per_sec' => '#33ff00',
#		'kB_per_Request' => '#aaff00',
        );
$aliases = array(
        'IO_Wait_Counter'           => 'IO Wait',
        );
 
$def[1] = "DEF:var1=$rrdfile:$DS[1]:AVERAGE " ;
$def[1] .= "LINE1:var1".$colors[$NAME[1]].": " ;
$def[1] .= "AREA:var1".$colors[$NAME[1]]."80:\"ticks delayed\t\" " ;
$def[1] .= "GPRINT:var1:AVERAGE:\"%5.0lf Average\" ";
$def[1] .= "GPRINT:var1:MAX:\"%5.0lf Max\" ";
$def[1] .= "GPRINT:var1:LAST:\"%5.0lf Last\\n\" ";
 
