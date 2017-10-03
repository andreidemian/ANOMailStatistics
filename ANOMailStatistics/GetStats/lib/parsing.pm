package parsing;
use strict;
use warnings;
use Mail::POP3Client;
use lib::regex;
use lib::logistics;
use lib::LogToDB;


sub new {
	my $class = shift;
	my $self = {};
	bless($self, $class);
	return $self;
}

sub mlog {
	my ($self, $dbh, $conf) = @_;

	my $parsing_regex = regex->new( $conf->{logtype} );
	my $logistics = logistics->new( $dbh );
	my $log_to_db = LogToDB->new( $dbh );

	## clean tables
	$logistics->DeleteLogOlderThen( $conf );
	
	## rotate log
	my $log_id = $logistics->LogManagement( $conf );

	## curent log position
	my $position_num = $logistics->LogPosition( $log_id, $conf );

	sleep(2);

	my $line_number = 0;
	my $count = 0;
	my @m_log;

	open(LOG,'<',"$conf->{log}");

	while(<LOG>) {

		$line_number = ($.);

		if($position_num <= $line_number) {
		
			$count++;

			my $mail_log = $parsing_regex->maillog("$_", "$line_number");

			if(defined($mail_log->{mess_id})) {

				if(defined($mail_log->{client})) {
					push @m_log, $mail_log;
				}
				elsif(($mail_log->{mess_id} ne 'NOQUEUE') && (defined($mail_log->{from}))) {
					push @m_log, $mail_log;
				}
				elsif(($mail_log->{mess_id} ne 'NOQUEUE') && ((defined($mail_log->{to})) || (defined($mail_log->{orig_to}))) && (defined($mail_log->{status}))) {
					push @m_log, $mail_log;
				}
				elsif($mail_log->{mess_id} eq 'NOQUEUE') {
					push @m_log, $mail_log;
				}
			}

			last if $count == $conf->{iteration_num};
		}
	}
	close(LOG);

	$log_to_db->AddToMLOG( \@m_log, $log_id, $line_number );

	if($count < 1000) {
		sleep(15);
	}
}


sub bounce {
	my ($self, $dbh, $mbb) = @_;

	my $logistics = logistics->new( $dbh );

	$logistics->DeleteBounceOlderThen( $mbb );

	my $parsing_regex = regex->new();
	my $log_to_db = LogToDB->new( $dbh );
	my @bounced;

	my %popAuth = (
		USER 	 => $mbb->{account},
		PASSWORD => $mbb->{password},
		HOST 	 => $mbb->{host},
		PORT 	 => $mbb->{port}
	);

	if($mbb->{ssl} eq '1') {
		$popAuth{USESSL} = 1;
	}

	my $pop = new Mail::POP3Client( %popAuth ) or die pop->Message();

	my $num = $pop->Count();

	my $count = 0;
	for(my $i = $num; 0 <= $i; $i--) {

		my @mbox = $pop->Retrieve($i);
		my $parsing_box = $parsing_regex->BouncedMailbox(\@mbox);
		
		if(defined($parsing_box->{MessageID})) {
			push @bounced, $parsing_box;
			$count++;
			$pop->Delete($i);
		}

		last if $count == $mbb->{iteration_num};
	}

	$pop->Close();

	$log_to_db->AddToBounced( \@bounced, $mbb->{id} );

	if($count < 200) {
		sleep(15);
	}
}

1;