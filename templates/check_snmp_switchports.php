<?php
 
$colors = array(
        'discard' => '#DD8E45',
        'in' => '#4596DD',
        'out' => '#45DD99',
        'error' => '#ff3300',
        );

$counter=1;

$interfaces;

#########################################
# Sort data into a tidy structure.
#########################################

foreach ($NAME as $fieldname){

    if (preg_match('/_in$/', $fieldname)) {
        $interface_name = chop($fieldname, '_in');
        $field='in';
    }
    elseif (preg_match('/_out$/', $fieldname)) {
        $interface_name = chop($fieldname, '_out');
        $field='out';
    }
    elseif (preg_match('/_discard$/', $fieldname)) {
        $interface_name = chop($fieldname, '_discard');
        $field='discard';
    }
    elseif (preg_match('/_error$/', $fieldname)) {
        $interface_name = chop($fieldname, '_error');
        $field='error';
    }
    $interfaces[$interface_name][$field]= $DS[$counter];
    $counter++;
}



#########################################
# Display overlayed Incoming Traffic
#########################################



$opt[1] = "-l 0 --vertical-label \"Network Usage\"  --title \"Server $hostname Combined Incoming\"  ";
    
$ds_name[1] = "Combined Incoming Interfaces" ;
$def[1]="";

$counter=1;
foreach ($interfaces as $interface_name =>$interface_data){

    $def[1] .= "DEF:varin".$counter."=$rrdfile:".$interface_data['in'].":AVERAGE " ;
    $def[1] .= "LINE1:varin".$counter.$colors['in'].":\"".$interface_name." Incoming\" " ;
    
    
    $counter++;
}

#########################################
# Display overlayed Outgoing Traffic
#########################################



$opt[2] = "-l 0 --vertical-label \"Network Usage\"  --title \"Server $hostname Combined Outgoing\"  ";
    
$ds_name[2] = "Combined Outgoing Interfaces" ;
$def[2]="";

$counter=1;
foreach ($interfaces as $interface_name =>$interface_data){

    $def[2] .= "DEF:varin".$counter."=$rrdfile:".$interface_data['out'].":AVERAGE " ;
    $def[2] .= "LINE1:varin".$counter.$colors['out'].":\"".$interface_name." Outgoing\" " ;
    
    
    $counter++;
}


##############################################
# Display Individual Breakdowns of each port.
##############################################


$counter=3;

foreach ($interfaces as $interface_name =>$interface_data){

    $opt[$counter] = "-l 0 --vertical-label \"Network Usage\"  --title \"Server $hostname interface $interface_name\"  ";
    
    $ds_name[$counter] = "interface $interface_name ".$interface_data['in'] ."| ".$interface_data['out']. "| ".$interface_data['discard']." | ".$interface_data['error'];
    $def[$counter]  = "DEF:varin".$counter."=$rrdfile:".$interface_data['in'].":AVERAGE " ;
    $def[$counter] .= "LINE2:varin".$counter.$colors['in'].":\"Incoming\" " ;
    $def[$counter] .= "GPRINT:varin".$counter.":AVERAGE:\"%10.0lf Average\" ";
    $def[$counter] .= "GPRINT:varin".$counter.":MAX:\"%10.0lf Max\" ";
    $def[$counter] .= "GPRINT:varin".$counter.":LAST:\"%10.0lf Last\\n\" ";
    
    $def[$counter] .= "DEF:varout".$counter."=$rrdfile:".$interface_data['out'].":AVERAGE " ;
    $def[$counter] .= "LINE2:varout".$counter.$colors['out'].":\"outgoing\" " ;
    $def[$counter] .= "GPRINT:varout".$counter.":AVERAGE:\"%10.0lf Average\" ";
    $def[$counter] .= "GPRINT:varout".$counter.":MAX:\"%10.0lf Max\" ";
    $def[$counter] .= "GPRINT:varout".$counter.":LAST:\"%10.0lf Last\\n\" ";
    
    $def[$counter] .= "DEF:vardiscard".$counter."=$rrdfile:".$interface_data['discard'].":AVERAGE " ;
    $def[$counter] .= "LINE2:vardiscard".$counter.$colors['discard'].":\"discard\" " ;
    $def[$counter] .= "GPRINT:vardiscard".$counter.":AVERAGE:\"%11.0lf Average\" ";
    $def[$counter] .= "GPRINT:vardiscard".$counter.":MAX:\"%10.0lf Max\" ";
    $def[$counter] .= "GPRINT:vardiscard".$counter.":LAST:\"%10.0lf Last\\n\" ";
    
    $def[$counter] .= "DEF:varerror".$counter."=$rrdfile:".$interface_data['error'].":AVERAGE " ;
    $def[$counter] .= "LINE2:varerror".$counter.$colors['error'].":\"error\" " ;
    $def[$counter] .= "GPRINT:varerror".$counter.":AVERAGE:\"%13.0lf Average\" ";
    $def[$counter] .= "GPRINT:varerror".$counter.":MAX:\"%10.0lf Max\" ";
    $def[$counter] .= "GPRINT:varerror".$counter.":LAST:\"%10.0lf Last\\n\" ";
    
    $counter++;
}





















#
#$def[1] .= "GPRINT:inbit:AVERAGE:\"%.0lf kbits Average\" ";
#$def[1] .= "GPRINT:inbit:MAX:\"%.0lf kbits Max\" ";
#$def[1] .= "GPRINT:inbit:LAST:\"%.0lf kbits Last\\n\" ";
#
#$def[1] .= "DEF:var2=$rrdfile:$DS[2]:AVERAGE " ;
#$def[1] .= "CDEF:outbit=var2,1024,/  ";
#$def[1] .= "LINE2:outbit".$colors[$NAME[2]].": " ;
#$def[1] .= "AREA:outbit".$colors[$NAME[2]]."55:\"$NAME[2]\t\" " ;
#
#$def[1] .= "GPRINT:outbit:AVERAGE:\"%.0lf kbits Average\" ";
#$def[1] .= "GPRINT:outbit:MAX:\"%.0lf kbits Max\" ";
#$def[1] .= "GPRINT:outbit:LAST:\"%.0lf kbits Last\\n\" ";
 
 
?>
foo
