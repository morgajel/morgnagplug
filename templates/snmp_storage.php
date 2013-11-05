<?php
$colors = array(
        '/var/log'  => '#33ff00',
        '/'         => '#45C5DD',
        '/tmp'      => '#DDAF45',
        '/boot'     => '#DD457C',
        '/mnt/storage' => '#D0DD45',
        '' => '#45DD99',
        '' => '#DD8E45',
        '' => '#4596DD',
        '' => '#4557DD',
        '' => '#A445DD',
        '' => '#DD45D8',
        '' => '#FFaa00',
        '' => '#ff3300',
        '' => '#aaff00',
        );


$ds_name ="Disk Capacity for ($hostname)";
$opt[1] = "--vertical-label \"% used\" -l 0 -u 100 --title \"$ds_name\" ";
$def[1]="";

foreach ($DS as $i) {

    if ($NAME[$i] != "_dev_shm"){
    
        $def[1] .= rrd::hrule( "100","#003300");
        $def[1] .= rrd::hrule( "90","#FF0000");
        $def[1] .= rrd::hrule( "85","#FFFF00");
        
        $def[1] .= rrd::def("var$i",   $RRDFILE[1], $DS[$i]); 
        $def[1] .= rrd::cdef("used$i",  "var$i,$MAX[$i],/,100,*");


        $color="#FF00FF";
        if (isset($colors[$LABEL[$i]])){
            $color=$colors[$LABEL[$i]] ;
        }
        $def[1] .= rrd::line2(   "used$i", $color, "$LABEL[$i]\t" );
        $def[1] .= rrd::gprint(  "var$i", "LAST",  "%6.2lf MB of $MAX[$i] MB\\n");
    
    
    }
}
?>

