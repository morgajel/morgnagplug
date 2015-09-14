<?php
 
$ds_name[0] = $servicedesc." ($hostname)";
 
$opt[0] = "--vertical-label \"Sessions\" -l 0 --title \"Sessions ($hostname)\" --upper-limit=$MAX[1] ";
$def[0]="";
# Memory definition
$def[0] .= rrd::def("var1",   $RRDFILE[1], $DS[1]);
$def[0] .= rrd::cdef($NAME[1], 'var1,' );


$def[0] .= rrd::gradient("var1", "#ffffff", "#33cccc", $NAME[1]."\\t\\t");
$def[0] .= rrd::line1(   "var1", "#339999" );
$def[0] .= rrd::gprint(  $NAME[1], "LAST",    "last\: %4.lf sessions\\t");
$def[0] .= rrd::gprint(  $NAME[1], "MAX",     "max\: %4.lf sessions\\t");
$def[0] .= rrd::gprint(  $NAME[1], "AVERAGE", "average\: %4.2lf sessions\\n"); 

if ($WARN[1] != "") {
    $def[0] .= "HRULE:$WARN[1]#FFFF00 ";
}
if ($CRIT[1] != "") {
    $def[0] .= "HRULE:$CRIT[1]#FF0000 ";
}


?>
