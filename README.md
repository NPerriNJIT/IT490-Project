# IT490 Project - Alcohol Social

# About
This project was made by Nicholas Perri, Joseph Heaney, Zachary Garcia, and Joshua Okossi. Alcohol Social is a software as a service project designed primarily on PHP and MySQL to serve the user with a cocktail recipe application using Apache and RabbitMQ. The starter code was provided by Professor Kehoe (https://github.com/engineeroflies). The architecture of the project involves three machines: one serving the webpage, one running RabbitMQ, and one running the isolated database. The most basic functionality of the project is that the machine serving the webpage will sanitize inputs and send a query through the AMQP server to reach a listener on the server running the database, the SQL server will run a query based on the type of request made, and send a response back through the AMQP server if everything worked. For example, a user will log in by entering their credentials, the input will be sanitized, then sent through the AMQP server with a tag marking the request as 'login'. Then the listener on the machine running MySQL will receive this request, run the login function using the credentials, log the session ID if the login was successful, then return the response to the Apache server and proceed from there. The API used for the cocktail data is https://www.thecocktaildb.com/.
## Functionality
Common Deliverables:
* Authentication System 
* Functioning Web Site
* Secured Database
* Messaging through a Queueing system
* Procedural Data collection from your 3rd party data source (API)
* SystemD for all scripts that need to run on startup
* DB replication 
* Firewalls on production
* HTTPS (self signed certificate)
* Password hashing
* Responsive website

First Personal Deliverables
* Ability to search through drink recipes and add to profile 
* Profile page to see cocktail book and recipes
* Ability to rate and review recipes
* A recommendation system based on likes and preferences
* Ability to make personal recipes public and private, should show in recipe search if public
* Blog post from bartender
* Top rated drinks page
* Follow other users User activity feed
# Server Documentation:

## MQ/DB Server

Necessary packages: rabbitmq-server, curl (for zerotier, documentation in screenshots of changelog), mysql-server, php, php-amqp, php-mysql, default ubuntu server distribution packages

1.  `apt install` all of the above packages, use curl to install zero-tier
2.  once zero-tier is installed, join the network and use the zero-tier dashboard to enable the machine and view its ip address, probably give it a label too
3.  Use git (or scp depending on what cluster the machine is on) to install the project folder, everything in here is able to reference the necessary files as long as they aren't moved around within the project folder
4.  Create a new mysql user and give it permissions on the db you will be using
5.  Create a .env file in the IT490-Project/scripts/lib folder with a link to the DB in the format specified in the DB.php file using that user's information
6.  Set up DB replication on a separate machine by creating a replica user, ensure that log positions match on main and replica machines (from this guide [https://www.digitalocean.com/community/tutorials/how-to-set-up-replication-in-mysql](https://www.digitalocean.com/community/tutorials/how-to-set-up-replication-in-mysql "https://www.digitalocean.com/community/tutorials/how-to-set-up-replication-in-mysql"))
7.  Enable the RabbitMQ management plugin, and create a new User, a VHost under that user, an Exchange on that VHost, and a Queue binded to that exchange, then ensure that the information in that matches the rabbitmq ini file in the scripts folder
8.  Use systemD following the format in the discord to run the listener file on startup, it should now be able to process requests
9.  Match firewall to example in discord

## Front-end server:

Necessary packages: rabbitmq-server, curl (for zerotier, documentation in screenshots of changelog), php, php-amqp, apache2, default ubuntu server distribution packages

1.  `apt install` all of the above packages, use curl to install zero-tier
2.  once zero-tier is installed, join the network and use the zero-tier dashboard to enable the machine and view its ip address, probably give it a label too
3.  Use git (or scp depending on what cluster the machine is on) to install the project folder into the /var/www folder
4.  Follow this guide to setup the apache2 server, the installed files are already in right place within the /var/www folder [https://www.digitalocean.com/community/tutorials/how-to-set-up-apache-virtual-hosts-on-ubuntu-20-04](https://www.digitalocean.com/community/tutorials/how-to-set-up-apache-virtual-hosts-on-ubuntu-20-04 "https://www.digitalocean.com/community/tutorials/how-to-set-up-apache-virtual-hosts-on-ubuntu-20-04")
5.  Ensure that the rabbitmq ini file matches the server details of the mq/db server user, ip, etc
6.  Enable HTTPS/port 443 (from [https://www.rosehosting.com/blog/how-to-enable-https-protocol-with-apache-2-on-ubuntu-20-04/#Step-4-Enable-HTTPS-and-Install-an-SSL-Certificate](https://www.rosehosting.com/blog/how-to-enable-https-protocol-with-apache-2-on-ubuntu-20-04/#Step-4-Enable-HTTPS-and-Install-an-SSL-Certificate "https://www.rosehosting.com/blog/how-to-enable-https-protocol-with-apache-2-on-ubuntu-20-04/#Step-4-Enable-HTTPS-and-Install-an-SSL-Certificate"))

**HTTPS SETUP** prereq: Apache2 set up

8.  sudo apt update
9.  sudo a2enmod ssl to enable ssl certificates
10.  sudo systemctl restart apache2
11.  sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/ssl/private/apache-selfsigned.key -out /etc/ssl/certs/apache-selfsigned.crt generate ssl certificate for your domain
12.  Go through question prompts, Make sure that common name is same domain as apache
13.  Edit the configuration file at "/etc/apache2/sites-available/{yourdomain}.conf
14.  Change the VIrtualHost from 80 to 443
15.  Add these 3 lines underneath Document Root : SSLEngine on SSLCertificateFile /etc/ssl/certs/apache-selfsigned.crt SSLCertificateKeyFile /etc/ssl/private/apache-selfsigned.key
16.  sudo a2ensite {yourdomain}.conf to enable config file
17.  sudo systemctl reload apache2


```
