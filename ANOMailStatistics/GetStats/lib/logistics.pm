package logistics;
use strict;
use warnings;
use POSIX qw(strftime);
use Data::Uniqid qw ( suniqid uniqid luniqid );

sub new {
	my $class = shift;
	my $self = {
		dbh => shift
	};
	bless($self, $class);
	return $self;
}

sub LogTagging {
	my ($self, $log) = @_;
	my %id_tag;
	
	if(!-f $log) {
		system("touch $log");
	}

	### Check for id
	open(my $id,'<',"$log");
		while(<$id>) {
			if($_ =~ m/maillog-id=\K[^ ]+/) {
				$id_tag{cur_id} = $&;
				$id_tag{cur_id} =~ s/[\n\r]//g;
				last;
			}
		}
	close($id);

	## add an id if no id is found
	if(not defined($id_tag{cur_id})) {

		$id_tag{new_id} = uniqid;
		
		open(my $read,'<',"$log");
		open(my $write,'>',"$log".'new');
			print $write "maillog-id=".$id_tag{new_id}."\n";
			while(<$read>) {
				print $write $_;
			}
		close($write);
		close($read);
		### delete old log
		unlink($log);
		### rename new log to old name
		rename("$log".'new', $log);
		system("kill -HUP `cat /var/run/syslogd.pid 2> /dev/null` 2> /dev/null || true");
	}
	return \%id_tag;
}

sub RowCount {
	my ($log) = @_;
	
	my $num = 0;
	
	open(my $file,'<',$log);
		while(<$file>) {
			$num = $.;
		}
	close($file);

	return int($num);
}

## log management
sub LogManagement {
	my ($self, $conf) = @_;
	
	my $tag_id;
	
	## Curent date for log taging
	my $cur_date = (strftime '%Y-%m-%d %H:%M:%S',localtime);

	## generate log id and insert the id into the log
	my $log_id = &LogTagging('',$conf->{log});

	## if the log is a new addition add id else retreive id from log
	if(defined($log_id->{cur_id})) {
		$tag_id = $log_id->{cur_id};
	}
	else {
		$tag_id = $log_id->{new_id};
	}
	
	## Concat hour and minut end convert from string to int
	my $time = int($conf->{R_H}.$conf->{R_M});

	my $q2 = $self->{dbh}->prepare("select * from logvar where path_id = ? order by date desc limit 1;");
	$q2->execute( $conf->{id} );
	my $lv = $q2->fetchrow_hashref;
	$q2->finish;

	my $vlp = $self->{dbh}->prepare("INSERT INTO logvar (`path_id`,`log_id`,`log_description`,`line_num`,`date`) VALUES ( ?,?,?,?,? );");
	my $vlid = $self->{dbh}->prepare("select log_id from logvar where log_id = ?");
 

	if(defined($lv->{log_id})) {

		if(defined($log_id->{cur_id})) {
			$vlid->execute("$log_id->{cur_id}");
			my $lid = $vlid->fetchrow_array;
			$vlid->finish;
			if(not defined($lid)) {
				$vlp->execute("$conf->{id}","$log_id->{cur_id}","$conf->{log_description}",0,"$cur_date");
				$tag_id = $log_id->{cur_id};
			}
		}
		elsif(not defined($log_id->{cur_id})) {
			$vlid->execute("$log_id->{new_id}");
			my $lid = $vlid->fetchrow_array;
			$vlid->finish;
			if(not defined($lid)) {
				$vlp->execute("$conf->{id}","$log_id->{new_id}","$conf->{log_description}",0,"$cur_date");
				$tag_id = $log_id->{new_id};
			}
		}
	
		### Retrive datetime from logvar
		my @ld = split('[\-\ \:]+',$lv->{date});
		my $date = int($ld[0].$ld[1].$ld[2]);

		if((defined($conf->{logrotate})) && ($conf->{logrotate} == 1)) {
		 	## DAILY log rotate
			if(($conf->{R_W} eq '0') && (int(strftime '%Y%m%d',localtime) > $date) && (int(strftime '%H%M',localtime) >= $time)) {
				rename($conf->{log}, $conf->{log}.'-'.(strftime '%Y%m%d',localtime));
				my $rotate_new_id = &LogTagging('',$conf->{log});
				$vlp->execute("$conf->{id}","$rotate_new_id->{new_id}","$conf->{log_description}",0,"$cur_date");
				$tag_id = $rotate_new_id->{new_id};
			} ## weekly log rotate
			elsif(($conf->{R_W} gt '0') && (int(strftime '%Y%m%d',localtime) > $date) && (int(strftime '%u',localtime) == $conf->{R_W}) && (int(strftime '%H%M',localtime) >= $time)) {
				rename($conf->{log}, $conf->{log}.'-'.(strftime '%Y%m%d',localtime));
				my $rotate_new_id = &LogTagging('',$conf->{log});
				$vlp->execute("$conf->{id}","$rotate_new_id->{new_id}","$conf->{log_description}",0,"$cur_date");
				$tag_id = $rotate_new_id->{new_id};
			}
		}
	}
	else {
		if(not defined($log_id->{cur_id})) {
			$vlp->execute("$conf->{id}","$log_id->{new_id}","$conf->{log_description}",0,"$cur_date");
			$tag_id = $log_id->{new_id};
		}
		elsif(defined($log_id->{cur_id})) {
			$vlp->execute("$conf->{id}","$log_id->{cur_id}","$conf->{log_description}",0,"$cur_date");
			$tag_id = $log_id->{cur_id};
		}
	}
	$vlp->finish;
	return $tag_id;
}

sub LogPosition {
	my ($self, $log_id, $conf) = @_;

	my $q1 = $self->{dbh}->prepare("select * from logvar where path_id = ? AND log_id = ? limit 1;");
	$q1->execute( $conf->{id}, $log_id );
	my $lp = $q1->fetchrow_hashref;
	$q1->finish;

	if($lp->{line_num} == 0) {
		return 0;
	}
	else {
		return ($lp->{line_num} + 1);
	}
}


sub SQLDatetime {
	my ($self, $datetime) = @_;
	my %m = (
		Jan => '01',
		Feb => '02',
		Mar => '03',
		Apr => '04',
		May => '05',
		Jun => '06',
		Jul => '07',
		Aug => '08',
		Sep => '09',
		Oct => '10',
		Nov => '11',
		Dec => '12'
		);

	my @dt = split('[\-\ \:]+',$datetime);

	if($dt[2] =~ m/^[0-9]$/) {
		$dt[2] = ('0'.$&);
	}

	if($dt[1] =~ m/(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)/) {
		return int($dt[0].$m{$&}.$dt[2].$dt[3].$dt[4].$dt[5]);
	}
}

sub DeleteLogOlderThen {
	my ($self, $conf) = @_;

	my $time = int(strftime('%k',localtime));

	if((11 == $time) || (23 == $time)) {

		my @tables = ( 'm_client', 'm_from', 'm_delivery' );

		my $q1 = $self->{dbh}->prepare("select * from logvar where `path_id` = ? and `date` < DATE_SUB(NOW(), INTERVAL ? DAY)");
		$q1->execute($conf->{id},$conf->{del_older_rows});
		my $id_to_del = $q1->fetchall_hashref('id');

		my $del_logvar = $self->{dbh}->prepare("delete from logvar where `date` < DATE_SUB(NOW(), INTERVAL ? DAY) and log_id = ? and
											(select count(distinct(log_id)) from m_client where log_id = ?) = 0 and
											(select count(distinct(log_id)) from m_from where log_id = ?) = 0 and
											(select count(distinct(log_id)) from m_delivery where log_id = ?) = 0;");

		my $m_reject = $self->{dbh}->prepare("delete from m_reject where `date` < DATE_SUB(NOW(), INTERVAL 7 DAY)");
		$m_reject->execute;

		foreach my $key (keys(%$id_to_del)) {
			if($id_to_del->{$key}->{log_id}) {
				$del_logvar->execute($conf->{del_older_rows}, $id_to_del->{$key}->{log_id}, $id_to_del->{$key}->{log_id}, $id_to_del->{$key}->{log_id}, $id_to_del->{$key}->{log_id});
			}
		}

		foreach my $table (@tables) {
			my $delete = $self->{dbh}->prepare("delete from $table where `date` < DATE_SUB(NOW(), INTERVAL ? DAY) and log_id = ?;");
			foreach my $key (keys(%$id_to_del)) {
				if($id_to_del->{$key}->{log_id}) {
					$delete->execute($conf->{del_older_rows}, $id_to_del->{$key}->{log_id});
				}
			}
		}

		system("find $conf->{log}* -mtime +$conf->{del_older_logs} -delete");
	}
}

sub DeleteBounceOlderThen {
	my ($self, $mbox) = @_;

	my $time = int(strftime('%k',localtime));

	if((13 == $time) || (23 == $time)) {

		my $delete = $self->{dbh}->prepare("delete from bounce_report where `date` < DATE_SUB(NOW(), INTERVAL ? DAY) and mbox_id = ?;");
		$delete->execute($mbox->{del_older_rows}, $mbox->{id});
	}
}

1;