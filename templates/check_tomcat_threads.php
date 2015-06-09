<?php
  
$ds_name[0] = $servicedesc." ($hostname)";
  
$opt[0] = "--vertical-label \"Thread usage\" -l 0 --title \"".$ds_name[0]."\" ";
$def[0]="";
$def[0] .= rrd::def("var1",   $RRDFILE[1], $DS[1]);
$def[0] .= rrd::def("var2",   $RRDFILE[1], $DS[2]);
$def[0] .= rrd::cdef($NAME[1], 'var1' );
$def[0] .= rrd::cdef($NAME[2], 'var2' );
 
$def[0] .= rrd::gradient($NAME[2], "#ffffff", "#ccaaaa", $NAME[2]."\\t");
$def[0] .= rrd::line1(   $NAME[2], "#aacccc");
$def[0] .= rrd::gprint(  $NAME[2], "LAST",    "last\: %4.lf\\t");
$def[0] .= rrd::gprint(  $NAME[2], "MAX",     "max\: %4.lf\\t");
$def[0] .= rrd::gprint(  $NAME[2], "AVERAGE", "average\: %4.2lf\\n"); 
 
$def[0] .= rrd::gradient($NAME[1], "#ffffff", "#33cccc", $NAME[1]."\\t");
$def[0] .= rrd::line1(   $NAME[1], "#339999" );
$def[0] .= rrd::gprint(  $NAME[1], "LAST",    "last\: %4.lf\\t");
$def[0] .= rrd::gprint(  $NAME[1], "MAX",     "max\: %4.lf\\t");
$def[0] .= rrd::gprint(  $NAME[1], "AVERAGE", "average\: %4.2lf\\n"); 
 
?>
