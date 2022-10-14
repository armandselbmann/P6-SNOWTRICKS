# Projet 6 OpenClassrooms - Développez de A à Z le site communautaire SnowTricks

## Parcours Développeur d'application - PHP / Symfony
**Armand Selbmann**

---
## Code quality
The code has benn verified by Codacy.<br>
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/39ff8a0047154b0d892fa930cd7b5277)](https://www.codacy.com/gh/armandselbmann/P6-SNOWTRICKS/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=armandselbmann/P6-SNOWTRICKS&amp;utm_campaign=Badge_Grade)
---
## Online demo
[snowtricks.armand-selbmann.fr](https://snowtricks.armand-selbmann.fr/)

---
## Setup project
### Clone the Repository
SSH : 
```
git clone git@github.com:armandselbmann/P6-SNOWTRICKS.git
```
HTTPS : 
```
git clone https://github.com/armandselbmann/P6-SNOWTRICKS.git
```

### Technical requirements
PHP 8.1.7 or above </br>
Composer </br>
[Symfony CLI](https://symfony.com/download) </br>
Yarn </br>

You can run this command to check wether you are missing any extensions or not.
```
symfony check:requirements
```
Proceed to the next step if your system is ready to run Symfony projects.

### Download Composer dependencies
Make sure you have [Composer installed](https://getcomposer.org/download/)
and then run:

```
composer install
```

## Database setup
### Configuring Database access
At the root of the project, you need to update the .env file to configure the access to the Database.</br>
You must override this following variable :
```
DATABASE_URL="mysql://root:root@127.0.0.1:3306/snowtricks?serverVersion=5.7&charset=utf8mb4"
``` 

### Creating Database and insertion of the data
Creating the database :
```
php bin/console doctrine:database:create
```
Creating the Database Tables/Schema :
```
php bin/console doctrine:migrations:migrate
```
Loading initial data :
```
php bin/console doctrine:fixtures:load
```

## Email Setup

Don't forget to configure the mail server in the .env file to
operate the functionnalities related to the use of the mail service.<br>
Here is the variable to update :
```
MAILER_DSN=null://null
```

## JSON Web Token Setup
Add this in the .env
```
###> JWT Service ###
JWT_SECRET='azerty'
###< JWT Service ###
```
---
## Launch the WebServer
```
symfony serve
```

## Default User Connexion
```
login : lolo
password : lolo
```