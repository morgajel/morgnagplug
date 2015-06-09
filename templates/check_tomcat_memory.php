<?php
 
$ds_name[0] = $servicedesc." ($hostname)";
 
$opt[0] = "--vertical-label \"Memory Usage \" -l 0 --title \"".$ds_name[0]."\" ";
$def[0]="";
# Memory definition
$def[0] .= rrd::def("var1",   $RRDFILE[1], $DS[1]);
$def[0] .= rrd::def("var2",   $RRDFILE[1], $DS[2]);
$def[0] .= rrd::def("var3",   $RRDFILE[1], $DS[3]);
$def[0] .= rrd::cdef($NAME[1], 'var1,1024,/,1024,/' );
$def[0] .= rrd::cdef($NAME[2], 'var2,1024,/,1024,/' );
$def[0] .= rrd::cdef($NAME[3], 'var3,1024,/,1024,/' );

$def[0] .= rrd::gradient("var3", "#ffffff", "#cceeee", $NAME[3]."\\t\\t");
$def[0] .= rrd::line1(   "var3", "#ddcccc");
$def[0] .= rrd::gprint(  $NAME[3], "LAST",    "last\: %4.lf mb\\t");
$def[0] .= rrd::gprint(  $NAME[3], "MAX",     "max\: %4.lf mb\\t");
$def[0] .= rrd::gprint(  $NAME[3], "AVERAGE", "average\: %4.2lf mb\\n"); 

$def[0] .= rrd::gradient("var2", "#ffffff", "#ccaaaa", $NAME[2]."\\t");
$def[0] .= rrd::line1(   "var2", "#aacccc");
$def[0] .= rrd::gprint(  $NAME[2], "LAST",    "last\: %4.lf mb\\t");
$def[0] .= rrd::gprint(  $NAME[2], "MAX",     "max\: %4.lf mb\\t");
$def[0] .= rrd::gprint(  $NAME[2], "AVERAGE", "average\: %4.2lf mb\\n"); 

$def[0] .= rrd::gradient("var1", "#ffffff", "#33cccc", $NAME[1]."\\t\\t");
$def[0] .= rrd::line1(   "var1", "#339999" );
$def[0] .= rrd::gprint(  $NAME[1], "LAST",    "last\: %4.lf mb\\t");
$def[0] .= rrd::gprint(  $NAME[1], "MAX",     "max\: %4.lf mb\\t");
$def[0] .= rrd::gprint(  $NAME[1], "AVERAGE", "average\: %4.2lf mb\\n"); 



?>
