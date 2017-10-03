# ANO Mail Statistics

![screenshot](https://user-images.githubusercontent.com/17200386/31127113-98df95b4-a856-11e7-937b-966b656566b4.png)  
  
![screenshot](https://user-images.githubusercontent.com/17200386/31127468-add99fae-a857-11e7-98c6-2fd63efdd4ec.png)  

  ANO Mail Statistics is an web based tool for postfix log analysis, bounce analysis and also generates charts based on the colected data.  

 * Details:  
   * Delivery charts for Sent, Receive, Deferred and Bounced.  
   * Bounced Pie Chart with detailed number of bounces per error code.  
   * Bounced list with all the emails and error messages, search filters and an export to cvs option.  
   * Detailed postfix log with search filters and export to cvs option.  

Installing:
-----------

**1. Import database**  

```
  mysql -u root -p postfix_log < postfix_log.sql
```
  
**2. edit the DB/db.xml file with the credentials for db access**  
  
```
<connection>
	<port>3306</port>
	<host>localhost</host>
	<user>user</user>
	<password>password</password>
	<db>postfix_log</db>
</connection>
```
  
**3.Perl modules list**  
   .. perl-Parallel-ForkManager  
   .. perl-XML-Simple  
   .. perl-Data-Uniqid  
   .. perl-Mail-POP3Client  
  
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

```
    mail.* -/var/log/mail;FileFormat
```

<br>
<br>
