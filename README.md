# IT490-Project
Fantasy football probably
Group Members: Nicholas Perri, Joseph Heaney, Zachary Garcia, Joshua Okossi

# TODOs
## Authentication

**Things we need to get done for other things to work**

- [x] Put machines on VPN
- [x] Create database
	- [ ] Relevant tables:
		- [x] Users
		- [ ] Sessions
- [x] Create RabbitMQ hosts, queues, etc
- [x] Get webserver to be accessible by other machines
	- [ ] Webserver has to have forms accessible 
	- [ ] Webserver has to automatically sanitize inputs

**Functionality**
 - [ ] Registration
	 - [ ] Client: Send request type "registration" with username, password to clientToServerQueue, wait for response from serverToClientQueue
	 - [ ] Database: Receive request from clientToServerQueue with "registration" type, create user if they don't exist, return result to user on serverToClientQueue
 - [ ] Login
	 - [ ] Client: Send request type "login" with username, password to clientToServerQueue, wait for response from serverToClientQueue
		 - [ ] If login is successful, create a session, store session ID received by server
	- [ ] Database: Receive request type "login", check against users table
		- [ ] If the login is successful, create a session, store session ID in database, return session ID to user
		- [ ] If the login is unsuccessful, deny access, send denied message on queue
 - [ ] Sessions
	 - [ ] Client: Send request type "validateSession" with session id to clientToServerQueue with requested variables, wait for response from serverToClientQueue
	 - [ ] Server: Receive request from clientToServerQueue with type "validateSession", check against sessions table, return requested information if it is a match on serverToClientQueue
