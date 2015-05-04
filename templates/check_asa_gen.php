<?php
#
# Copyright (c)  2010 Yannig Perre (http://lesaventuresdeyannigdanslemondeit.blogspot.com)
# Plugin: check_Load
#
$ds_name[1] = "Load Activity";
 
$opt[1] = "--lower-limit 0 -r --vertical-label Utilization  -L5  --title \"CPU Utilizationon $hostname\" ";
 
$trend_array = array(
  "one_week"    => array(strtotime("-1 week", $this->TIMERANGE['end']), $this->TIMERANGE['end'], "1 week trend:dashes=5", "#7F007F", "line2"),
  "global_trend" => array($this->TIMERANGE['start'], $this->TIMERANGE['end'], "Global trend\\n:dashes=20", "#707070", "line2"),
);
 
$def[1] =  rrd::def("5sec", $RRDFILE[8], $DS[8]);  #user
$def[1] .= rrd::def("1min", $RRDFILE[9], $DS[9]);  #System
$def[1] .= rrd::def("5min", $RRDFILE[10], $DS[10]); #Idle
 
$trends_graphic = "";
 
foreach(array_keys($trend_array) as $trend) {
  $def[1] .= rrd::def("5sec$trend",  $RRDFILE[8], $DS[8], "AVERAGE:start=".$trend_array[$trend][0]);
  $def[1] .= rrd::def("1min$trend",  $RRDFILE[9], $DS[9], "AVERAGE:start=".$trend_array[$trend][0]);
  $def[1] .= rrd::def("5min$trend", $RRDFILE[10], $DS[10], "AVERAGE:start=".$trend_array[$trend][0]);
 
#  $def[1] .= rrd::vdef("dtrend$trend", "5sec$trend,LSLSLOPE");
#  $def[1] .= rrd::vdef("htrend$trend", "5sec$trend,LSLINT");
#  $def[1] .= rrd::cdef("curve_5sec$trend", "5sec$trend,POP,dtrend$trend,COUNT,*,htrend$trend,+");
#  $trends_graphic .= rrd::$trend_array[$trend][4]("curve_5sec$trend", $trend_array[$trend][3], $trend_array[$trend][2]);
}
 
if ($WARN[1] != "") { $def[1] .= rrd::hrule($WARN[1], "#FFFF00"); }
if ($CRIT[1] != "") { $def[1] .= rrd::hrule($CRIT[1], "#FF0000"); }
 
#$def[1] .= rrd::area("5sec", "#005CFFDD", "5sec  ");
$def[1] .= rrd::gradient("5sec", "#ffffffDD", "#005CFFDD", "5sec");
$def[1] .= rrd::gprint("5sec", "LAST", "%6.2lf%%");
$def[1] .= rrd::gprint("5sec", "AVERAGE", "avg %6.2lf%%");
$def[1] .= rrd::gprint("5sec", "MAX", "max %6.2lf%%\\n");
#$def[1] .= rrd::area("1min", "#FF5C00AA", "1min   ");
$def[1] .= rrd::gradient("1min", "#ffffffAA", "#FF5C00AA", "1min");
$def[1] .= rrd::gprint("1min", "LAST", "%6.2lf%%");
$def[1] .= rrd::gprint("1min", "AVERAGE", "avg %6.2lf%%");
$def[1] .= rrd::gprint("1min", "MAX", "max %6.2lf%%\\n");
#$def[1] .= rrd::area("5min", "#5C5C0088", "5min   ");
$def[1] .= rrd::gradient("5min", "#ffffff88", "#5C5C0088", "5min");
$def[1] .= rrd::gprint("5min", "LAST", "%6.2lf%%");
$def[1] .= rrd::gprint("5min", "AVERAGE", "avg %6.2lf%%");
$def[1] .= rrd::gprint("5min", "MAX", "max %6.2lf%%\\n");

$def[1] .= rrd::line1("5min", "#5C5C00");
$def[1] .= rrd::line1("1min", "#FF5C00");
$def[1] .= rrd::line1("5sec", "#005CFF");
$def[1] .= $trends_graphic;

#global_active_conn=1190;; global_setup_rate1=14;; global_setup_rate5=9;; setup_rate1_udp=4;; setup_rate1_tcp=10;; setup_rate5_udp=4;; setup_rate5_tcp=4;; cpu_5sec=6;; cpu_1min=5;; cpu_5min=5;; sysMemUsed=227921232;; sysMemFree=845820408;; sysMemLargestFree=761269832;;
$ds_name[2] = $servicedesc." ($hostname)";
 
$opt[2] = "--vertical-label \"Connections \" -l 0 --title \"".$ds_name[2]."\" ";
$def[2]="";
# Memory definition
$NAME[1]='global_active_conn';

$def[2] .= rrd::def("var1",   $RRDFILE[1], $DS[1]);



$def[2] .= rrd::line2( "var1", "#33cccc", 'Connections'."\\t");
$def[2] .= rrd::gprint("var1", "LAST",    "last\: %4.lf \\t");
$def[2] .= rrd::gprint("var1", "MAX",     "max\: %4.lf \\t");
$def[2] .= rrd::gprint("var1", "AVERAGE", "average\: %4.lf \\n"); 




$ds_name[0] = $servicedesc." ($hostname)";
 
$opt[0] = "--vertical-label \"Memory Usage \" -l 0 --title \"".$ds_name[0]."\" ";
$def[0]="";
# Memory definition
$NAME[1]='sysMemUsed';
$NAME[2]='sysMemFree';

$def[0] .= rrd::def("var1",   $RRDFILE[1], $DS[11]);
$def[0] .= rrd::def("var2",   $RRDFILE[1], $DS[12]);
$def[0] .= rrd::cdef($NAME[1], 'var1,1024,/,1024,/' );
$def[0] .= rrd::cdef($NAME[2], 'var2,1024,/,1024,/' );



$def[0] .= rrd::area("var1", "#33cccc", $NAME[1]."\\t",1);
$def[0] .= rrd::gprint(  $NAME[1], "LAST",    "last\: %4.lf mb\\t");
$def[0] .= rrd::gprint(  $NAME[1], "MAX",     "max\: %4.lf mb\\t");
$def[0] .= rrd::gprint(  $NAME[1], "AVERAGE", "average\: %4.2lf mb\\n"); 

$def[0] .= rrd::area("var2", "#ccaaaa", $NAME[2]."\\t",1);
$def[0] .= rrd::gprint(  $NAME[2], "LAST",    "last\: %4.lf mb\\t");
$def[0] .= rrd::gprint(  $NAME[2], "MAX",     "max\: %4.lf mb\\t");
$def[0] .= rrd::gprint(  $NAME[2], "AVERAGE", "average\: %4.2lf mb\\n"); 


?>





