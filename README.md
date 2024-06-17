# Documentation for the API

## All Endpoints are tested and working :)

## API URL

    https://playbuddy-3198da0e5cb7.herokuapp.com/

## Endpoints

| method | prefix | route                  | parameter(url)               | parameter(body)                                                                           | What it does                          |
| ------ | ------ | ---------------------- | ---------------------------- | ----------------------------------------------------------------------------------------- | ------------------------------------- |
| POST   | api    | /login                 | /                            | UsernameOfEmail and password                                                              | login to account                      |
| POST   | api    | /register              | /                            | Username, email, password, bio, location, games , platforms, skill_level, profile_picture | make a new account                    |
| GET    | api    | /users                 | /                            | /                                                                                         | retrieve all users                    |
| GET    | api    | /users                 | /${id}                       | /                                                                                         | retrieve a specific user by id        |
| PATCH  | api    | /users                 | /${id}                       | Username, email, password, bio, location, games , platforms, skill_level, profile_picture | updates a specific user by id         |
| DELETE | api    | /users                 | /${id}                       | /                                                                                         | delete a specific user by id          |
| GET    | api    | /matches               | /                            | /                                                                                         | retrieve all matches                  |
| POST   | api    | /matches               | /                            | userid1, userid2, time                                                                    | creates a new match                   |
| DELETE | api    | /matches               | /${id}                       | /                                                                                         | delete a specific match               |
| GET    | api    | /matches               | /${id}                       | /                                                                                         | retrieve a specific match             |
| GET    | api    | /messages/conversation | /${id}                       | /                                                                                         | retrieve the convos a user has        |
| POST   | api    | /messages              | /                            | sender_id, recipient_id, message, time                                                    | send a new message                    |
| GET    | api    | /messages/conversation | /${senderid}/${recipient_id} | /                                                                                         | retrieve the messages between 2 users |
| PATCH  | api    | /messages              | /${id}                       | sender_id, recipient_id, message, time                                                    | update a specific message by id       |
| DELETE | api    | /messages              | /${id}                       | /                                                                                         | delete a specific message by id       |
| GET    | api    | /swipes                | /                            | /                                                                                         | retrieve all the swipes               |
| POST   | api    | /swipes                | /                            | user_id , swiped_on_id , direction , time                                                 | creates a new swipe                   |
| GET    | api    | /swipes                | /${id}                       | /                                                                                         | retrieve a specific swipe by id       |
| PATCH  | api    | /swipes                | /${id}                       | user_id , swiped_on_id , direction , time                                                 | updates a specific swipe by id        |
| DELETE | api    | /swipes                | ${id}                        | /                                                                                         | delete a specific swipe by id         |
