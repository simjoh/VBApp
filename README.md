# VBApp

App för att administrera randonnéelopp

Består av ett gui skriver i Angular och ett api skrivet i PHP

## Projektstruktur

* [Brevet-gui](frontend/docs/README.md) - Angular frontend för administration
* [Brevet-rider](frontend-rider/docs/README.md) - Angular frontend för cyklister
* [Brevet-api](api/docs/README.md) - PHP API backend
* [Loppservice-api](loppservice/docs/README.md) - Laravel API service

## Systemkrav

- Docker & docker compose
- Angular cli
- php
- composer

### Starta lokalt för utveckling

### Starta allt i docker

* [Docker](docker/README.md)

#### Endast brevet-gui

* [Frontend](frontend/docs/README.md)

#### Endast brevet-rider

* [Frontend Rider](frontend-rider/docs/README.md)

#### Endast brevet-api

* [Brevet-api](api/docs/README.md)

#### Endast loppservice-api

* [Loppservice-api](loppservice/docs/README.md)



### phpmyadmin

  localhost:8190
  - Välj db-1 (loppservice) eller db-2 (Västerbottenbrevet app)
	### Inte så säkra inloggningsuppgidter
  - User: myuser
  - Pass: secret