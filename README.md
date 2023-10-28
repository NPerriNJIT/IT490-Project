# IT490-Project
COCKTAIL DB
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

**Other common deliverable stuff**

 - [ ] Distributed Logging
	 - [ ] Logs will get sent to rabbitmq
	 - [ ] Logs will be received by every machine on network

**Personal Deliverables**

 - [ ] Ability to search through drinks and add them to profile
 - [ ] Profile page to see cocktail book & recipes
 - [ ] Ability to rate and review recipes
 - [ ] A recommendation system based on likes and preferences
 - [ ] Ability to make personal recipes public and private, should show in recipe search if public
 - [ ] Blog posts from bartender
