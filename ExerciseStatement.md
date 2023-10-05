# Exercise Statement: Laravel REST API

## Overview

In this exercise, you will create a comprehensive API with access control configured using tokens.

### Level 1

The dice game is played with two dice. If the sum of the results of the two dice is 7, the game is won, otherwise, it's lost. To play the game, users need to be registered as a unique user in the application with a unique email and a non-repeating nickname. If they prefer, users can choose not to add a name, in which case, they will be named "Anonymous" by default. Multiple anonymous players can exist. Upon user registration, a unique identifier and registration date are assigned.

Each player can view a list of all the rolls they have made, including the value of each die and whether the game was won or lost. Additionally, users can check their success rate for all the rolls they have made.

Individual roll entries cannot be deleted, but it is possible to clear the list of rolls for a player.

The software should allow the application administrator to view all players in the system, their success rates, and the average success rate of all players in the system.

The software should follow best design practices.

### Add Security

Include passport authentication for all access to microservice URLs.
Define a role system and restrict access to different routes based on their privilege level.

### Testing

Create integration unit tests for the application. Apply TDD to the application to test each route.

### Important

Consider the following construction details:

URLs:

- POST /players: Create a player.
- PUT /players/{id}: Modify the player's name.
- POST /players/{id}/games/: A specific player makes a dice roll.
- DELETE /players/{id}/games: Delete a player's rolls.
- GET /players: Return a list of all players in the system with their average success rate.
- GET /players/{id}/games: Return the list of game entries for a player.
- GET /players/ranking: Return the average ranking of all players in the system, i.e., the average success rate.
- GET /players/ranking/loser: Return the player with the lowest success rate.
- GET /players/ranking/winner: Return the player with the highest success rate.

### Level 2

Create a front-end application with AJAX to consume data from different routes. You can choose to build it using a framework such as Angular, Vue, React, etc.
Handle CORS if it appears when establishing communication between the front and back ends.

### Level 3

Deploy your project in a production environment.

---

Please note that I've used the English version of the exercise name, but you can replace it with the appropriate term in Catalan or Spanish, or any other language as needed.
