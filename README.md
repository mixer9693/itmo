# itmo
Для развертвания клонируйте проект, зайдите в папку

Выполните
```
php composer.phar install
```

Запустите Docker контейнер с БД
```
sudo docker-compose up -d
```

Сделайте миграцию
```
bin/console do:mi:mi
```

Запустите веб сервер.
Если установлен Symfony Local Web Server, выполните 
```
symfony server:start
```
