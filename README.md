Hello
I used nanoninja/docker-nginx-php-mysql as fast skeleton for develop.

For start application:
0. Download docker, docker-compose
1. ```cp web/app/composer.json.dist web/app/composer.json```
2. ```sudo docker-compose up -d```
3. ```sudo docker run --rm -v $(pwd)/web/app:/app composer require php-di/php-di```
4. I created dump of test database
5. ```source .env && sudo docker exec -i $(sudo docker-compose ps -q mysqldb) mysql -u"$MYSQL_ROOT_USER" -p"$MYSQL_ROOT_PASSWORD" < "data/db/dumps/db.sql"```
6. Go to ```http://http://localhost:8000```
6. User credential ```test/test, test2/test2```


I did not spent a lot of time on UI/UX.
I did not add pagination, all indexes in db added so adding pagination in future is not a problem.

One external lib - di container.

Thanks.
