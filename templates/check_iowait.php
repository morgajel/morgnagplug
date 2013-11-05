<?php
 
$ds_name[0] = $servicedesc." ($hostname)";
 
$opt[0] = "--vertical-label \"Ticks Delayed\" -l 0 --title \"Ticks delayed over 5 minute intervals ($hostname)\" ";
$def[0]="";
# Memory definition
$def[0] .= rrd::def("var1",   $RRDFILE[1], $DS[1]);
$def[0] .= rrd::cdef($NAME[1], 'var1,' );


$def[0] .= rrd::gradient("var1", "#ffffff", "#33cccc", $NAME[1]."\\t\\t");
$def[0] .= rrd::line1(   "var1", "#339999" );
$def[0] .= rrd::gprint(  $NAME[1], "LAST",    "last\: %4.lf ticks\\t");
$def[0] .= rrd::gprint(  $NAME[1], "MAX",     "max\: %4.lf ticks\\t");
$def[0] .= rrd::gprint(  $NAME[1], "AVERAGE", "average\: %4.2lf ticks\\n"); 



?>
