# Simple REST Full API project

## Реализация небольшой простой REST Full API для авиакомпании

### (c) Denis Puzik <scorpion3dd@gmail.com>

- API реагирует на следующие запросы-команды пользователя::
~~~~
1. забронировать место в рейсе;
2. отменить бронь;
3. купить билет;
4. возвратить купленный билет.
~~~~
Место - обычное число от 1 до 150. 

Купить билет можно как после бронирования, так и без него. 

Функционал оплаты и возврата денег реализовыван в виде измения состояний.

- API подписан на получение событий через HTTP протокол (callback-уведомления) 
на один из своих адресов - http://localhost/api/v1/callback/events 
~~~~~~
Типы уведомлений:
- завершена продажа билетов на рейс;
- рейс отменён.
~~~~~~
- Пример уведомления:  
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
При отмене рейса пользователям, забронировавшим или купившим билеты на этот рейс, 
в фоновом режиме отправляются эл. письма об отмене рейса. 

В случае ответа на событие HTTP кодом отличным от 200 событие будет повторно получено 
через некоторый промежуток времени.


### Установка:

> 1 Созлать БД airlines
>
> 2 В БД airlines создать таблицы, выполнив SQL-скрипт:
> 
> CREATE TABLE airlines.reservation (
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
>
> CREATE TABLE airlines.ticket (
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
> 
> 2 В файле \api\SuperRepository.php при необходимости изменить параметры PARAMS
> 
> 3 Прописать настройки Web-сервера Apache на папку с проектом

### Описание API-запросов:

1 забронировать место в рейсе
~~~~~~
POST
http://localhost/api/v1/reservation 
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
2 изменить бронь
~~~~~~
PUT
http://localhost/api/v1/reservation 
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
3 отменить бронь
~~~~~~
DELETE
http://localhost/api/v1/reservation
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
4 все брони
~~~~~~
GET
http://localhost/api/v1/reservation
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
5 конкретная бронь
~~~~~~
GET
http://localhost/api/v1/reservation
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

6 купить билет
~~~~~~
POST
http://localhost/api/v1/ticket 
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
7 изменить билет
~~~~~~
PUT
http://localhost/api/v1/ticket 
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
8 возвратить купленный билет
~~~~~~
DELETE
http://localhost/api/v1/ticket
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
9 все билеты
~~~~~~
GET
http://localhost/api/v1/ticket
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
10 конкретный билет
~~~~~~
GET
http://localhost/api/v1/ticket
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

11 подписка на получение событий
~~~~~~
POST
http://localhost/api/v1/callback/events
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