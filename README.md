# ANO

<p><b>1. Import database</b></p>
 <p> * mysql -u root -p postfix_log < postfix_log.sql</p>
<br>
<p><b>2. edit the DB/db.xml file with the credentials for db access</b></p>

```
<connection>
	<port>3306</port>
	<host>localhost</host>
	<user>user</user>
	<password>password</password>
	<db>postfix_log</db>
</connection>
```

<br>
<p><b>3. Perl modules list</b></p>
<p>  * perl-Parallel-ForkManager</p>
<p>  * perl-XML-Simple</p>
<p>  * perl-Data-Uniqid</p>
<p>  * perl-Mail-POP3Client</p>
<br>
<p><b>4.PHP modules list</b></p>
<p>  * php-mysql</p>
<p>  * php-xml</p>
<br>
<p><b>5. ANO TYPE LOG &ensp; -- &ensp; vi /etc/rsyslog.conf and add this lines </b></p>
<p>  * add this template:</p>

   ```
   template(name="FileFormat" type="string" option.sql="on" string="datetime=%timestamp:::date-mysql%, hostname=%HOSTNAME%, syslogtag=%syslogtag%%msg:::sp-if-no-1st-sp%%msg:::drop-last-lf%\n")
   ```
   
<br>

<p>  * apply the template to the mail log like this :</p> 

    mail.* -/var/log/mail;FileFormat
    
<br>
<br>
<p align="center">
  <p><b>Delivery Charts</b></p>
  <img src="https://user-images.githubusercontent.com/17200386/31127113-98df95b4-a856-11e7-937b-966b656566b4.png" width="350"/>
  <br>
  <br>
  <p><b>Bounced Pie Charts</b></p>
  <img src="https://user-images.githubusercontent.com/17200386/31127468-add99fae-a857-11e7-98c6-2fd63efdd4ec.png" width="350"/>
</p>
