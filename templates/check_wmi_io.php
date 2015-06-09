<?php

foreach(array_keys($NAME) as $key){
    $dsnames[$NAME[$key]]=$key;
}

$colors= array(
'#FF9933','#3E9ADE','#FF3300','#FFC2C0','#00CF00',
'#2175D9','#55009D','#942D0C','#D8ACE0','#00B99B',
'#990000','#80B4C1','#FFCC00','#FF0000','#FF7D00',
);

$colorID=0;
$graphID=1;


# Start the Graphs!
 
$opt[1] = "-T 55 -l 0   --vertical-label \"Total Disk IO\" --title \"Server $hostname\" ";
$ds_name[1] = "Total Disk IO";
$def[1] = "DEF:var2=$rrdfile:$DS[2]:AVERAGE " ;
$def[1] .= "LINE1:var2#00000080: " ;
$def[1] .= "AREA:var2".$colors[$colorID++].":\"Busy\t\" " ;
$def[1] .= "GPRINT:var2:AVERAGE:\"%.0lf Average\" ";
$def[1] .= "GPRINT:var2:MAX:\"%.0lf Max\" ";
$def[1] .= "GPRINT:var2:LAST:\"%.0lf Last\\n\" ";

$def[1] .= "DEF:var1=$rrdfile:$DS[1]:AVERAGE " ;
$def[1] .= "STACK:var1".$colors[$colorID++].":\"Idle\t\" " ;
$def[1] .= "GPRINT:var1:AVERAGE:\"%.0lf Average\" ";
$def[1] .= "GPRINT:var1:MAX:\"%.0lf Max\" ";
$def[1] .= "GPRINT:var1:LAST:\"%.0lf Last\\n\" ";

++$graphID;

 
#####################################################
#####################################################
#####################################################
#####################################################
$ds_name[$graphID] = 'Disk Reads and Writes';
$opt[$graphID] = " --title '$hostname - Read and Write Counts' ";
$opt[$graphID].= " --vertical-label 'Counts Per Second ' ";
$opt[$graphID].= " --lower-limit 0";
 
$values=array( '_DiskReadsPersec_Total', '_DiskWritesPersec_Total');
 
$def[$graphID]="";
foreach($values as $key){
    $colorID= ($colorID+1) % count($colors);
    $name=str_replace('COM_','', $key);
    $name=ucwords(strtolower(str_replace('_',' ', $name)));
    $def[$graphID] .= rrd::def($key, $RRDFILE[1], $DS[$dsnames[$key]], 'AVERAGE');
    $def[$graphID] .= rrd::line2($key,$colors[$colorID], rrd::cut($name, 20));
    $def[$graphID] .= rrd::gprint($key, array('LAST','AVERAGE','MAX'), '%5.0lf');
}
++$graphID;
 
#####################################################
#####################################################
#####################################################
#####################################################
$ds_name[$graphID] = 'Disk Reads and Writes';
$opt[$graphID] = " --title '$hostname - Bytes Read and Written ' ";
$opt[$graphID].= " --vertical-label 'Bytes Per Second ' ";
$opt[$graphID].= " --lower-limit 0";
 
$values=array( '_DiskReadBytesPersec_Total', '_DiskWriteBytesPersec_Total');
 
$def[$graphID]="";
foreach($values as $key){
    $colorID= ($colorID+1) % count($colors);
    $name=str_replace('COM_','', $key);
    $name=ucwords(strtolower(str_replace('_',' ', $name)));
    $def[$graphID] .= rrd::def($key, $RRDFILE[1], $DS[$dsnames[$key]], 'AVERAGE');
    $def[$graphID] .= rrd::line2($key,$colors[$colorID], rrd::cut($name, 20));
    $def[$graphID] .= rrd::gprint($key, array('LAST','AVERAGE','MAX'), '%5.0lf');
}
++$graphID;


#'_PercentDiskTime_Total' =>  
#'_PercentDiskReadTime_Total' =>  
#'_PercentDiskWriteTime_Total' =>  
#'_DiskReadBytesPersec_Total' =>  
#'_DiskWriteBytesPersec_Total' =>  
 
#####################################################
#####################################################
#####################################################
#####################################################
$ds_name[$graphID] = 'Disk Queues';
$opt[$graphID] = " --title '$hostname - Disk Queue length' ";
$opt[$graphID].= " --vertical-label 'Items in Queue ' ";
$opt[$graphID].= " --lower-limit 0";
 
$values=array( 'CurrentDiskQueueLength_Total', '_AvgDiskQueueLength_Total', '_AvgDiskReadQueueLength_Total', '_AvgDiskWriteQueueLength_Total');
 
$def[$graphID]="";
foreach($values as $key){
    $colorID= ($colorID+1) % count($colors);
    $name=str_replace('COM_','', $key);
    $name=ucwords(strtolower(str_replace('_',' ', $name)));
    $def[$graphID] .= rrd::def($key, $RRDFILE[1], $DS[$dsnames[$key]], 'AVERAGE');
    $def[$graphID] .= rrd::line2($key,$colors[$colorID], rrd::cut($name, 20));
    $def[$graphID] .= rrd::gprint($key, array('LAST','AVERAGE','MAX'), '%5.0lf');
}
++$graphID;




#'CurrentDiskQueueLength_Total' =>  
#'_AvgDiskQueueLength_Total' =>  
#'_AvgDiskReadQueueLength_Total' =>  
#'_AvgDiskWriteQueueLength_Total' =>  
?>

