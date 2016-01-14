<?php
 
$opt[1] = "-T 55 -l 0 ";
$ds_name[1] = "";
$def[1] = "";
 
$colors = array(
        'IO_Wait_Counter' => '#DD8E45',
        'user' => '#DDAF45',
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
        'no match' => '#AD2E75',
        );
$aliases = array(
        'user'           => 'User %',
        );


if ($NAME[1] == 'user'  ) {
    # Copyright (c)  2010 Yannig Perre (http://lesaventuresdeyannigdanslemondeit.blogspot.com)
    # Plugin: check_cpu 
	$opt[1] = "--upper-limit 100 --vertical-label CPU -l0  --title \"CPU activity on $hostname\" ";
	
	$trend_array = array(
	  "one_month"    => array(strtotime("-1 month", $this->TIMERANGE['end']), $this->TIMERANGE['end'], "1 month trend:dashes=10", "#FF007F", "line3"),
	  "global_trend" => array($this->TIMERANGE['start'], $this->TIMERANGE['end'], "Global trend\\n:dashes=20", "#707070", "line2"),
	);
	
	$def[1] =  rrd::def("usertime", $RRDFILE[1], $DS[1]);  #user
	$def[1] .= rrd::def("systime", $RRDFILE[2], $DS[2]);  #System
	$def[1] .= rrd::cdef("totaltime", "systime,usertime,+");
	$def[1] .= rrd::def("idletime", $RRDFILE[3], $DS[3]); #Idle
	
	$trends_graphic = "";
	
	foreach(array_keys($trend_array) as $trend) {
	  $def[1] .= rrd::def("usertime$trend", $RRDFILE[1], $DS[1], "AVERAGE:start=".$trend_array[$trend][0]);
	  $def[1] .= rrd::def("systime$trend", $RRDFILE[2], $DS[2], "AVERAGE:start=".$trend_array[$trend][0]);
	  $def[1] .= rrd::cdef("user$trend", "systime$trend,usertime$trend,+");
	
	  $def[1] .= rrd::vdef("dtrend$trend", "user$trend,LSLSLOPE");
	  $def[1] .= rrd::vdef("htrend$trend", "user$trend,LSLINT");
	  $def[1] .= rrd::cdef("curve_user$trend", "user$trend,POP,dtrend$trend,COUNT,*,htrend$trend,+");
	  $trends_graphic .= rrd::$trend_array[$trend][4]("curve_user$trend", $trend_array[$trend][3], $trend_array[$trend][2]);
	}
	
	if ($WARN[1] != "") { $def[1] .= rrd::hrule($WARN[1], "#FFFF00"); }
	if ($CRIT[1] != "") { $def[1] .= rrd::hrule($CRIT[1], "#FF0000"); }
	
	$def[1] .= rrd::area("totaltime", "#005CFF", "user  ");
	$def[1] .= rrd::gprint("usertime", "LAST", "%6.2lf ");
	$def[1] .= rrd::gprint("usertime", "AVERAGE", "avg %6.2lf");
	$def[1] .= rrd::gprint("usertime", "MAX", "max %6.2lf\\n");
	$def[1] .= rrd::area("systime", "#FF5C00", "sys   ");
	$def[1] .= rrd::gprint("systime", "LAST", "%6.2lf");
	$def[1] .= rrd::gprint("systime", "AVERAGE", "avg %6.2lf");
	$def[1] .= rrd::gprint("systime", "MAX", "max %6.2lf\\n");
	
	$def[1] .= rrd::line1("totaltime", "#000000", "Total");
	$def[1] .= rrd::gprint("totaltime", "LAST", " %6.2lf");
	$def[1] .= rrd::gprint("totaltime", "AVERAGE", "moy %6.2lf");
	$def[1] .= rrd::gprint("totaltime", "MAX", "max %6.2lf\\n");
	$def[1] .= rrd::line1("systime", "#000000");
	$def[1] .= $trends_graphic;


} elseif ($NAME[1] == 'IO_Wait_Counter' ) {
 
    $ds_name[1] .= "I/O Wait";
    $opt[1] = " --vertical-label \"CPU Ticks\"  --title \"$NAME[1] ($hostname) \" ";
    $def[1] .= "DEF:var1=$rrdfile:$DS[1]:AVERAGE " ;
    $def[1] .= "LINE1:var1".$colors[$NAME[1]].": " ;
    $def[1] .= "AREA:var1".$colors[$NAME[1]]."80:\"ticks delayed\t\" " ;
    $def[1] .= "GPRINT:var1:AVERAGE:\"%5.0lf Average\" ";
    $def[1] .= "GPRINT:var1:MAX:\"%5.0lf Max\" ";
    $def[1] .= "GPRINT:var1:LAST:\"%5.0lf Last\\n\" ";

    if ($WARN[1] != "") {
    	$def[1] .= "HRULE:$WARN[1]#FFFF00 ";
    }
    if ($CRIT[1] != "") {
    	$def[1] .= "HRULE:$CRIT[1]#FF0000 ";
    }
} elseif ($NAME[1] == 'users' ) {

 
    $ds_name[1] .= "Users";
    $opt[1] = " --vertical-label \"User Count\"  --title \"$NAME[1] ($hostname) \" ";
    $def[1] .= "DEF:var1=$rrdfile:$DS[1]:AVERAGE " ;
    $def[1] .= "LINE1:var1".$colors['user'].": " ;
    $def[1] .= "AREA:var1".$colors['user']."80:\"users\t\" " ;
    $def[1] .= "GPRINT:var1:AVERAGE:\"%5.0lf Average\" ";
    $def[1] .= "GPRINT:var1:MAX:\"%5.0lf Max\" ";
    $def[1] .= "GPRINT:var1:LAST:\"%5.0lf Last\\n\" ";

 } else {
 
    $ds_name[1] .= "SNMP Stats";
    $opt[1] = " --vertical-label \"SNMP Stats\"  --title \"$NAME[1] ($hostname) \" ";
    $def[1] .= "DEF:var1=$rrdfile:$DS[1]:AVERAGE " ;
    $def[1] .= "LINE1:var1".$colors['no match'].": " ;
    $def[1] .= "AREA:var1".$colors['no match']."80:\"units\t\" " ;
    $def[1] .= "GPRINT:var1:AVERAGE:\"%5.0lf Average\" ";
    $def[1] .= "GPRINT:var1:MAX:\"%5.0lf Max\" ";
    $def[1] .= "GPRINT:var1:LAST:\"%5.0lf Last\\n\" ";
 }

