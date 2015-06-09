<?php
#
# Copyright (c) 2006-2008 Joerg Linge (http://www.pnp4nagios.org)
# Plugin: check_http_response
# $Id: check_http.php 367 2008-01-23 18:10:31Z pitchfork $
# Modified by Romuald FRONTEAU
#
# Response Time
#
$opt[1] = "--vertical-label \"$UNIT[1]\" --title \"Response Times - $hostname / $servicedesc\" --slope-mode --color=BACK#000000 --color=FONT#F7F7F7 --color=SHADEA#ffffff --color=SHADEB#ffffff --color=CANVAS#000000 --color=GRID#00991A --color=MGRID#00991A --color=ARROW#FF0000 ";
#
#
#
$def[1] =  "DEF:var1=$RRDFILE[1]:$DS[1]:AVERAGE " ;
$def[1] .= "VDEF:slope=var1,LSLSLOPE " ;
$def[1] .= "VDEF:int=var1,LSLINT " ;
$def[1] .= "CDEF:proj=var1,POP,slope,COUNT,*,int,+ " ;
$def[1] .= "LINE2:proj#ff00ff:\"Projection \" " ;
$def[1] .= "GPRINT:var1:LAST:\"%6.2lf$UNIT[1] last\" " ;
$def[1] .= "GPRINT:var1:AVERAGE:\"%6.2lf$UNIT[1] avg\" " ;
$def[1] .= "GPRINT:var1:MAX:\"%6.2lf$UNIT[1] max\\n\" ";
$def[1] .=  "CDEF:sp1=var1,100,/,10,* " ;
$def[1] .=  "CDEF:sp2=var1,100,/,20,* " ;
$def[1] .=  "CDEF:sp3=var1,100,/,30,* " ;
$def[1] .=  "CDEF:sp4=var1,100,/,40,* " ;
$def[1] .=  "CDEF:sp5=var1,100,/,50,* " ;
$def[1] .=  "CDEF:sp6=var1,100,/,60,* " ;
$def[1] .=  "CDEF:sp7=var1,100,/,70,* " ;
$def[1] .=  "CDEF:sp8=var1,100,/,80,* " ;
$def[1] .=  "CDEF:sp9=var1,100,/,90,* " ;
 
 
$def[1] .= "AREA:var1#0000A0:\"$NAME[1] \" " ;
$def[1] .= "AREA:sp9#0000A0: " ;
$def[1] .= "AREA:sp8#0000C0: " ;
$def[1] .= "AREA:sp7#0010F0: " ;
$def[1] .= "AREA:sp6#0040F0: " ;
$def[1] .= "AREA:sp5#0070F0: " ;
$def[1] .= "AREA:sp4#00A0F0: " ;
$def[1] .= "AREA:sp3#00D0F0: " ;
$def[1] .= "AREA:sp2#A0F0F0: " ;
$def[1] .= "AREA:sp1#F0F0F0: " ;
 
#
# Filesize
#
$opt[2] = "--vertical-label \"$UNIT[2]\" --title \"Size $hostname / $servicedesc\" ";
#
#
#
$def[2] =  "DEF:var1=$RRDFILE[2]:$DS[2]:AVERAGE " ;
$def[2] .= "AREA:var1#00FFFF:\"$NAME[2] \" " ;
$def[2] .= "LINE1:var1#000000 " ;
$def[2] .= "GPRINT:var1:LAST:\"%6.2lf$UNIT[2] last\" " ;
$def[2] .= "GPRINT:var1:AVERAGE:\"%6.2lf$UNIT[2] avg\" " ;
$def[2] .= "GPRINT:var1:MAX:\"%6.2lf$UNIT[2] max\\n\" ";
?>
