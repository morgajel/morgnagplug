#!/usr/bin/env python
####################### check_apachestatus.py #######################
# Licence : GPL - http://www.fsf.org/licenses/gpl.txt
#############################################################
# 20070727 Lieven.DeBodt at gmail.com v1.1
#          Authored: De Bodt Lieven (Lieven.DeBodt at gmail.com)
#
# 20080912 <karsten at behrens dot in> v1.2
#          added output of Requests/sec, kB/sec, kB/request  
#          changed perfdata output so that PNP accepts it
#          http://www.behrens.in/download/check_apachestatus.pl.txt
#
# 20080930 <karsten at behrens dot in> v1.3
#          Fixed bug in perfdata regexp when Apache output was
#          "nnn B/sec" instead of "nnn kB/sec"
#
# 20081231 <geoff.mcqueen at hiivesystems dot com > v1.4
#          Made the scale logic more robust to byte only, kilobyte
#          and provided capacity for MB and GB scale options
#          on bytes per second and bytes per request (untested)
#
# 20130326 <morgajel at gmail dot com> v1.5
#          Adding https support
#
# 20130326 <morgajel at gmail dot com> v1.6
#          rewriting in python
#
import pynagios
import urllib2
from pynagios import Plugin, Response, make_option
import re
class MyCheck(Plugin):
    ssl  = make_option("--ssl",  action="store_true")
    port = make_option("-p", "--port",      type="int", default="80")

    def check(self):

        #because plugin timeout of zero and urllib2's timeout of zero do different things...
        if self.options.timeout == 0:
            self.options.timeout=None

        try:
            if self.options.ssl: 
                website = urllib2.urlopen('https://%s:%i/server-status?auto' % 
                            ( self.options.hostname, self.options.port),None, self.options.timeout )
            else:
                website = urllib2.urlopen('http://%s:%i/server-status?auto' % 
                            ( self.options.hostname, self.options.port), None, self.options.timeout )
            
            content = website.read().strip()
            results = dict(re.split(':\s*', line) for line in content.split('\n'))

        except urllib2.HTTPError, e:
            return Response(pynagios.UNKNOWN, 
                            "Cannot retrieve URL: HTTP Error Code %s" % e.code )
        except urllib2.URLError, e:
            return Response(pynagios.UNKNOWN, 
                            "Cannot retrieve URL: " + e.reason[1])

        return self.response_for_value(results)

    def response_for_value(self, results):
        response=Response(pynagios.OK);
        response.set_perf_data('total accesses', results['Total Accesses'], uom='c', )
        response.set_perf_data('total kBytes', results['Total kBytes'], uom='kb', )
        response.set_perf_data('CPULoad', float(results['CPULoad'])*100, uom='%', )
        response.set_perf_data('Uptime', results['Uptime'], uom='c', )

        return response

#ReqPerSec: .0207126
#BytesPerSec: 313.067
#BytesPerReq: 15114.8
#BusyWorkers: 1
#IdleWorkers: 13
#Scoreboard: ____________W_..................................................................................................................................................................................................................................................................................................................................................................................................
#
#


#Note :
#  The script will return
#    * Without warn and critical options:
#        OK       if we are able to connect to the apache server's status page,
#        CRITICAL if we aren't able to connect to the apache server's status page,,
#    * With warn and critical options:
#        OK       if we are able to connect to the apache server's status page and #available slots > <warn_level>,
#        WARNING  if we are able to connect to the apache server's status page and #available slots <= <warn_level>,
#        CRITICAL if we are able to connect to the apache server's status page and #available slots <= <crit_level>,
#        UNKNOWN  if we aren't able to connect to the apache server's status page
#
#Perfdata legend:
#"_;S;R;W;K;D;C;L;G;I;.;1;2;3"
#_ : Waiting for Connection
#S : Starting up
#R : Reading Request
#W : Sending Reply
#K : Keepalive (read)
#D : DNS Lookup
#C : Closing connection
#L : Logging
#G : Gracefully finishing
#I : Idle cleanup of worker
#. : Open slot with no current process
#1 : Requests per sec
#2 : kB per sec
#3 : kB per Request

if __name__ == "__main__":
    # Instantiate the plugin, check it, and then exit
    MyCheck().check().exit()
