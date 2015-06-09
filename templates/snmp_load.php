<?php
#
# Copyright (c)  2010 Yannig Perre (http://lesaventuresdeyannigdanslemondeit.blogspot.com)
# Plugin: check_Load
#
$ds_name[1] = "Load Activity";
 
$opt[1] = "--lower-limit 0 -r --vertical-label Load -L5  --title \"System Load on $hostname\" ";
 
$trend_array = array(
  "one_week"    => array(strtotime("-1 week", $this->TIMERANGE['end']), $this->TIMERANGE['end'], "1 week trend:dashes=5", "#7F007F", "line2"),
  "global_trend" => array($this->TIMERANGE['start'], $this->TIMERANGE['end'], "Global trend\\n:dashes=20", "#707070", "line2"),
);
 
$def[1] =  rrd::def("1min", $RRDFILE[1], $DS[1]);  #user
$def[1] .= rrd::def("5min", $RRDFILE[2], $DS[2]);  #System
$def[1] .= rrd::def("15min", $RRDFILE[3], $DS[3]); #Idle
 
$trends_graphic = "";
 
foreach(array_keys($trend_array) as $trend) {
  $def[1] .= rrd::def("1min$trend",  $RRDFILE[1], $DS[1], "AVERAGE:start=".$trend_array[$trend][0]);
  $def[1] .= rrd::def("5min$trend",  $RRDFILE[2], $DS[2], "AVERAGE:start=".$trend_array[$trend][0]);
  $def[1] .= rrd::def("15min$trend", $RRDFILE[3], $DS[3], "AVERAGE:start=".$trend_array[$trend][0]);
 
#  $def[1] .= rrd::vdef("dtrend$trend", "1min$trend,LSLSLOPE");
#  $def[1] .= rrd::vdef("htrend$trend", "1min$trend,LSLINT");
#  $def[1] .= rrd::cdef("curve_1min$trend", "1min$trend,POP,dtrend$trend,COUNT,*,htrend$trend,+");
#  $trends_graphic .= rrd::$trend_array[$trend][4]("curve_1min$trend", $trend_array[$trend][3], $trend_array[$trend][2]);
}
 
if ($WARN[1] != "") { $def[1] .= rrd::hrule($WARN[1], "#FFFF00"); }
if ($CRIT[1] != "") { $def[1] .= rrd::hrule($CRIT[1], "#FF0000"); }
 
#$def[1] .= rrd::area("1min", "#005CFFDD", "1min  ");
$def[1] .= rrd::gradient("1min", "#ffffffDD", "#005CFFDD", "1min    "."\\t");
$def[1] .= rrd::gprint("1min", "LAST", "%6.2lf");
$def[1] .= rrd::gprint("1min", "AVERAGE", "avg %6.2lf");
$def[1] .= rrd::gprint("1min", "MAX", "max %6.2lf\\n");
#$def[1] .= rrd::area("5min", "#FF5C00AA", "5min   ");
$def[1] .= rrd::gradient("5min", "#ffffffAA", "#FF5C00AA", "5min    "."\\t");
$def[1] .= rrd::gprint("5min", "LAST", "%6.2lf");
$def[1] .= rrd::gprint("5min", "AVERAGE", "avg %6.2lf");
$def[1] .= rrd::gprint("5min", "MAX", "max %6.2lf\\n");
#$def[1] .= rrd::area("15min", "#5C5C0088", "15min   ");
$def[1] .= rrd::gradient("15min", "#ffffff88", "#5C5C0088", "15min    "."\\t");
$def[1] .= rrd::gprint("15min", "LAST", "%6.2lf");
$def[1] .= rrd::gprint("15min", "AVERAGE", "avg %6.2lf");
$def[1] .= rrd::gprint("15min", "MAX", "max %6.2lf\\n");

$def[1] .= rrd::line1("15min", "#5C5C00");
$def[1] .= rrd::line1("5min", "#FF5C00");
$def[1] .= rrd::line1("1min", "#005CFF");
$def[1] .= $trends_graphic;
?>
