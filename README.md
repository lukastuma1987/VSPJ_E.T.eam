## Zprovoznění na localhostu

Pro zprovoznění na localhostu spuste následující příkazy ve složce projektu:

* `composer install`

Pokud jste při instalaci závislostí nevyplnili/špatně zadali připojení k databázi/smtp serveru, upravte `app/config/parameters.yml`

* `php bin/console doctrine:migrations:migrate`
* `php bin/console server:run`

## Požadavky

* PHP verze 7.2 a vyšší
* Databázový server MariaDB nebo MySQL
* Pro produkční prostředí server Apache nebo Nginx

## Vytvoření uživatele s administrátorskými právy

`php bin/console et:create-admin-user`
