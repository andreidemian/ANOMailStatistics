package poller;
use warnings;
use strict;
use POSIX qw(strftime);
use Data::Dumper;

sub new {
	my $class = shift;
	my $self = {};
	bless($self, $class);
	return $self;
}

sub PieChart {
	my ($self, $dbh) = @_;

	sub InsertStats {
		my (%data) = @_;

		if((defined($data{b_count})) && ($data{b_count} > 0)) {
			my $insert_piechart = $data{dbh}->prepare('INSERT INTO BouncePieChart ( `domain_id`,`b_code`,`b_count`,`date` ) VALUES ( ?,?,?,? );');
			$insert_piechart->execute( $data{domain_id}, $data{b_code}, $data{b_count}, $data{date} );
		}
	}

	sub UpdateStats {
		my (%data) = @_;

		if((defined($data{b_code})) && ($data{b_count} > 0)) {
			my $update_piechart = $data{dbh}->prepare('UPDATE BouncePieChart SET `b_count` = ? WHERE `b_code` = ? AND `date` = ? AND `domain_id` = ?;');
			$update_piechart->execute( $data{b_count}, $data{b_code}, $data{date}, $data{domain_id} );
		}
	}

	my $CurDate = (strftime '%Y-%m-%d',localtime);
	my $yesterday = (strftime '%Y-%m-%d',localtime(time() - 24*60*60));
	my $hour = int(strftime '%k', localtime());

	my $DN = $dbh->prepare('select * from domains');
	$DN->execute();
	while(my $domains = $DN->fetchrow_hashref()) {

		my $StausDiffCount = $dbh->prepare("select `status`, count(`status`) AS `count` from bounce_report where DATE(`date`) = ? AND `from_addr` like '%".$domains->{domain}."' AND `status` = ? group by `status`;");

		#### check for dates not in the piechar table
		my $DateD = $dbh->prepare("select DISTINCT(DATE(`date`)) as `date`, `status` from bounce_report where
									(DATE(`date`), `status`) not in (select `date`, `b_code` from BouncePieChart where domain_id = ?)
									AND `from_addr` like '%".$domains->{domain}."'
									order by DATE(`date`) asc");

		$DateD->execute( $domains->{id} );

		while(my $DateDiff = $DateD->fetchrow_hashref()) {

			if(defined($DateDiff->{date})) {

				$StausDiffCount->execute( $DateDiff->{date}, $DateDiff->{status} );
				my $Scount = $StausDiffCount->fetchrow_hashref();

				&InsertStats(
					dbh => $dbh, 
					domain_id => $domains->{id},
					b_code => $Scount->{status},
					b_count => $Scount->{count},
					date => $DateDiff->{date}
				);
			}
		}

		my $StausC = $dbh->prepare("select `status`, count(`status`) AS `count` from bounce_report where DATE(`date`) = ? AND `from_addr` like '%".$domains->{domain}."' group by `status`;");

		if(($hour == 7) == ($hour == 23)) {

			$StausC->execute( $yesterday );
			while(my $Scount = $StausC->fetchrow_hashref()) {

				&UpdateStats(
					dbh => $dbh,
					domain_id => $domains->{id},
					b_code => $Scount->{status},
					b_count => $Scount->{count},
					date => $yesterday
				);
			}	
		}

		if(defined($CurDate)) {
			$StausC->execute( $CurDate );
			while(my $Scount = $StausC->fetchrow_hashref()) {

				&UpdateStats(
					dbh => $dbh,
					domain_id => $domains->{id},
					b_code => $Scount->{status},
					b_count => $Scount->{count},
					date => $CurDate
				);
			}
		}
	}
}


sub SentChart {
	my ($self, $dbh) = @_;

	my $curdate = (strftime '%Y-%m-%d',localtime);
	my $yesterday = (strftime '%Y-%m-%d',localtime(time() - 24*60*60));
	my $hour = int(strftime '%k', localtime());

	sub StartingStats {
		my ($dbh, $date, $domain_id) = @_;

		my $checkSC = $dbh->prepare('select * from SentChart where `date` = ? AND `domain_id` = ?;');
		$checkSC->execute($date, $domain_id);
		my $check_rez = $checkSC->fetchrow_hashref();

		my $inset_sentchart = $dbh->prepare("INSERT INTO SentChart ( `domain_id`,`sent`,`incoming`,`deferred`,`bounced`,`date` ) VALUES ( ?, 0, 0, 0, 0, ? );");
		if(!defined($check_rez->{date})) {
			$inset_sentchart->execute( $domain_id, $date );
			return 1;
		}
		return 0;
	}

	sub SentChartQuery {
		my ($dbh, $date, $domain) = @_;

		my %rez;

		### Sent
		my $chart_query1 = $dbh->prepare("SELECT DATE(m_delivery.`date`) AS `date`, count(distinct(m_delivery.`id`)) AS `sent` FROM m_delivery
											left join m_from on m_from.mess_id=m_delivery.mess_id
    										WHERE m_delivery.`status_b` = 1
    										AND DATE(m_delivery.`date`) = '".$date."' 
    										AND m_from.`from_addr` like '%".$domain."' 
    										AND m_delivery.`relay` not in (select exclude_relay from relays)
    										GROUP BY DATE(m_delivery.`date`)");
		$chart_query1->execute();
		my $sent = $chart_query1->fetchrow_hashref();

		### Incoming
		my $chart_query2 = $dbh->prepare("SELECT DATE(`date`) AS `date`, count(id) AS `incoming` FROM m_delivery
											WHERE `status_b` = 1 
   											AND DATE(`date`) = '".$date."'
   											AND `to_addr` like '%".$domain."' 
   											AND `relay` in (select `include_relay` from relays)
											GROUP BY DATE(`date`)");
		$chart_query2->execute();
		my $incoming = $chart_query2->fetchrow_hashref();

		### Deferred
		my $chart_query3 = $dbh->prepare("SELECT DATE(m_delivery.`date`) AS `date`, count(distinct(m_delivery.`to_addr`)) AS `deferred` FROM m_delivery 
											left join m_from on m_from.mess_id=m_delivery.mess_id
											WHERE m_delivery.`status_b` = 3
    										AND DATE(m_delivery.`date`) = '".$date."' 
    										AND m_from.`from_addr` like '%".$domain."'
											GROUP BY DATE(m_delivery.`date`)");
		$chart_query3->execute();
		my $deferred = $chart_query3->fetchrow_hashref();

		### Deferred
		my $chart_query4 = $dbh->prepare("SELECT DATE(date) AS `date`, count(id) AS `bounced` FROM bounce_report
											WHERE DATE(`date`) = '".$date."' 
    										AND from_addr like '%".$domain."' 
    										GROUP BY DATE(`date`)");
		$chart_query4->execute();
		my $bounced = $chart_query4->fetchrow_hashref();


			if(defined($sent->{sent})) {
				$rez{sent} = $sent->{sent};
			}
			else {
				$rez{sent} = 0;
			}

			if(defined($incoming->{incoming})) {
				$rez{incoming} = $incoming->{incoming};
			}
			else {
				$rez{incoming} = 0;
			}

			if(defined($deferred->{deferred})) {
				$rez{deferred} = $deferred->{deferred};
			}
			else {
				$rez{deferred} = 0;
			}

			if(defined($bounced->{bounced})) {
				$rez{bounced} = $bounced->{bounced};
			}
			else {
				$rez{bounced} = 0;
			}

			$rez{date} = $date;

		##print Dumper(%rez),"\n";
		return %rez;
	}

	my $dm = $dbh->prepare('select * from domains');
	$dm->execute();

	while (my $domain = $dm->fetchrow_hashref()) {

		my $update_sentchart = $dbh->prepare('update SentChart set `sent` = ?, `incoming` = ?, `deferred` = ?, `bounced` = ? where `date` = ? AND `domain_id` = ?;');

		my $DateD = $dbh->prepare('select DISTINCT(DATE(`date`)) as `date` from m_delivery where 
						DATE(`date`) not in (select `date` from SentChart where `domain_id` = ?)');
		$DateD->execute($domain->{id});

		while(my $DateDiff = $DateD->fetchrow_hashref()) {
			if(defined($DateDiff->{date})) {
				### StartTable
				&StartingStats( $dbh, $DateDiff->{date}, $domain->{id} );
				### UpdateTable
				my %rez = &SentChartQuery( $dbh, $DateDiff->{date}, $domain->{domain} );
				##print 'Dete DIFF -- '.Dumper(%rez),"\n";
				$update_sentchart->execute( $rez{sent}, $rez{incoming}, $rez{deferred}, $rez{bounced}, $rez{date}, $domain->{id} );
			}
		}

		if(my %rez = SentChartQuery( $dbh, $curdate, $domain->{domain} )) {
			&StartingStats( $dbh, $curdate, $domain->{id} );
			#print 'CurentDate -- '.Dumper($rez),"\n";
			$update_sentchart->execute( $rez{sent}, $rez{incoming}, $rez{deferred}, $rez{bounced}, $rez{date}, $domain->{id} );
		}

		if(($hour == 8) || ($hour == 23)) {
			&StartingStats( $dbh, $yesterday, $domain->{id} );
			my %rez = SentChartQuery( $dbh, $yesterday, $domain->{domain} );
			#print 'Yesterday -- '.Dumper($rez);
			$update_sentchart->execute( $rez{sent}, $rez{incoming}, $rez{deferred}, $rez{bounced}, $rez{date}, $domain->{id} );
		}
	}
}

1;