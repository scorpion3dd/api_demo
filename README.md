> # Simple demo REST API project
>
> ### Implementing a small simple REST API for an airline
>
> This project is no longer maintained.
>
> At this time, the repository has been archived, and is read-only.
### (c) Denis Puzik <scorpion3dd@gmail.com>

---

This self-written project is done without using any framework.

RELEASE INFORMATION
===================

- The API responds to the following user command requests:
~~~~
1. book a seat on the flight;
2. cancel reservation;
3. to buy a ticket;
4. return a purchased ticket.
~~~~
Place - the usual number from 1 to 150. 

You can buy a ticket either after booking or without it. 

The functionality of payment and refund is implemented in the form of state changes.

- The API is subscribed to receive events via the HTTP protocol (callback notifications)
  to one of your addresses - /api/v1/callback/events 
~~~~~~
Types of notifications:
- sale of tickets for the flight is completed;
- flight canceled.
~~~~~~
- Sample notification:  
~~~~~~
{
    "data": {
        "flight_id": 1,
        "triggered_at": 1585012345,
        "event": "flight_ticket_sales_completed",
        "secret_key": "a1b2c3d4e5f6a1b2c3d4e5f6"
    }
}
~~~~~~
If a flight is canceled, users who have booked or purchased tickets for that flight,
emails are sent in the background. flight cancellation letters. 

In case of response to the event with an HTTP code other than 200, the event will be re-received
after a certain period of time.

SYSTEM REQUIREMENTS
===================

This self-written project requires PHP 5.6 or later.

INSTALLATION
============

1. Create DB airlines
~~~~~~
CREATE DATABASE airlines
CHARACTER SET utf8
COLLATE utf8_general_ci;
~~~~~~
2. Create tables in the airlines database by executing the SQL script:
~~~~~~
CREATE TABLE airlines.reservation (
    id int(11) NOT NULL AUTO_INCREMENT,
    flight_id int(11) NOT NULL,
    place int(11) NOT NULL,
    user_email varchar(255) DEFAULT NULL,
    date_create datetime DEFAULT NULL,
    PRIMARY KEY (id)
  )
  ENGINE = INNODB,
  AUTO_INCREMENT = 7,
  AVG_ROW_LENGTH = 5461,
  CHARACTER SET utf8,
  COLLATE utf8_general_ci;
~~~~~~
~~~~~~
CREATE TABLE airlines.ticket (
    id int(11) NOT NULL AUTO_INCREMENT,
    flight_id int(11) NOT NULL,
    place int(11) NOT NULL,
    user_email varchar(255) DEFAULT NULL,
    date_create datetime DEFAULT NULL,
    suma int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
  )
  ENGINE = INNODB,
  AUTO_INCREMENT = 12,
  AVG_ROW_LENGTH = 1820,
  CHARACTER SET utf8,
  COLLATE utf8_general_ci;
~~~~~~
3. Clone a project from the repository
~~~~~~
git clone https://github.com/scorpion3dd/Simple_REST_Full_API_airlines.git ./api.simple
~~~~~~
4. In the file /api/SuperRepository.php, if necessary, change the PARAMS parameters

5. Create virtual host in you web server

6. Reload Web Server (example Apache)
~~~~~~
sudo systemctl restart apache2
~~~~~~

DESCRIPTION OF API REQUESTS
============

1. book a seat on the flight
~~~~~~
POST /api/v1/reservation 
IN:
{
    "data": {
        "flight_id": 1,
        "place": 50,
        "user_email": "den5@gmail.com",
        "secret_key": "a1b2c3d4e5f6a1b2c3d4e5f6"
    }
}
OUT OK (200):
{
    "id": 8,
    "message": "Saved"
}
OUT ERROR (500):
{
    "message": "Saving Error"
}
~~~~~~
2. change booking
~~~~~~
PUT /api/v1/reservation 
IN:
{
  "data": {
      "id": 1,
      "flight_id": 1,
      "place": 80,
      "user_email": "den5@gmail.com",
      "secret_key": "a1b2c3d4e5f6a1b2c3d4e5f6"
  }
}
OUT OK (200):
{
  "id": 1,
  "message": "Updated"
}
OUT ERROR (500):
{
  "message": "Update error"
}
~~~~~~
3. cancel reservation
~~~~~~
DELETE /api/v1/reservation
IN:
{
    "data": {
        "id": 1,
        "secret_key": "a1b2c3d4e5f6a1b2c3d4e5f6"
    }
}
OUT OK (200):
{
    "id": 8,
    "message": "Deleted"
}
OUT ERROR (500):
{
    "message": "Delete Error"
}
~~~~~~
4. all reservations
~~~~~~
GET /api/v1/reservation
IN:
{
    "data": {
        "secret_key": "a1b2c3d4e5f6a1b2c3d4e5f6"
    }
}
OUT OK (200):
{
    "items": [
        {"id":"4", "flight_id":"1", "place":"49", 
            "user_email":"den4@gmail.com", "date_create":"2020-05-04 23:49:36"},
        {"id":"5", "flight_id":"1", "place":"55", 
            "user_email":"den5@gmail.com", "date_create":"2020-05-06 12:57:38"},
        {"id":"6", "flight_id":"1", "place":"58", 
            "user_email":"den5@gmail.com", "date_create":"2020-05-06 13:18:16"}
    ],
    "message": "all"
}
OUT ERROR (500):
{
    "message": "Error"
}
~~~~~~
5. specific booking
~~~~~~
GET /api/v1/reservation
IN:
{
    "data": {
        "id": 4,
        "secret_key": "a1b2c3d4e5f6a1b2c3d4e5f6"
    }
}
OUT OK (200):
{
    "item":{
        "id":"4", "flight_id":"1", "place":"49",
        "user_email":"den4@gmail.com",
        "date_create":"2020-05-04 23:49:36"
    },
    "message": "all"
}
OUT ERROR (500):
{
    "message": "Data not found"
}
~~~~~~
6. to buy a ticket
~~~~~~
POST /api/v1/ticket 
IN:
{
    "data": {
        "flight_id": 1,
        "place": 50,
        "user_email": "den5@gmail.com",
        "suma": 145,
        "secret_key": "a1b2c3d4e5f6a1b2c3d4e5f6"
    }
}
OUT OK (200):
{
    "id": 8,
    "message": "Saved"
}
OUT ERROR (500):
{
    "message": "Saving Error"
}
~~~~~~
7. change ticket
~~~~~~
PUT /api/v1/ticket 
IN:
{
  "data": {
      "id": 1,
      "flight_id": 1,
      "place": 80,
      "user_email": "den5@gmail.com",
      "suma": 175,
      "secret_key": "a1b2c3d4e5f6a1b2c3d4e5f6"
  }
}
OUT OK (200):
{
  "id": 1,
  "message": "Updated"
}
OUT ERROR (500):
{
  "message": "Update error"
}
~~~~~~
8. return a purchased ticket
~~~~~~
DELETE /api/v1/ticket
IN:
{
    "data": {
        "id": 1,
        "secret_key": "a1b2c3d4e5f6a1b2c3d4e5f6"
    }
}
OUT OK (200):
{
    "id": 8,
    "message": "Deleted"
}
OUT ERROR (500):
{
    "message": "Delete Error"
}
~~~~~~
9. all tickets
~~~~~~
GET /api/v1/ticket
IN:
{
    "data": {
        "secret_key": "a1b2c3d4e5f6a1b2c3d4e5f6"
    }
}
OUT OK (200):
{
    "items": [
        {"id":"3", "flight_id":"1", "place":"50",
            "user_email":"den5@gmail.com", 
            "date_create":"2020-05-05 00:12:06", "suma":"145"},
        {"id":"4", "flight_id":"1", "place":"51",
            "user_email":"den5@gmail.com", 
            "date_create":"2020-05-06 12:16:24", "suma":"145"}
    ],
    "message": "all"
}
OUT ERROR (500):
{
    "message": "Error"
}
~~~~~~
10. specific ticket
~~~~~~
GET /api/v1/ticket
IN:
{
    "data": {
        "id": 4,
        "secret_key": "a1b2c3d4e5f6a1b2c3d4e5f6"
    }
}
OUT OK (200):
{
    "item":{
        "id":"4", "flight_id":"1", "place":"50",
        "user_email":"den5@gmail.com",
        "date_create":"2020-05-06 12:16:24",
        "suma":"145"
    },
    "message": "all"
}
OUT ERROR (500):
{
    "message": "Data not found"
}
~~~~~~
11. event subscription
~~~~~~
POST /api/v1/callback/events
IN:
{
    "data": {
        "flight_id": 1,
        "triggered_at": 1585012345,
        "event": "flight_ticket_sales_completed",
        "secret_key": "a1b2c3d4e5f6a1b2c3d4e5f6"
    }
}
OUT OK (200):
{
    "message": "Does"
}
OUT ERROR (500):
{
    "message": "Error"
}
~~~~~~