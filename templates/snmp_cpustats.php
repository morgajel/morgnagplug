<?php
#
# Copyright (c)  2010 Yannig Perre (http://lesaventuresdeyannigdanslemondeit.blogspot.com)
# Plugin: check_cpu
#
 
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
?>
