# ANO Mail Statistics

  ANO Mail Statistics is an web based tool for postfix log analysis, bounce analysis and also generates charts based on the colected data.  

 * Details:  
   * Delivery charts for Sent, Receive, Deferred and Bounced.  
   * Bounced Pie Chart with detailed number of bounces per error code.  
   * Bounced list with all the emails and error messages, search filters and an export to cvs option.  
   * Detailed postfix log with search filters and export to cvs option.  
 -----------  
 
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
  
**3. Perl at least version v5.10.1**  
	* perl-Parallel-ForkManager  
	* perl-XML-Simple  
	* perl-Data-Uniqid  
	* perl-Mail-POP3Client  
  
**4. php at least version v5.3.3**  
	* php-mysql   
	* php-xml  
	
**5. ANO TYPE LOG &ensp; -- &ensp; vi /etc/rsyslog.conf and add this lines**  
* Add this template:  

```
   template(name="FileFormat" type="string" option.sql="on" string="datetime=%timestamp:::date-mysql%, hostname=%HOSTNAME%, syslogtag=%syslogtag%%msg:::sp-if-no-1st-sp%%msg:::drop-last-lf%\n")
```    

* Apply the template to the mail log like this:  
	
```
    mail.* -/var/log/mail;FileFormat
```  
  
-----------

**Status Page**  
  
![screenshot](https://user-images.githubusercontent.com/17200386/31162925-8ba94556-a8e8-11e7-896d-dfef48812666.png)  
-----------

**Mail log list page**  
  
![screenshot](https://user-images.githubusercontent.com/17200386/31162924-8ba5acac-a8e8-11e7-82c9-69d24106a116.png)  
-----------

**Mail Log Details**  
  
![screenshot](https://user-images.githubusercontent.com/17200386/31162923-8ba5c1b0-a8e8-11e7-848c-374afaf81ee4.png)  
-----------

**Bounce list**
  
![screenshot](https://user-images.githubusercontent.com/17200386/31162921-8ba4168a-a8e8-11e7-888f-fe54e5021363.png)  
-----------

**Bounce list Details**
  
![screenshot](https://user-images.githubusercontent.com/17200386/31162922-8ba43840-a8e8-11e7-9226-c7c771882b51.png)  
-----------

**Delivery Charts for Sent, Receive, Deferred and Bounced**
  
![screenshot](https://user-images.githubusercontent.com/17200386/31162954-bc9e4b52-a8e8-11e7-8f58-c2ce7b80bbee.png)  
-----------

**Bounce Pie Chart with detaild number of bounces per error code**
  
![screenshot](https://user-images.githubusercontent.com/17200386/31162956-bcaf636a-a8e8-11e7-98d6-16aa99633402.png)
-----------  
