package LogToDB;
use strict;
use warnings;

sub new {
	my $class = shift;
	my $self = {
		dbh => shift
	};
	bless($self, $class);
	return $self;
}

sub AddToMLOG {
	my ($self, $row, $log_id, $cur_line_num) = @_;

	$self->{dbh}->begin_work;

	my $line_num = $self->{dbh}->prepare("UPDATE logvar SET line_num = ? WHERE log_id = ?");

	my $m_client = $self->{dbh}->prepare("INSERT INTO m_client ( `log_id`,`cur_num`,`srv`,`inst`,`proc`,`mess_id`,`client`,`sasl_method`,`sasl_username`,`date` ) VALUES ( ?,?,?,?,?,?,?,?,?,? );");
	my $m_from = $self->{dbh}->prepare("INSERT INTO m_from ( `log_id`,`cur_num`,`srv`,`inst`,`proc`,`mess_id`,`from_addr`,`size`,`date` ) VALUES ( ?,?,?,?,?,?,?,?,? );");
	my $m_delivery = $self->{dbh}->prepare("INSERT INTO m_delivery ( `log_id`,`cur_num`,`srv`,`inst`,`proc`,`mess_id`,`to_addr`,`orig_to`,`relay`,`delay`,`delays`,`dsn`,`status`,`status_b`,`details`,`date` ) VALUES ( ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,? );");
	my $m_reject = $self->{dbh}->prepare("INSERT INTO m_reject ( `log_id`,`cur_num`,`srv`,`inst`,`proc`,`mess_id`,`from_addr`,`to_addr`,`reject_from`,`helo`,`message`,`date` ) VALUES ( ?,?,?,?,?,?,?,?,?,?,?,? );");
	

	foreach my $i (@{$row}) {

		### Insert into m_client
		if(($i->{mess_id} ne 'NOQUEUE') && (defined($i->{client}))) {
			$m_client->execute(
				$log_id,
				$i->{line},
				$i->{hostname},
				$i->{inst},
				$i->{proc},
				$i->{mess_id},
				$i->{client},
				$i->{sasl_method},
				$i->{sasl_username},
				$i->{sqldatetime}
			);
		}
		elsif(($i->{mess_id} ne 'NOQUEUE') && (defined($i->{from}))) {
			$m_from->execute(
				$log_id,
				$i->{line},
				$i->{hostname},
				$i->{inst},
				$i->{proc},
				$i->{mess_id},
				$i->{from},
				$i->{size},
				$i->{sqldatetime} 
			);
		}
		elsif(($i->{mess_id} ne 'NOQUEUE') && ((defined($i->{to})) || (defined($i->{orig_to})))) {
			$m_delivery->execute(
				$log_id,
				$i->{line},
				$i->{hostname},
				$i->{inst},
				$i->{proc},
				$i->{mess_id},
				$i->{to},
				$i->{orig_to},
				$i->{relay},
				$i->{delay},
				$i->{delays},
				$i->{dsn},
				$i->{status},
				$i->{status_b},
				$i->{details},
				$i->{sqldatetime}
			);
		}
		elsif($i->{mess_id} eq 'NOQUEUE') {
			$m_reject->execute(
				$log_id,
				$i->{line},
				$i->{hostname},
				$i->{inst},
				$i->{proc},
				$i->{mess_id},
				$i->{from},
				$i->{to_addr},
				$i->{reject_from},
				$i->{helo},
				$i->{reject_mess},
				$i->{sqldatetime}
			);
		}
	}

	if(defined($cur_line_num)) {
		$line_num->execute( $cur_line_num, $log_id );
	}

	$self->{dbh}->commit;
}


sub AddToBounced {
	my ($self, $row, $mbox_id) = @_;

	$self->{dbh}->begin_work;

	my $bounced = $self->{dbh}->prepare("INSERT INTO bounce_report ( `mbox_id`,`reporting-mta`,`mess_id`,`from_addr`,`to_addr`,`orig_to`,`action`,`status`,`remote-mta`,`diagnostic-code`,`date` ) 
												VALUES ( ?,?,?,?,?,?,?,?,?,?,? )");

	foreach my $i (@{$row}) {

		if(defined($i->{MessageID})) {
			$bounced->execute(
				$mbox_id,
				$i->{RportingMTA},
				$i->{MessageID},
				$i->{Sender},
				$i->{FinalRecipient},
				$i->{OriginalRecipient},
				$i->{Action},
				$i->{Status},
				$i->{RemoteMTA},
				$i->{DiagnosticCode},
				$i->{sqldatetime}
			);
			$bounced->finish;
		}
	}

	$self->{dbh}->commit;
}

1;