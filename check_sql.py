#!/usr/bin/env python
####################### check_apachestatus.py #######################
# Licence : GPL - http://www.fsf.org/licenses/gpl.txt
#############################################################
# Inspired by check_oracle_generic, Copyright 2006 David Ligeret (david.ligeret at gmail.com)

from pynag.Plugins import PluginHelper,ok,warning,critical,unknown
import re
import time
import os
import sys
from sqlalchemy import create_engine
from sqlalchemy import databases
from sqlalchemy.sql import text


my_plugin = PluginHelper()

my_plugin.parser.add_option('-w', '--warning',  default="0:5", help='return warning if sql is outside RANGE')
my_plugin.parser.add_option('-c', '--critical', default="0:10", help='return critical if sql is outside RANGE')
my_plugin.parser.add_option('-H', '--hostname',   default="localhost"   )
my_plugin.parser.add_option('-P', '--password',   default='')
my_plugin.parser.add_option('-U', '--user',       default='')
my_plugin.parser.add_option('-D', '--driver',     default='mysql')
my_plugin.parser.add_option('-s', '--sid',        default='', help='sid/DB name for oracle driver')
my_plugin.parser.add_option(      '--dsn',        default='', help='DSN (required for ODBC driver)')
my_plugin.parser.add_option(      '--dbname',     default='', help='DB name to query')
my_plugin.parser.add_option('-f', '--file',       default='', help='file containing SQL (should return a number)')
my_plugin.parser.add_option('-u', '--units',      default='', help='Units of Returned Value')
my_plugin.parser.add_option('-p', '--port',       default='3306')
my_plugin.parser.add_option('-q', '--query',      default='select 1')

my_plugin.parse_arguments()

if my_plugin.options.driver == 'oracle' and my_plugin.options.sid == '':
    my_plugin.exit(summary="Oracle driver requires a --sid" , exit_code=unknown)
elif my_plugin.options.driver == 'odbc' and my_plugin.options.dsn == '':
    my_plugin.exit(summary="ODBC requires a --dsn" , exit_code=unknown)
try:
    if my_plugin.options.driver == 'mssql':
        engine = create_engine("mssql+pymssql://%s:%s@%s/%s" % (my_plugin.options.user, my_plugin.options.password, my_plugin.options.hostname, my_plugin.options.dbname) ) 
        results=engine.execute( text(my_plugin.options.query) ).fetchall()[0][0]
    elif my_plugin.options.driver == 'mysql':
        engine = create_engine("mysql+pymysql://%s:%s@%s/%s" % (my_plugin.options.user, my_plugin.options.password, my_plugin.options.hostname, my_plugin.options.dbname) ) 
        results=engine.execute( text(my_plugin.options.query) ).fetchall()[0][0]
    else:
        my_plugin.exit(summary="This driver is not currently supported, sorry." , exit_code=unknown)
except ImportError, e:
    my_plugin.exit(summary="Something is missing: %s" % str(e)   , long_output="You may be missing critical python modules.", exit_code=unknown, perfdata='')
except Exception, e:
    my_plugin.exit(summary="Something horrible happened: %s " % e.__class__ , long_output=str(e), exit_code=unknown, perfdata='')

# Lets Parse the data:
my_plugin.add_summary( "%s result" % results)

# and add metrics:
my_plugin.add_metric( label='result', value=results, warn=my_plugin.options.warning, crit=my_plugin.options.critical )

# By default assume everything is ok. Any thresholds specified with --threshold can overwrite this status:
my_plugin.status(ok)

# Here all metrics will be checked against thresholds that are either
# built-in or added via --threshold from the command-line
my_plugin.check_all_metrics()

# Print out plugin information and exit nagios-style
my_plugin.exit()



