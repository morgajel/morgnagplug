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
        $interface_name = str_replace("TenGigabitEthernet", "10GEth", $interface_name);
        $interface_name = str_replace("GigabitEthernet", "GigEth", $interface_name);
        $interface_name = str_replace("FastEthernet", "FastEth", $interface_name);
        $interface_name = str_replace("Port-channel", "Port_Ch", $interface_name);
        $field='in';
    }
    elseif (preg_match('/_out$/', $fieldname)) {
        $interface_name = chop($fieldname, '_out');
        $interface_name = str_replace("TenGigabitEthernet", "10GEth", $interface_name);
        $interface_name = str_replace("GigabitEthernet", "GigEth", $interface_name);
        $interface_name = str_replace("FastEthernet", "FastEth", $interface_name);
        $interface_name = str_replace("Port-channel", "Port_Ch", $interface_name);
        $field='out';
    }
    elseif (preg_match('/_discard$/', $fieldname)) {
        $interface_name = chop($fieldname, '_discard');
        $interface_name = str_replace("TenGigabitEthernet", "10GEth", $interface_name);
        $interface_name = str_replace("GigabitEthernet", "GigEth", $interface_name);
        $interface_name = str_replace("FastEthernet", "FastEth", $interface_name);
        $interface_name = str_replace("Port-channel", "Port_Ch", $interface_name);
        $field='discard';
    }
    elseif (preg_match('/_error$/', $fieldname)) {
        $interface_name = chop($fieldname, '_error');
        $interface_name = str_replace("TenGigabitEthernet", "10GEth", $interface_name);
        $interface_name = str_replace("GigabitEthernet", "GigEth", $interface_name);
        $interface_name = str_replace("FastEthernet", "FastEth", $interface_name);
        $interface_name = str_replace("Port-channel", "Port_Ch", $interface_name);
        $field='error';
    }
    $interfaces[$interface_name][$field]= $DS[$counter];
    $counter++;
}



#########################################
# Display overlayed Incoming Traffic
#########################################
srand(1);



$opt[1] = "-l 0 --vertical-label \"Network Usage\"  --title \"Server $hostname Combined Incoming\"  ";
    
$ds_name[1] = "Combined Incoming Interfaces";
$def[1]="";

$counter=1;
$colorcounter=1;
ksort($interfaces);
foreach ($interfaces as $interface_name =>$interface_data){

    $def[1] .= "DEF:varin".$counter."=$rrdfile:".$interface_data['in'].":AVERAGE " ;
    $def[1] .= "AREA:varin".$counter."". sprintf("#%06X",  $colorcounter/count($interfaces)*0xDDDDDD    )  .":".$interface_name.":STACK " ; 
    $def[1] .= "PRINT:varin".$counter.":AVERAGE:\"%11.0lf Average\" ";
    $counter++;
    $colorcounter++;
}

#########################################
# Display overlayed Outgoing Traffic
#########################################



$opt[2] = "-l 0 --vertical-label \"Network Usage\"  --title \"Server $hostname Combined Outgoing\"  ";
    
$ds_name[2] = "Combined Outgoing Interfaces" ;
$def[2]="";

$counter=1;
$colorcounter=1;
foreach ($interfaces as $interface_name =>$interface_data){

    $def[2] .= "DEF:varin".$counter."=$rrdfile:".$interface_data['out'].":AVERAGE " ;
    #$def[2] .= "AREA:varin".$counter."". sprintf("#%06X", rand(0,0xffffff))  ."::STACK " ; 
    $def[2] .= "AREA:varin".$counter."". sprintf("#%06X",  $colorcounter/count($interfaces)*0xDDDDDD    )  .":".$interface_name.":STACK " ; 
    $def[2] .= "PRINT:varin".$counter.":AVERAGE:\"%11.0lf Average\" ";
#    $def[1] .= "LINE1:varin".$counter.sprintf("#%06X", rand(0,0xffffff)).":STACK " ;
    
    $counter++;
    $colorcounter++;
}


##############################################
# Display Individual Breakdowns of each port.
##############################################


$counter=3;
$colorcounter=1;

foreach ($interfaces as $interface_name =>$interface_data){

    $opt[$counter] = "-l 0 --vertical-label \"Network Usage\"  --title \"Server $hostname interface $interface_name\"  ";
    $color=sprintf("#%06X",  $colorcounter/count($interfaces)*0xDDDDDD)    ; 
    $ds_name[$counter] = "interface $interface_name ".$interface_data['in'] ."| ".$interface_data['out']. "| ".$interface_data['discard']." | ".$interface_data['error'];
    $def[$counter]  = "DEF:varin".$counter."=$rrdfile:".$interface_data['in'].":AVERAGE " ;
    $def[$counter] .= "CDEF:nvarin".$counter."=varin".$counter.",1024,/,1024,/ " ;
    $def[$counter] .= "AREA:varin".$counter.$color.":\"Incoming\" " ;
    $def[$counter] .= "GPRINT:nvarin".$counter.":AVERAGE:\"%10.0lfMB Avg\" ";
    $def[$counter] .= "GPRINT:nvarin".$counter.":MAX:\"%10.0lfMB Max\" ";
    $def[$counter] .= "GPRINT:nvarin".$counter.":LAST:\"%10.0lfMB Last\\n\" ";
    
    $def[$counter] .= "DEF:varout".$counter."=$rrdfile:".$interface_data['out'].":AVERAGE " ;
    $def[$counter] .= "CDEF:nvarout".$counter."=varout".$counter.",-1,* " ;
    $def[$counter] .= "CDEF:nvarrevout".$counter."=nvarout".$counter.",1024,/,1024,/ " ;
    $def[$counter] .= "AREA:nvarout".$counter.$color.":\"outgoing\" " ;
    $def[$counter] .= "GPRINT:nvarrevout".$counter.":AVERAGE:\"%10.0lfMB Avg\" ";
    $def[$counter] .= "GPRINT:nvarrevout".$counter.":MAX:\"%10.0lfMB Max\" ";
    $def[$counter] .= "GPRINT:nvarrevout".$counter.":LAST:\"%10.0lfMB Last\\n\" ";
    
    $def[$counter] .= "DEF:vardiscard".$counter."=$rrdfile:".$interface_data['discard'].":AVERAGE " ;
    $def[$counter] .= "LINE2:vardiscard".$counter.$colors['discard'].":\"discard\" " ;
    $def[$counter] .= "GPRINT:vardiscard".$counter.":AVERAGE:\"%11.0lf Octets Avg\" ";
    $def[$counter] .= "GPRINT:vardiscard".$counter.":MAX:\"%10.0lf Octets Max\" ";
    $def[$counter] .= "GPRINT:vardiscard".$counter.":LAST:\"%10.0lf Octets Last\\n\" ";
    
    $def[$counter] .= "DEF:varerror".$counter."=$rrdfile:".$interface_data['error'].":AVERAGE " ;
    $def[$counter] .= "LINE2:varerror".$counter.$colors['error'].":\"error\" " ;
    $def[$counter] .= "GPRINT:varerror".$counter.":AVERAGE:\"%13.0lf Octets avg\" ";
    $def[$counter] .= "GPRINT:varerror".$counter.":MAX:\"%10.0lf Octets Max\" ";
    $def[$counter] .= "GPRINT:varerror".$counter.":LAST:\"%10.0lf Octets Last\\n\" ";
    
    $counter++;
    $colorcounter++;
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
