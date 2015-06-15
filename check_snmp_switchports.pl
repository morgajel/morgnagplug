#!/usr/bin/perl -w
############################## check_snmp_switchports ##############
# Version : 0.1
# Date :  Jun 15 2015
# Author  : Jesse Morgan ( morgajel at gmail.com)
# Help : https://github.com/dnsmichi/manubulon-snmp/
# Licence : GPL - http://www.fsf.org/licenses/gpl.txt
# TODO : 
# Contribs : Dimo Velev, Makina Corpus, A. Greiner-B\ufffdr
# Modeled after check_snmp_storage by Patrick Proy
#################################################################
#
# help : ./check_snmp_switchports -h
 
use strict;
use Net::SNMP;
use Getopt::Long;

use lib "/usr/local/icinga/libexec";
use utils qw(%ERRORS $TIMEOUT);
my $TIMEOUT = 15;
#my %ERRORS=('OK'=>0,'WARNING'=>1,'CRITICAL'=>2,'UNKNOWN'=>3,'DEPENDENT'=>4);

# SNMP Datas

my $data= {

    'name'        => {'oid' =>    '1.3.6.1.2.1.2.2.1.2'},
    'op stat'     => {'oid' =>    '1.3.6.1.2.1.2.2.1.8'},
    'in octets'   => {'oid' =>    '1.3.6.1.2.1.2.2.1.10'},
    'in discard'  => {'oid' =>    '1.3.6.1.2.1.2.2.1.13'},
    'in error'    => {'oid' =>    '1.3.6.1.2.1.2.2.1.14'},
    'out octets'  => {'oid' =>    '1.3.6.1.2.1.2.2.1.16'},
    'out discard' => {'oid' =>    '1.3.6.1.2.1.2.2.1.19'},
    'out error'   => {'oid' =>    '1.3.6.1.2.1.2.2.1.20'},
    };


# Globals

my $Name='check_snmp_switchports';
my $Version='0.1';

my $o_host = 	undef; 		# hostname 
my $o_community = undef; 	# community 
my $o_port = 	161; 		# port
my $o_domain=   'udp/ipv4';	# Default to UDP over IPv4
my $o_version2	= undef;	#use snmp v2c
my $o_descr = 	undef; 		# description filter 
my $o_warn = 	undef; 		# warning limit 
my $o_crit=	undef; 		# critical limit
my $o_help=	undef; 		# wan't some help ?
my $o_verb=	undef;		# verbose mode
my $o_version=  undef;          # print version
my $o_timeout=  5;            	# Default 5s Timeout
my $o_perf=	undef;		# Output performance data
my $o_short=	undef;	# Short output parameters
# SNMPv3 specific
my $o_login=	undef;		# Login for snmpv3
my $o_passwd=	undef;		# Pass for snmpv3
my $v3protocols=undef;	# V3 protocol list.
my $o_authproto='md5';		# Auth protocol
my $o_privproto='des';		# Priv protocol
my $o_privpass= undef;		# priv password
#TODO add flags for "input only", "output only", "include errors", and "include discards".
# functions

sub p_version { print "$Name version : $Version\n"; }

sub print_usage {
    print "Usage: $Name [-v] -H <host> -C <snmp_community> [-2] | (-l login -x passwd [-X pass -L <authp>,<privp>]) [-p <port>] [-P <protocol>] -w <warn_level> -c <crit_level> [-t <timeout>] \n";
}

sub round ($$) {
#TODO probably not needed
    sprintf "%.$_[1]f", $_[0];
}


sub is_pattern_valid { # Test for things like "<I\s*[^>" or "+5-i"
# probably not needed
 my $pat = shift;
 if (!defined($pat)) { $pat=" ";} # Just to get rid of compilation time warnings
 return eval { "" =~ /$pat/; 1 } || 0;
}

# Get the alarm signal (just in case snmp timout screws up)
$SIG{'ALRM'} = sub {
#     print ("ERROR: General time-out (Alarm signal)\n");
#     exit $ERRORS{"UNKNOWN"};
     print ("Waiting for info\n");
     exit $ERRORS{"OK"};
};

sub isnnum { # Return true if arg is not a number
# probably not needed
  my $num = shift;
  if ( $num =~ /^-?(\d+\.?\d*)|(^\.\d+)$/ ) { return 0 ;}
  return 1;
}

sub help {
   print "\nSNMP switch port monitor,  version ",$Version,"\n";
   print "(c)2015 Jesse Morgan\n";
   print "  Based on check_snmp_storage by Patrick Proy\n\n";
   print_usage();
   print <<EOT;
By default, plugin will report input and output octets :
warn if %used > warn and critical if %used > crit
-v, --verbose
   print extra debugging information (and lists all storages)
-h, --help
   print this help message
-H, --hostname=HOST
   name or IP address of host to check
-C, --community=COMMUNITY NAME
   community name for the host's SNMP agent (implies SNMP v1)
-2, --v2c
   Use snmp v2c
-l, --login=LOGIN ; -x, --passwd=PASSWD
   Login and auth password for snmpv3 authentication 
   If no priv password exists, implies AuthNoPriv 
-X, --privpass=PASSWD
   Priv password for snmpv3 (AuthPriv protocol)
-L, --protocols=<authproto>,<privproto>
   <authproto> : Authentication protocol (md5|sha : default md5)
   <privproto> : Priv protocole (des|aes : default des) 
-x, --passwd=PASSWD
   Password for snmpv3 authentication
-p, --port=PORT
   SNMP port (Default 161)
-P, --protocol=PROTOCOL
   Network protocol to be used
   ['udp/ipv4'] : UDP over IPv4
    'udp/ipv6'  : UDP over IPv6
    'tcp/ipv4'  : TCP over IPv4
    'tcp/ipv6'  : TCP over IPv6
-w, --warn=INTEGER
   percent / MB of disk used to generate WARNING state
   you can add the % sign 
-c, --critical=INTEGER
   percent / MB of disk used to generate CRITICAL state
   you can add the % sign 
-f, --perfparse, --perfdata
   Performance data output
-t, --timeout=INTEGER
   timeout for SNMP in seconds (Default: 5)
-V, --version
   prints version number
  
EOT
}

sub verb { my $t=shift; print $t,"\n" if defined($o_verb) ; }

sub check_options {
    Getopt::Long::Configure ("bundling");
    GetOptions(
		'v'	    => \$o_verb,	    'verbose'	=> \$o_verb,
        'h'     => \$o_help,    	'help'        	=> \$o_help,
        'H:s'   => \$o_host,		'hostname:s'	=> \$o_host,
        'p:i'   => \$o_port,   		'port:i'	=> \$o_port,
	    'P:s'	=> \$o_domain,		'protocol:s'	=> \$o_domain,
        'C:s'   => \$o_community,	'community:s'	=> \$o_community,
    	'2'     => \$o_version2,    'v2c'           => \$o_version2,
    	'l:s'	=> \$o_login,		'login:s'	=> \$o_login,
    	'x:s'	=> \$o_passwd,		'passwd:s'	=> \$o_passwd,
	    'X:s'	=> \$o_privpass,	'privpass:s'	=> \$o_privpass,
    	'L:s'	=> \$v3protocols,	'protocols:s'	=> \$v3protocols,   	
        'c:s'   => \$o_crit,    	'critical:s'	=> \$o_crit,
        'w:s'   => \$o_warn,    	'warn:s'	=> \$o_warn,
    	't:i'   => \$o_timeout,    	'timeout:i'     => \$o_timeout,
        'V'     => \$o_version,     'version'       => \$o_version,
    	'f'	    => \$o_perf,	    'perfparse'	    => \$o_perf, 'perfdata' => \$o_perf,
    );

    ############################
    # A quick input sanity check
    ############################
    if (defined($o_help) ) { help(); exit $ERRORS{"UNKNOWN"}};
    if (defined($o_version) ) { p_version(); exit $ERRORS{"UNKNOWN"}};
    # check snmp information
    if ( !defined($o_community) && (!defined($o_login) || !defined($o_passwd)) )
	  { print "incomplete snmp login info!\n"; print_usage(); exit $ERRORS{"UNKNOWN"}}
	if ((defined($o_login) || defined($o_passwd)) && (defined($o_community) || defined($o_version2)) )
	  { print "Can't mix snmp v1,2c,3 protocols!\n"; print_usage(); exit $ERRORS{"UNKNOWN"}}
	if (defined ($v3protocols)) {
	  if (!defined($o_login)) { print "Missing snmp V3 login info with protocols!\n"; print_usage(); exit $ERRORS{"UNKNOWN"}}
	  my @v3proto=split(/,/,$v3protocols);
	  if ((defined ($v3proto[0])) && ($v3proto[0] ne "")) {$o_authproto=$v3proto[0];	}	# Auth protocol
	  if (defined ($v3proto[1])) {$o_privproto=$v3proto[1];	}	# Priv  protocol
	  if ((defined ($v3proto[1])) && (!defined($o_privpass))) {
	    print "Missing snmp V3 priv login info with priv protocols!\n"; print_usage(); exit $ERRORS{"UNKNOWN"}}
	}

}

######################################################## MAIN ##################################################
# First we check our timeouts, then connect to the host
#
#
#
######################################################## MAIN ##################################################

check_options();


#####################
# Check global timeout
#####################

if (defined($TIMEOUT)) {
  verb("Alarm at $TIMEOUT");
  alarm($TIMEOUT);
} else {
  verb("no timeout defined : $o_timeout + 10");
  alarm ($o_timeout+10);
}

###################
# Connect to host
###################
my ($session,$error);
if ( defined($o_login) && defined($o_passwd)) {
  # SNMPv3 login
  verb("SNMPv3 login");
    if (!defined ($o_privpass)) {
  verb("SNMPv3 AuthNoPriv login : $o_login, $o_authproto");
    ($session, $error) = Net::SNMP->session(
      -hostname   	=> $o_host,
      -version		=> '3',
      -username		=> $o_login,
      -authpassword	=> $o_passwd,
      -authprotocol	=> $o_authproto,
      -port      	=> $o_port,
      -retries       => 10,
      -timeout          => $o_timeout,
      -domain           => $o_domain
    );  
  } else {
    verb("SNMPv3 AuthPriv login : $o_login, $o_authproto, $o_privproto");
    ($session, $error) = Net::SNMP->session(
      -hostname   	=> $o_host,
      -version		=> '3',
      -username		=> $o_login,
      -authpassword	=> $o_passwd,
      -authprotocol	=> $o_authproto,
      -privpassword	=> $o_privpass,
	  -privprotocol => $o_privproto,
      -port      	=> $o_port,
      -retries       => 10,
      -timeout          => $o_timeout,
      -domain           => $o_domain
    );
  }
} else {
	if (defined ($o_version2)) {
		# SNMPv2 Login
		verb("SNMP v2c login");
		  ($session, $error) = Net::SNMP->session(
		 -hostname  => $o_host,
		 -version   => 2,
		 -community => $o_community,
		 -port      => $o_port,
  		 -retries       => 10,
		 -timeout   => $o_timeout,
		 -domain    => $o_domain
		);
  	} else {
	  # SNMPV1 login
	  verb("SNMP v1 login");
	  ($session, $error) = Net::SNMP->session(
		-hostname  => $o_host,
		-community => $o_community,
		-port      => $o_port,
                -retries       => 10,
		-timeout   => $o_timeout,
		-domain    => $o_domain
	  );
	}
}

if (!defined($session)) {
   printf("ERROR: %s.\n", $error);
   exit $ERRORS{"UNKNOWN"};
}
use Data::Dumper;


my $returndata={};

foreach my $key (keys %$data){
    my $field_oid=$data->{$key}->{'oid'};
    my $table = $session->get_table(Baseoid => $field_oid);
    
    foreach my $oid (keys %$table){
        my $interface_id=$oid;
        $interface_id=~s/$field_oid\.//;
        if (defined $returndata->{$interface_id}){
            $returndata->{$interface_id}->{$key} = $table->{$oid};
        }else{
            $returndata->{$interface_id}={};
            $returndata->{$interface_id}->{$key} = $table->{$oid};
        }
        

    }
}


my @perfresults;
foreach my $interface_id (keys %$returndata){
    my $interface_data=$returndata->{$interface_id};
    my $iname=$interface_data->{'name'};
    my $in=$interface_data->{'in octets'};
    my $out=$interface_data->{'out octets'};
    my $discard=$interface_data->{'in discard'} + $interface_data->{'out discard'} ;
    my $error=$interface_data->{'in error'} + $interface_data->{'out error'} ;

    my $perfstring=sprintf("'%s'=%dc,%dc,%dc,%dc;;;0;",$iname, $in, $out, $discard, $error  );
    push @perfresults, $perfstring;
}
print "Switch responding OK | ".join(" ",@perfresults)."\n";

exit 0;

#foreach my $key ( keys %$resultat) {
#   verb("OID : $key, Desc : $$resultat{$key}");
#   # test by regexp or exact match / include or exclude
#   if (defined($o_negate)) {
#     $test = defined($o_noreg)
#                ? $$resultat{$key} ne $o_descr
#                : $$resultat{$key} !~ /$o_descr/;
#   } else {
#     $test = defined($o_noreg)
#                ? $$resultat{$key} eq $o_descr
#                : $$resultat{$key} =~ /$o_descr/;
#   }  
#  if ($test) {
#    # get the index number of the interface
#    my @oid_list = split (/\./,$key);
#    $tindex[$num_int] = pop (@oid_list);
#       # Check if storage type is OK
#       if (defined($o_storagetype)) {
#	   my($skey)=$storagetype_table.".".$tindex[$num_int];
# 	   verb("   OID : $skey, Storagetype: $hrStorage{$$stype{$skey}} ?= $o_storagetype");
#           if ( $hrStorage{$$stype{$skey}} !~ $o_storagetype) {
#	     $test=undef;
#	   }
#	}
#	if ($test) {
#       # get the full description
#       $descr[$num_int]=$$resultat{$key};
#       # put the oid in an array
#       $oids[$count_oid++]=$size_table . $tindex[$num_int];
#       $oids[$count_oid++]=$used_table . $tindex[$num_int];
#       $oids[$count_oid++]=$alloc_units . $tindex[$num_int];
#
#       verb("   Name : $descr[$num_int], Index : $tindex[$num_int]");
#       $num_int++;
#    }
#  }
#}
#verb("storages selected : $num_int");
#if ( $num_int == 0 ) { print "Unknown storage : $o_descr : ERROR\n" ; exit $ERRORS{"UNKNOWN"};}
#
#my $result=undef;
#
#if (Net::SNMP->VERSION lt 4)
#   {
#   $result = $session->get_request(@oids);
#   }
#else
#   {
#      $result = $session->get_request(Varbindlist => \@oids);
#      foreach my $key ( keys %$result)
#             {
#             # Fix for filesystems larger 2 TB. More than 2 TB will cause an error because
#             # as defined in the RFC hrStorageSize is a 32 bit integer. So filesystems
#             # larger 2 TB report a negative value because the first bit will be interpreted
#             # as an algebraic sign. (0 = +, all others will be -). You simply have to add
#             # 2 to the power of 32 (4294967296) and it is fixed.
#             # Martin Fuerstenau, Oce Printing Systems, 25th Sept 2012
#             if ($$result{$key} < 0)
#                 {
#                 $$result{$key} = $$result{$key} + 4294967296;
#                 }
#             verb("$key  x $$result{$key}");
#             }
#   }
#
#if (!defined($result)) { printf("ERROR: Size table :%s.\n", $session->error); $session->close;
#   exit $ERRORS{"UNKNOWN"};
#}
#
#$session->close;
#
## Only a few ms left...
#alarm(0);
#
## Sum everything if -s and more than one storage
#if ( defined ($o_sum) && ($num_int > 1) ) {
#  verb("Adding all entries");
#  $$result{$size_table . $tindex[0]} *= $$result{$alloc_units . $tindex[0]};
#  $$result{$used_table . $tindex[0]} *= $$result{$alloc_units . $tindex[0]};
#  $$result{$alloc_units . $tindex[0]} = 1;
#  for (my $i=1;$i<$num_int;$i++) {
#    $$result{$size_table . $tindex[0]} += ($$result{$size_table . $tindex[$i]} 
#					  * $$result{$alloc_units . $tindex[$i]}); 
#    $$result{$used_table . $tindex[0]} += ($$result{$used_table . $tindex[$i]}
#					  * $$result{$alloc_units . $tindex[$i]});
#  }
#  $num_int=1;
#  $descr[0]="Sum of all $o_descr";
#}
#
#my $i=undef;
#my $warn_state=0;
#my $crit_state=0;
#my ($p_warn,$p_crit);
#my $output=undef;
#my $output_metric_val = 1024**2;
#my $output_metric = "M";
## Set the metric 
#if (defined($o_giga)) {
#	$output_metric_val *= 1024;
#	$output_metric='G';
#}
#
#for ($i=0;$i<$num_int;$i++) {
#  verb("Descr : $descr[$i]");
#  verb("Size :  $$result{$size_table . $tindex[$i]}");
#  verb("Used : $$result{$used_table . $tindex[$i]}");
#  verb("Alloc : $$result{$alloc_units . $tindex[$i]}");
#  
#  if (!defined($$result{$size_table . $tindex[$i]}) || 
#	!defined($$result{$used_table . $tindex[$i]}) || 
#	!defined ($$result{$alloc_units . $tindex[$i]})) {
#     print "Data not fully defined for storage ",$descr[$i]," : UNKNOWN\n";
#     exit $ERRORS{"UNKNOWN"};
#  }
#  my $to = $$result{$size_table . $tindex[$i]} * ( ( 100 - $o_reserve ) / 100 ) * $$result{$alloc_units . $tindex[$i]} / $output_metric_val;
#  my $pu=undef;
#  if ( $$result{$used_table . $tindex[$i]} != 0 ) {
#	$pu = $$result{$used_table . $tindex[$i]}* 100 /  ( $$result{$size_table . $tindex[$i]} * ( 100 - $o_reserve ) / 100 );
#  }else {
#    $pu=0;
#  } 
#  my $bu = $$result{$used_table . $tindex[$i]} *  $$result{$alloc_units . $tindex[$i]} / $output_metric_val;
#  my $pl = 100 - $pu;
#  my $bl = ( ( $$result{$size_table . $tindex[$i]} * ( ( 100 - $o_reserve ) / 100 ) - ( $$result{$used_table . $tindex[$i]} ) ) * $$result{$alloc_units . $tindex[$i]} / $output_metric_val );
#  # add a ' ' if some data exists in $perf_out
#  $perf_out .= " " if (defined ($perf_out)) ;
#  ##### Ouputs and checks
#  # Keep complete description fot performance output (in MB)
#  my $Pdescr=$descr[$i];
#  $Pdescr =~ s/[`~!\$%\^&\*'"<>|\?,\(= )]/_/g; 
#  ##### TODO : subs "," with something
# if (defined($o_shortL[2])) {
#   if ($o_shortL[2] < 0) {$descr[$i]=substr($descr[$i],$o_shortL[2]);}
#   else {$descr[$i]=substr($descr[$i],0,$o_shortL[2]);}   
# }
#
#verb ("Perf data : $perf_out");
#
#my $comp_oper=undef;
#my $comp_unit=undef;
#
#if (!defined ($output)) { $output="All selected storages "; }
#
#if ( $crit_state == 1) {
#    $comp_oper = ($comp_oper eq "<") ? ">" : "<";  # Inverse comp operator
#    if (defined($o_shortL[1])) {
#	  print "CRITICAL : (",$comp_oper,$o_crit,$comp_unit,") ",$output;
#	} else {
#	  print $output,"(",$comp_oper,$o_crit,$comp_unit,") : CRITICAL";
#	}
#	(defined($o_perf)) ?  print " | ",$perf_out,"\n" : print "\n";
#     exit $ERRORS{"CRITICAL"};
#    }
#if ( $warn_state == 1) {
#    $comp_oper = ($comp_oper eq "<") ? ">" : "<";  # Inverse comp operator
#    if (defined($o_shortL[1])) {
#       print "WARNING : (",$comp_oper,$o_warn,$comp_unit,") ",$output;
#	} else {
#       print $output,"(",$comp_oper,$o_warn,$comp_unit,") : WARNING";
#	}
#	(defined($o_perf)) ?  print " | ",$perf_out,"\n" : print "\n";
#     exit $ERRORS{"WARNING"};
#   }
#if (defined($o_shortL[1])) {
#  print "OK : (",$comp_oper,$o_warn,$comp_unit,") ",$output;
#} else {
#  print $output,"(",$comp_oper,$o_warn,$comp_unit,") : OK";
#}
#(defined($o_perf)) ? print " | ",$perf_out,"\n" : print "\n";
#
#exit $ERRORS{"OK"};
#
