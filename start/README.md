What's new in Symfony 2.2 + ESI Fragments Bonus Tutorial
========================================================

This code represents the staring point of the screencast. To get things working,
try the following steps:

1) Update your vendors

    php composer.phar install

2) Customize your `app/config/parameters.yml` file

3) Fix your permissions

    chmod -R 777 app/cache app/logs

4) Build your database

    php app/console doctrine:database:create
    php app/console doctrine:schema:create
    php app/console doctrine:fixtures:load

5) Setup a virtualhost that points to the web/ directory and a hosts entry
   for your fake domain

6) Pop it open in your browser!

7) Dance and celebrate!
