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
import urllib2
import pynagios
from pynagios import Plugin, Response, make_option
import re
import sys
import time
class MyCheck(Plugin):
    ssl  = make_option("--ssl",  action="store_true")
    port = make_option("-p", "--port",      type="int", default="80")

    def check(self):

        #because plugin timeout of zero and urllib2's timeout of zero do different things...
        if self.options.timeout == 0:
            self.options.timeout=None
        if self.options.warning == None:
            self.options.warning='10'
        if self.options.critical == None:
            self.options.critical='20'

        try:
            start = time.time()
            if self.options.ssl: 
                website = urllib2.urlopen('https://%s:%i/server-status?auto' % 
                            ( self.options.hostname, self.options.port),None, self.options.timeout )
            else:
                website = urllib2.urlopen('http://%s:%i/server-status?auto' % 
                            ( self.options.hostname, self.options.port), None, self.options.timeout )
            
            content = website.read().strip()
            end = time.time()
            results = dict(re.split(':\s*', line) for line in content.split('\n'))
            results['ResponseTime']=end-start
            #results['scores']= dict((char,results['Scoreboard'].count(char)) for char in  "_SRWKDCLGI."    )

        except urllib2.HTTPError, e:
            return Response(pynagios.UNKNOWN, 
                            "Cannot retrieve URL: HTTP Error Code %s" % e.code )
        except urllib2.URLError, e:
            return Response(pynagios.UNKNOWN, 
                            "Cannot retrieve URL: " + e.reason[1])

        return self.response_for_value(results)

    def response_for_value(self, results):
        if results['ResponseTime'] >  self.options.critical:
            response=Response(pynagios.CRITICAL, "Response time greater than critical")
        elif results['ResponseTime'] >  self.options.warning:
            response=Response(pynagios.WARNING, "Response time greater than warning")
        else:
            response=Response(pynagios.OK, "Response time is ok")
        

        response.set_perf_data('Total Accesses', results['Total Accesses'], uom='c', )
        response.set_perf_data('Total kBytes', results['Total kBytes'], uom='kb', )
        response.set_perf_data('CPULoad', float(results['CPULoad'])*100, uom='%', )
        response.set_perf_data('Uptime', results['Uptime'], uom='c', )
        response.set_perf_data('ReqPerSec', results['ReqPerSec'], )
        response.set_perf_data('BytesPerSec', results['BytesPerSec'], uom='b', )
        response.set_perf_data('BytesPerReq', results['BytesPerReq'], uom='b', )
        response.set_perf_data('BusyWorkers', results['BusyWorkers'],  )
        response.set_perf_data('IdleWorkers', results['IdleWorkers'],  )
        response.set_perf_data('ResponseTime', results['ResponseTime'], uom='s',warn=self.options.warning, crit=self.options.critical )

        return response

#BusyWorkers: 1
#IdleWorkers: 13
#Scoreboard: ____________W_..................................................................................................................................................................................................................................................................................................................................................................................................
#
#

if __name__ == "__main__":
    # Instantiate the plugin, check it, and then exit
    MyCheck().check().exit()
