#!/usr/bin/env python
# check_sql - a monitoring plugin for databases
# Copyright (C) 2014 Jesse Morgan
# BASED ON: check_oracle_generic, Copyright 2006 David Ligeret (david.ligeret at gmail.com)
#
# check_sql
# This file is part of morgnagplug
#
# check_sql is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# check_sql is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program. If not, see <http://www.gnu.org/licenses/>.

prog = "check_sql"
prog_version = "3.0"

import os
import sys
import getopt
import nagiosplugin
import argparse
import logging
from sqlalchemy import create_engine
from sqlalchemy import databases
from sqlalchemy.sql import text

_log = logging.getLogger('nagiosplugin')


class SQL(nagiosplugin.Resource):

    def __init__(self,args):
        self.args=args
        

    def probe(self):
        _log.info('attempting to connect to the DB')
        # faking a result of 10 returned rows
        args=self.args
        if args.driver == 'mysql':
            engine = create_engine("mysql+pymysql://test:test@localhost/test") 
            results=engine.execute( text(args.query) ).fetchall()[0][0]
            yield nagiosplugin.Metric('rows', results, min=0, context='sql')
        else:
            raise nagiosplugin.CheckError("This driver is not currently supported, sorry.") 


class SQLContext(nagiosplugin.ScalarContext):

    def __init__(self, name, args):
        nagiosplugin.ScalarContext.__init__(self, name, args.warning, args.critical)
        self.args=args
        if args.driver == 'oracle' and args.sid == '':
            raise nagiosplugin.CheckError("Oracle driver requires a --sid") 
        elif args.driver == 'odbc' and args.dsn == '':
            raise nagiosplugin.CheckError("ODBC requires a --dsn") 
    def evaluate(self, metric, resource):
        result= nagiosplugin.ScalarContext.evaluate(self, metric, resource)
        
        return result


class SQLSummary(nagiosplugin.Summary):
    """Resulting Query Information."""

    def ok(self, results):
        return 'SQL results for %s' % str(results['rows'])

@nagiosplugin.guarded
def main():
    argp = argparse.ArgumentParser(description=__doc__)
    argp.add_argument('-w', '--warning', metavar='RANGE', default="1:100",
                      help='return warning if sql is outside RANGE')
    argp.add_argument('-c', '--critical', metavar='RANGE', default="1:100",
                      help='return critical if sql is outside RANGE')
    argp.add_argument('-H', '--host',       default='localhost')
    argp.add_argument('-P', '--password',   default='')
    argp.add_argument('-U', '--user',       default='')
    argp.add_argument('-D', '--driver',     default='mysql')
    argp.add_argument('-s', '--sid',        default='', help='sid/DB name for oracle driver')
    argp.add_argument(      '--dsn',        default='', help='DSN (required for ODBC driver)')
    argp.add_argument(      '--dbname',     default='', help='DB name to query')
    argp.add_argument('-f', '--file',       default='', help='file containing SQL (should return a number)')
    argp.add_argument('-u', '--units',      default='', help='Units of Returned Value')
    argp.add_argument('-p', '--port',       default='3306')
    argp.add_argument('-q', '--query',      default='select 1')
    argp.add_argument('-v', '--verbose', action='count', default=0,
                      help='increase output verbosity (use up to 3 times)')
    argp.add_argument('-t', '--type',       default='', help="""type of threshold (return value is over|under|range|equal WARN or CRIT; default is "under")
        if VALUE is over 3, WARN;
        if VALUE is under 3, WARN;
        if VALUE is equal 3, WARN;
        if VALUE is unequal 3, WARN;
        if VALUE is inside (15,20), WARN;
        if VALUE is outside (4,30), WARN;""")
    args = argp.parse_args()


    check = nagiosplugin.Check(
        SQL(args),
        SQLContext('sql', args),
        SQLSummary() )
    check.main(verbose=args.verbose)

if __name__ == '__main__':
    main()
