# ANO Mail Statistics

  ANO Mail Statistics is an web based tool for postfix log analysis, bounce analysis and also generates charts based on the colected data.  

 * Details:  
   * Delivery charts for Sent, Receive, Deferred and Bounced.  
   * Bounced Pie Chart with detailed number of bounces per error code.  
   * Bounced list with all the emails and error messages, search filters and an export to cvs option.  
   * Detailed postfix log with search filters and export to cvs option.  
 ----------- 
 
ANO Mail Statistics Wiki
-----------  

* [WIKI](https://github.com/andreidemian/ANOMailStatistics/wiki)  
  
  
  
Installation, Prerequisites and Requirements:
-----------
  
**1. Perl at least version v5.10.1**  
* perl-Parallel-ForkManager  
* perl-XML-Simple  
* perl-Data-Uniqid  
* perl-Mail-POP3Client  
  
**2. php at least version v5.3.3**  
* php-mysql   
* php-xml  

**3. Copy ANOMailStatistics to /opt**  
* Copy and set Rights
```
 mv ANOMailStatistics /opt/
 
 chgrp -R apache /opt/ANOMailStatistics/WEB/uploads
 chmod 775  /opt/ANOMailStatistics/WEB/uploads
 
 chgrp -R apache /opt/ANOMailStatistics/WEB/lib
 chmod 750 /ANOMailoptStatistics/WEB/lib
 chmod 640 /ANOMailoptStatistics/WEB/lib/*
```

**4. Database setup - mysql or mariadb**
* Create database  
```
mysql --user=user_name --password=your_password

create database postfix_log;

exit
```
* Import Database  
```
mysql --user=user_name --password=your_password --database=postfix_log < /opt/ANOMailStatistics/DB/postfix_log.sql
```
* Edit database config xml "vi /opt/ANOMailStatistics/DB/db.xml"
```
<connection>
	<port>3306</port>
	<host>localhost</host>
	<user>user_name</user>
	<password>your_password</password>
	<db>postfix_log</db>
</connection>
```

**5. Apache Config**  
* vi /etc/httpd/conf.d/ano.conf  
```
Alias "/ano" "/opt/ANOMailStatistics/WEB"

<Directory "/opt/ANOMailStatistics/WEB">
   AllowOverride None
   AuthName "ANO Mail Statistics"
   AuthType Basic
   AuthUserFile /etc/httpd/anopass
   Require valid-user
</Directory>

<Directory "/opt/ANOMailStatistics/WEB/lib">
   Require all denied
</Directory>
```
* Set user and password for Apache auth  
```
htpasswd -c /etc/httpd/anopass bob
```
**6. ANO Mail Statistics Settings**  
* [Log Parser Settings](https://github.com/andreidemian/ANOMailStatistics/wiki/Log-Config)  
* [Bounce Parser Settings](https://github.com/andreidemian/ANOMailStatistics/wiki/Bounce-Mail-Box)  
* [Delivery Chart IN/OUT relays](https://github.com/andreidemian/ANOMailStatistics/wiki/SMTP-IN-OUT)  
* [Specify Domains](https://github.com/andreidemian/ANOMailStatistics/wiki/Domains)  
* Start the parser:  
```
perl /opt/ANOMailStatistics/GetStats/ano_get.pl start
```
* Stop the parser:  
```
perl /opt/ANOMailStatistics/GetStats/ano_get.pl stop
```
* Parser Status:  
```
perl /opt/ANOMailStatistics/GetStats/ano_get.pl status
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
