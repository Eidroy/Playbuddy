# Documentation for the API

## All Endpoints are tested and working :)

## API URL

    https://playbuddy-3198da0e5cb7.herokuapp.com/

## Endpoints

| method | prefix | route                  | parameter(url)               | parameter(body)                                                                           | What it does                                                             |
| ------ | ------ | ---------------------- | ---------------------------- | ----------------------------------------------------------------------------------------- | ------------------------------------------------------------------------ |
| POST   | api    | /login                 | /                            | UsernameOrEmail and password                                                              | login to account                                                         |
| POST   | api    | /register              | /                            | Username, email, password, bio, location, games , platforms, skill_level, profile_picture | make a new account                                                       |
| GET    | api    | /users                 | /                            | /                                                                                         | retrieve all users                                                       |
| GET    | api    | /users                 | /${id}                       | /                                                                                         | retrieve a specific user by id                                           |
| POST   | api    | /users                 | /${id}                       | Username, email, password, bio, location, games , platforms, skill_level, profile_picture | updates a specific user by id                                            |
| DELETE | api    | /users                 | /${id}                       | /                                                                                         | delete a specific user by id                                             |
| GET    | api    | /matches               | /                            | /                                                                                         | retrieve all matches                                                     |
| POST   | api    | /matches               | /                            | userid1, userid2, time                                                                    | creates a new swipe , and checks if there is a match                     |
| DELETE | api    | /matches               | /${id}                       | /                                                                                         | delete a specific match                                                  |
| GET    | api    | /matches               | /${id}                       | /                                                                                         | retrieve a specific match /search on userID                              |
| GET    | api    | /messages/conversation | /${id}                       | /                                                                                         | retrieve the convos a user has / use this for loading a user his matches |
| POST   | api    | /messages              | /                            | sender_id, recipient_id, message, time                                                    | send a new message                                                       |
| GET    | api    | /messages/conversation | /${senderid}/${recipient_id} | /                                                                                         | retrieve the messages between 2 users use this to display a chat         |
| PATCH  | api    | /messages              | /${id}                       | sender_id, recipient_id, message, time                                                    | update a specific message by id                                          |
| DELETE | api    | /messages              | /${id}                       | /                                                                                         | delete a specific message by id                                          |
| GET    | api    | /swipes                | /                            | /                                                                                         | retrieve all the swipes                                                  |
| GET    | api    | /swipes                | /${id}                       | /                                                                                         | retrieve a specific swipe by id / searches on userID                     |
| PATCH  | api    | /swipes                | /${id}                       | user_id , swiped_on_id , direction , time                                                 | updates a specific swipe by id                                           |
| DELETE | api    | /swipes                | ${id}                        | /                                                                                         | delete a specific swipe by id                                            |
