#!/usr/bin/perl -w
use strict;
use warnings;
use FindBin qw($Bin);
use lib "$Bin";
use DBI;
use Parallel::ForkManager;
use lib::parsing;
use XML::Simple;
use lib::poller;
use Data::Dumper;

### Paths
my %PATHS = (
		DB_CREDENTIALS => $Bin.'/../DB/db.xml',
		PID_DIR => $Bin.'/../tmp/pids'
	);

sub DBH {
	my ($credentials) = @_;
	my $xml = new XML::Simple;
	my $conf = $xml->XMLin($credentials);
	my $dbh = DBI->connect("DBI:mysql:$conf->{db}:$conf->{host}:$conf->{port}","$conf->{user}","$conf->{password}") or die "Could not connect to database: $DBI::errstr";
	return $dbh;
}

sub ANOMailStats {

	my $dbh = &DBH($PATHS{DB_CREDENTIALS});
	my @processes;

	### Number logs to pars
	### DataBase connection handle 1
	my $q1 = $dbh->prepare("select * from config_log where active = 1");
		$q1->execute;
	my $logs = $q1->fetchall_hashref('id');

	### Number of emailboxes
	my $q2 = $dbh->prepare("select * from config_mbb where active = 1");
		$q2->execute;
	my $bbox = $q2->fetchall_hashref('id');

	### maximum number of procs
	my $max_procs = 0;
	
	if(%$logs) {
		my @count = keys(%$logs);
		$max_procs = $max_procs + ($#count + 1);
	}

	if(%$bbox) {
		my @count = keys(%$bbox);
	 	$max_procs = $max_procs + ($#count + 1);
	}

	$max_procs = $max_procs + 1;

	my $pm = Parallel::ForkManager->new($max_procs);
	$pm->set_waitpid_blocking_sleep(0);

	### parsing class
	my $parsing_log = parsing->new();

	if(%$logs) {
		foreach my $key (keys(%$logs)) {

			$pm->run_on_start( 
	      		sub {
					my ($pid,$ident) = @_;
					my %P = (
						PID => $pid,
						IDENT => $ident,
						ID => $logs->{$key}->{id},
						TYPE => 'mlog',
						TYPE_NAME => $logs->{$key}->{log}
					);
					push(@processes, \%P);
	      		}
			);

			$pm->start($logs->{$key}->{log_description}) and next;
				while(1) {
					$parsing_log->mlog( &DBH($PATHS{DB_CREDENTIALS}), $logs->{$key} );
				}
			$pm->finish;
		}
	}

	if(%$bbox) {
		foreach my $key (keys(%$bbox)) {
			
			$pm->run_on_start( 
	      		sub {
					my ($pid,$ident) = @_;
					my %P = (
						PID => $pid,
						IDENT => $ident,
						ID => $bbox->{$key}->{id},
						TYPE => 'mbox',
						TYPE_NAME => $bbox->{$key}->{account}
					);
					push(@processes, \%P);
	      		}
			);

			$pm->start($bbox->{$key}->{account}) and next;
				while(1) {
					$parsing_log->bounce( &DBH($PATHS{DB_CREDENTIALS}), $bbox->{$key} );
					sleep(5);
				}
			$pm->finish;
		}
	}

	my $poller = new poller();
	for(my $i = 0; $i < 1; $i++) {

		$pm->run_on_start( 
	    	sub {
				my ($pid,$ident) = @_;
				my %P = (
					PID => $pid,
					IDENT => $ident,
					ID => 0,
					TYPE => 'poller',
					TYPE_NAME => 'poller_and_StatusChecker'
				);
				push(@processes, \%P);
	    	}
		);

		$pm->start('poller') and next;
			while(1) {
				my $dbh = &DBH($PATHS{DB_CREDENTIALS});
				$poller->PieChart($dbh);
				$poller->SentChart($dbh);
				&Status(&DBH($PATHS{DB_CREDENTIALS}),$PATHS{PID_DIR});
				sleep(60);
			}
	  	$pm->finish;
	}
	return \@processes;
}

sub Start {
	my ($path) = @_;
	my $pids = &ANOMailStats;
	foreach my $pid (@{$pids}) {
		if(defined($pid->{PID})) {
			open(my $PIDFILE, '>', $path.'/'.$pid->{IDENT}.'.pid');
				print $PIDFILE $pid->{TYPE}.':'.$pid->{ID}.':'.$pid->{TYPE_NAME}.':'.$pid->{PID};
			close($PIDFILE);
		}
	}
}

sub Stop {
	my ($path) = @_;
	opendir(my $PIDDIR, $path);
		while(my $file = readdir($PIDDIR)) {
			if($file =~ m/(.*)\.(pid)/) {
				open(my $PID, '<', $path.'/'.$file);
					my @P = split(':',<$PID>);
					kill(15,$P[3]);
				close($PID);
				unlink $path.'/'.$file;
			}
		}
	closedir($PIDDIR);
}

sub Status {
	my ($dbh, $path, $print) = @_;
	my @PIDS;
	opendir(my $PIDDIR, $path);
		while(my $file = readdir($PIDDIR)) {
			if($file =~ m/(.*)\.(pid)/) {
				open(my $PID, '<', $path.'/'.$file);
				my @P1 = split(':',<$PID>);
				my %P = (
					PID => $P1[3],
					IDENT => $file,
					ID => $P1[1],
					TYPE => $P1[0],
					TYPE_NAME => $P1[2]
				);
				push(@PIDS, \%P);
				close($PID);
			}
		}
	closedir($PIDDIR);

	if(@PIDS) {
		
		my $mlogStatus = $dbh->prepare("update config_log set `status` = ? where `id` = ?;");
		my $mboxStatus = $dbh->prepare("update config_mbb set `status` = ? where `id` = ?;");

		foreach my $proc (@PIDS) {
			my $ps = `ps -p $proc->{PID} --no-headers`;
			my @psrez = split(' ',$ps);
			if((@psrez) && ($proc->{PID} eq $psrez[0])) {
				if($proc->{TYPE} eq 'mlog') {
					$mlogStatus->execute('on',$proc->{ID});
				}
				elsif($proc->{TYPE} eq 'mbox') {
					$mboxStatus->execute('on',$proc->{ID});
				}
			}
			else {
				if($proc->{TYPE} eq 'mlog') {
					$mlogStatus->execute('off',$proc->{ID});
				}
				elsif($proc->{TYPE} eq 'mbox') {
					$mboxStatus->execute('off',$proc->{ID});
				}
			}
		}
	}
	else {
		my $mlogStatus = $dbh->prepare("update config_log set `status` = 'off';");
		$mlogStatus->execute();

		my $mboxStatus = $dbh->prepare("update config_mbb set `status` = 'off';");
		$mboxStatus->execute();
	}

	if((@PIDS) && ($print)) {
		print "------------------------------------------------------------\n";
		print "--- ANO Mail Stats Proccess ---\n\n";
		foreach my $proc (@PIDS) {
			
			my $ps = `ps -p $proc->{PID} --no-headers`;
			my @psrez = split(' ',$ps);

			if((@psrez) && ($proc->{PID} eq $psrez[0])) {
				print 'Proccess Name \''.$proc->{TYPE_NAME}.'\' with PID number : \''.$psrez[0],"\'\n";
			}
			else {
				print "WARNING: THE PROCCESS NAME \'$proc->{TYPE_NAME}\' AND PID NUMBER \'$proc->{PID}\' IS OFFLINE","\n";
			}
		}
		print "------------------------------------------------------------\n";
	}
	elsif($print) {
		print "ANO Mail Stat is OFFLINE\n";
	}
}

if($ARGV[0] eq 'start') {
	&Start($PATHS{PID_DIR});
	&Status(&DBH($PATHS{DB_CREDENTIALS}),$PATHS{PID_DIR},1);
}
elsif($ARGV[0] eq 'stop') {
	&Stop($PATHS{PID_DIR});
	&Status(&DBH($PATHS{DB_CREDENTIALS}),$PATHS{PID_DIR},1);
}
elsif($ARGV[0] eq 'status') {
	&Status(&DBH($PATHS{DB_CREDENTIALS}),$PATHS{PID_DIR},1);
}