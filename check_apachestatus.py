#!/usr/bin/env python
####################### check_apachestatus.py #######################
# Licence : GPL - http://www.fsf.org/licenses/gpl.txt
#############################################################
# Inspired by De Bodt Lieven's perl implementation
# 20140428 <morgajel at gmail dot com> v1.6
#          rewriting in python
#
import urllib2
import re
import sys
import time
import pynagios
from pynagios import Plugin, Response, make_option, Range



class MyCheck(Plugin):
    """The Guts of the check"""

    # The basic options are covered by Plugin, but we need these as well.
    ssl  = make_option("--ssl",         action="store_true", help="use https")
    port = make_option("-p", "--port",      type="int", default="80" )



    def set_default_options(self):
        """ Manually setting these to work around bad defaults in pynagios.Plugin"""
        if self.options.timeout == 0:
            self.options.timeout=None
#        if self.options.warning == None:
#            self.options.warning=Range('0:10')
#        if self.options.critical == None:
#            self.options.critical=Range('0:20')
        if self.options.ssl:
            self.options.protocol='https'
        else:
            self.options.protocol='http'



    def check(self):
        """ Acquire data for parsing"""
        try:
            self.set_default_options()
            start = time.time()
            website = urllib2.urlopen('%s://%s:%i/server-status?auto' % 
                        (self.options.protocol ,self.options.hostname, self.options.port), None, self.options.timeout )
            results=self.parse_content( website.read().strip() )
            time.sleep(2)
            results['ResponseTime']="{0:.4f}".format(time.time() - start)
        except urllib2.HTTPError, e:
            return Response(pynagios.UNKNOWN, "Cannot retrieve URL: HTTP Error Code %s" % e.code )
        except urllib2.URLError, e:
            return Response(pynagios.UNKNOWN, "Cannot retrieve URL: " + e.reason[1])

        return self.response_for_value(results)



    def parse_content(self, content):
        """ Take the returned scoreboard text and parse it into a dict. """
        results = dict(re.split(':\s*', line) for line in content.split('\n'))
        results['OpenSlots']= results['Scoreboard'].count('.')
        return results



    def response_for_value(self, results):
        """ Determine the proper response from the results. """
        if self.options.critical is not None and self.options.critical.in_range(results['ResponseTime']):
            response=Response(pynagios.CRITICAL, "Response time %s greater than critical %s" % (results['ResponseTime'], self.options.critical))
        else:
            response=Response(pynagios.OK, "%s seconds response time" % results['ResponseTime'])

        
#        response.set_perf_data('Total Accesses', results['Total Accesses'], uom='c', )
#        response.set_perf_data('Total kBytes', results['Total kBytes'], uom='kb', )
#        response.set_perf_data('CPULoad', float(results['CPULoad'])*100, uom='%', )
#        response.set_perf_data('Uptime', results['Uptime'], uom='c', )
#        response.set_perf_data('ReqPerSec', results['ReqPerSec'], )
#        response.set_perf_data('BytesPerSec', results['BytesPerSec'], uom='b', )
#        response.set_perf_data('BytesPerReq', results['BytesPerReq'], uom='b', )
#        response.set_perf_data('BusyWorkers', results['BusyWorkers'],  )
#        response.set_perf_data('IdleWorkers', results['IdleWorkers'],  )
        response.set_perf_data('ResponseTime', results['ResponseTime'], uom='s',warn=self.options.warning, crit=self.options.critical )
        response.set_perf_data('Open slots', results['OpenSlots'] )

        return response


if __name__ == "__main__":
    # Instantiate the plugin, check it, and then exit
    MyCheck().check().exit()
