package regex;

use strict;
use warnings;
use lib::logistics;
use POSIX qw(strftime);


### Object Constructor
sub new {
	my $class = shift;
	my $self = {
		logtype => shift,
	};
	bless($self, $class);
	return $self;
}

my $datetime_parse = logistics->new();

sub maillog {
	my ($self, $log, $line) = @_;
	my %parsed;
		
			$parsed{line} = int($line);

		if($log =~ m/maillog-id=\K[^ ]+/) {
			$parsed{log_id} = $&;
		}

		#### postfix log date
		if(1 == $self->{logtype}) {

			if($log =~ m/^(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)[ ]{1,2}[0-9]{1,2}[ ]{1,2}[0-9]{1,2}\:[0-9]{1,2}\:[0-9]{1,2}/) {
				$parsed{datetime} = $&;
				$parsed{sqldatetime} = $datetime_parse->SQLDatetime((strftime '%Y', localtime).' '.$parsed{datetime});
			}

			if(defined($parsed{datetime})) {
				$log =~ m/$parsed{datetime}\ \K[^ ]+/;
				$parsed{hostname} = $&;
			}

			#### Postfix sys
			if(defined($parsed{hostname})) {
				$log =~ m/$parsed{hostname}\ \K[^ \[\:]+/;
				$parsed{syslogtag} = $&;
				my @tmp = split('/',$parsed{syslogtag});
				$parsed{inst} = $tmp[0];
				$parsed{proc} = $tmp[1];
			}
		} 
		else {

			if($log =~ m/datetime=\K[^ ,]+/) {
				$parsed{sqldatetime} = $&;
			}

			if($log =~ m/hostname=\K[^ ,]+/) {
				$parsed{hostname} = $&;
			}

			if($log =~ m/syslogtag=\K[^ \[]+/) {
				$parsed{syslogtag} = $&;
				my @tmp = split('/',$parsed{syslogtag});
				$parsed{inst} = $tmp[0];
				$parsed{proc} = $tmp[1];
			}
		}

		if(defined($parsed{syslogtag})) {
			$log =~ m/\Q$parsed{syslogtag}\E[0-9\[\]\:]+\ \K[^ :]+/;
			$parsed{mess_id} = $&;
		}

		#### Client
		if($log =~ m/client=\K[^ ,]+/) {
			$parsed{client} = $&;
		}
		
		if($log =~ m/sasl_method=\K[^ ,]+/) {
			$parsed{sasl_method} = $&;
		}

		if($log =~ m/sasl_username=\K[^ ,]+/) {
			$parsed{sasl_username} = $&;
		}

		if($log =~ m/message-id=\<\K[^ \>]+/) {
			$parsed{message_id} = $&;
		}

		#### FROM
		if($log =~ m/from=\<\K[^ \>]+/) {
			$parsed{from} = $&;
		}

		if($log =~ m/size=\K[^ ,]+/) {
			$parsed{size} = $&;
		}

		
		#### TO and ORIG_TO
		if($log =~ m/to=\<\K[^ \>]+/) {
			$parsed{to} = $&;
		}

		if($log =~ m/orig_to=\<\K[^ \>]+/) {
			$parsed{orig_to} = $&;
		}

		if($log =~ m/relay=\K[^ ,]+/) {
			$parsed{relay} = $&;
		}

		if($log =~ m/delay=\K[^ ,]+/) {
			$parsed{delay} = $&;
		}

		if($log =~ m/delays=\K[^ ,]+/) {
			$parsed{delays} = $&;
		}

		if($log =~ m/dsn=\K[^ ,]+/) {
			$parsed{dsn} = $&;
		}

		if($log =~ m/status=\K[^ ,]+/) {
			$parsed{status} = $&;

			if($parsed{status} eq 'sent') {
				$parsed{status_b} = 1;
			}
			elsif($parsed{status} eq 'bounced') {
				$parsed{status_b} = 2;
			}
			elsif($parsed{status} eq 'deferred'){
				$parsed{status_b} = 3;
			}
		
			if(defined($parsed{status})) {
				$log =~ m/(?<=status\=$parsed{status})\ (.*)/;
				$parsed{details} = substr($&,0,499);
				$parsed{details} =~ tr/[\:\'\(\)\,\<\>]//d;
			}
		}

		if($log =~ m/NOQUEUE/) {
                	
			if($log =~ m/NOQUEUE:\ reject:\ RCPT\ from\ \K[^ :]+/) {
				$parsed{reject_from} = $&;
			}
			elsif($log =~ m/NOQUEUE:\ reject:\ MAIL\ from\ \K[^ :]+/) {
				$parsed{reject_from} = $&;
			}

			if($log =~ m/helo=<\K[^ \>]+/) {
				$parsed{helo} = $&;
			}

			if($log =~ m/NOQUEUE:\ \K[^ ].*/) {
				$parsed{reject_mess} = substr($&,0,499);
			}
		}
	
	return \%parsed;
}


sub BouncedMailbox {
	my ($self, $mailbox) = @_;
	my %parsed;
	my $m = join(' ',@{$mailbox});

	if(($m =~ m/delivery-status/) and ($m =~ m/X-Postfix-Queue-ID/)) {

		for(my $i = 0; $i<=$#{$mailbox}; $i++) {

			if($mailbox->[$i] =~ m/(?<=^Reporting-MTA\:\ dns\;)(.*)/) {
				$parsed{RportingMTA} = $&;
				$parsed{RportingMTA} =~ tr/ //d;
			}

			if($mailbox->[$i] =~ m/(?<=^X-Postfix-Queue-ID\:)(.*)/) {
				$parsed{MessageID} = $&;
				$parsed{MessageID} =~ tr/ //d;
			}

			if($mailbox->[$i] =~ m/(?<=^X-Postfix-Sender\:\ rfc822\;)(.*)/) {
				$parsed{Sender} = $&;
				$parsed{Sender} =~ tr/ //d;
			}

			if($mailbox->[$i] =~ m/(?<=^Arrival-Date\:)(.*)/) {
				my @D = split(' ',$&);
				$parsed{datetime} = $D[3].' '.$D[2].' '.$D[1].' '.$D[4];
				$parsed{sqldatetime} = $datetime_parse->SQLDatetime($parsed{datetime});
			}

			if($mailbox->[$i] =~ m/(?<=^Final-Recipient\:\ rfc822\;)(.*)/) {
				$parsed{FinalRecipient} = $&;
				$parsed{FinalRecipient} =~ tr/ //d;
			}

			if($mailbox->[$i] =~ m/(?<=^Original-Recipient\:\ rfc822\;)(.*)/) {
				$parsed{OriginalRecipient} = $&;
				$parsed{OriginalRecipient} =~ tr/ //d;
			}

			if($mailbox->[$i] =~ m/(?<=^Action\:)(.*)/) {
				$parsed{Action} = $&;
				$parsed{Action} =~ tr/ //d;
			}

			if($mailbox->[$i] =~ m/(?<=^Status\:)(.*)/) {
				$parsed{Status} = $&;
				$parsed{Status} =~ tr/ //d;
			}

			if($mailbox->[$i] =~ m/(?<=^Remote-MTA\:\ dns\;)(.*)/) {
				$parsed{RemoteMTA} = $&;
				$parsed{RemoteMTA} =~ tr/ //d;
			}

			if($mailbox->[$i] =~ m/(?<=^Diagnostic-Code\:)(.*)/) {
				$parsed{DiagnosticCode} = substr($&.$mailbox->[$i+1],0,499);
				$parsed{DiagnosticCode} =~ tr/\;\:\<\>//d;
			}
		}
	}

	return \%parsed;
}

1;