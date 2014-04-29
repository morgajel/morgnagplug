#!/usr/bin/env python
####################### check_apachestatus.py #######################
# Licence : GPL - http://www.fsf.org/licenses/gpl.txt
#############################################################
# Inspired by De Bodt Lieven's perl implementation
# 20140428 <morgajel at gmail dot com> v1.6
#          rewriting in python

from pynag.Plugins import PluginHelper,ok,warning,critical,unknown
import urllib2
import re
import sys
import time

# Create an instance of PluginHelper()
my_plugin = PluginHelper()

# Add all of our wonderful flags
my_plugin.parser.add_option('-H', '--hostname', type="string",      default="127.0.0.1",    dest="hostname" )
my_plugin.parser.add_option('-p', '--port',     type="int",         default="80",           dest="port" )
my_plugin.parser.add_option('-s', '--ssl',      help="enable ssl",  action="store_true",    dest="ssl" )  
my_plugin.parser.add_option('-w', '--warning',  type="string",      default="5",            dest="warning" )
my_plugin.parser.add_option('-c', '--critical', type="string",      default="10",           dest="critical" )
my_plugin.parse_arguments()


# Set the proper protocol
if my_plugin.options.ssl:
    my_plugin.options.protocol='https'
else:
    my_plugin.options.protocol='http'


# Try to download the serverstatus page and do some basic data munging.
try:
    start = time.time()
    website = urllib2.urlopen('%s://%s:%i/server-status?auto' % 
                  (my_plugin.options.protocol,my_plugin.options.hostname,my_plugin.options.port), None,my_plugin.options.timeout )
    content= website.read().strip()
    # Split each parameter into a dict
    results = dict(re.split(':\s*', line) for line in content.split('\n'))
    results['OpenSlots']= results['Scoreboard'].count('.')
    results['ResponseTime']="{0:.4f}".format(time.time() - start)

# Catch any Errors
except urllib2.HTTPError, e:
    my_plugin.exit(summary="Cannot retrieve URL: HTTP Error Code %s" % e.code, long_output=str(e), exit_code=unknown)
except urllib2.URLError, e:
    my_plugin.exit(summary="Cannot retrieve URL: Perhaps a bad protocol (ssl not supported)?" , long_output=str(e), exit_code=unknown)
except Exception, e:
    my_plugin.exit(summary="Something horrible happened:", long_output=str(e), exit_code=unknown, perfdata='')


# Lets Parse the data:
my_plugin.add_summary( "%s seconds response time" % results['ResponseTime'])

# and add metrics:
my_plugin.add_metric( label='Total Accesses', value=results['Total Accesses'], uom='c', )
my_plugin.add_metric( label='Total kBytes', value=results['Total kBytes'], uom='kb', )
my_plugin.add_metric( label='CPULoad',      value=float(results['CPULoad'])*100, uom='%', )
my_plugin.add_metric( label='Uptime',       value=results['Uptime'], uom='c', )
my_plugin.add_metric( label='ReqPerSec',    value=results['ReqPerSec'], )
my_plugin.add_metric( label='BytesPerSec',  value=results['BytesPerSec'], uom='b', )
my_plugin.add_metric( label='BytesPerReq',  value=results['BytesPerReq'], uom='b', )
my_plugin.add_metric( label='BusyWorkers',  value=results['BusyWorkers'],  )
my_plugin.add_metric( label='IdleWorkers',  value=results['IdleWorkers'],  )
my_plugin.add_metric( label='ResponseTime', value=results['ResponseTime'], uom='s',warn=my_plugin.options.warning, crit=my_plugin.options.critical )
my_plugin.add_metric( label='Open slots',   value=results['OpenSlots'] )

# By default assume everything is ok. Any thresholds specified with --threshold can overwrite this status:
my_plugin.status(ok)

# Here all metrics will be checked against thresholds that are either
# built-in or added via --threshold from the command-line
my_plugin.check_all_metrics()

# Print out plugin information and exit nagios-style
my_plugin.exit()



