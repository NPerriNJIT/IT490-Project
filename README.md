# IT490-Project
Fantasy football probably
Group Members: Nicholas Perri, Joseph Heaney, Zachary Garcia, Joshua Okossi

# TODOs
## Authentication

**Things we need to get done for other things to work**

- [x] Put machines on VPN
- [x] Create database
	- [x] Relevant tables:
		- [x] Users
		- [x] Sessions
- [x] Create RabbitMQ hosts, queues, etc
- [x] Get webserver to be accessible by other machines
	- [x] Webserver has to have forms accessible 
	- [ ] Webserver has to automatically sanitize inputs

**Functionality**
 - [x] Registration
	 - [x] Client: Send request type "registration" with username, password to clientToServerQueue, wait for response from serverToClientQueue
	 - [x] Database: Receive request from clientToServerQueue with "registration" type, create user if they don't exist, return result to user on serverToClientQueue
 - [x] Login
	 - [x] Client: Send request type "login" with username, password to clientToServerQueue, wait for response from serverToClientQueue
		 - [x] If login is successful, create a session, store session ID received by server
	- [x] Database: Receive request type "login", check against users table
		- [x] If the login is successful, create a session, store session ID in database, return session ID to user
		- [x] If the login is unsuccessful, deny access, send denied message on queue
 - [x] Sessions
	 - [x] Client: Send request type "validateSession" with session id to clientToServerQueue with requested variables, wait for response from serverToClientQueue
	 - [x] Server: Receive request from clientToServerQueue with type "validateSession", check against sessions table, return requested information if it is a match on serverToClientQueue
