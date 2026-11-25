# preset-service

серверначя часть:
Laravel 12.74.0 (Laravel уже изучен, а разбираться с phalcon могло занять слишком много времени)
node.js 26.13.1



# установка (linux, apache)
/etc/hosts добавить маршрут(в .env APP_URL)

создать файл конфигурации apache с содержимым:
<VirtualHost *:80>

    ServerName preset-service.local #(ваш домен)
    ServerAlias www.preset-service.local #(ваш домен)
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html/preset-service.local #(ссылка на public из папки проекта)
    <Directory "/var/www/html/preset-service.local"> #(ссылка на public из папки проекта)
    Options Indexes FollowSymLinks MultiViews
    AllowOverride All
    Order allow,deny
    allow from all
    Require all granted
    </Directory>
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined

</VirtualHost>

# создание ссылка
# ln -s [полный путь до папки]/public/ preset-service.local



# user
# user@example.com password

# manager
# manager@example.com password