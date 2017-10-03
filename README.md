# ANO

1. Import database<br>
<br>
  mysql -u root -p postfix_log < postfix_log.sql<br>
<br>
2. edit the DB/db.xml file with the credentials for db access<br>
<br>
<p><b>3. Perl modules list</b></p>
<p>  * perl-Parallel-ForkManager</p>
<p>  * perl-XML-Simple</p>
<p>  * perl-Data-Uniqid</p>
<p>  * perl-Mail-POP3Client</p>
<br>
<p><b>4. ANO TYPE LOG &ensp; -- &ensp; vi /etc/rsyslog.conf and add this lines </b></p>
<p>  * add this template: &ensp;&ensp; template(name="FileFormat" type="string" option.sql="on" string="datetime=%timestamp:::date-mysql%, hostname=%HOSTNAME%, syslogtag=%syslogtag%%msg:::sp-if-no-1st-sp%%msg:::drop-last-lf%\n") </p>
<p>  * apply the template to the mail log like this : &ensp;&ensp; mail.* -/var/log/mail;FileFormat
<br>
![Screenshot](https://user-images.githubusercontent.com/17200386/31122990-bfe772b8-a846-11e7-8532-e7f81e896fb5.png)
