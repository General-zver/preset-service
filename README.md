# preset-service

серверначя часть:
Laravel 12.74.0 (Laravel уже изучен, а разбираться с phalcon могло занять слишком много времени и соответственно нет .volt шаблонов)
node.js 26.13.1
PHP 8.2.35



# установка (linux, apache)
# /etc/hosts добавить маршрут(в .env APP_URL)

# создать файл конфигурации apache с содержимым:
<VirtualHost *:80>

    ServerName preset-service.local #(ваш домен - удалить комментарий из конфига иначе будет ошибка)
    ServerAlias www.preset-service.local #(ваш домен - удалить комментарий из конфига иначе будет ошибка)
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html/preset-service.local #(ссылка на public из папки проекта - удалить комментарий из конфига иначе будет ошибка)
    <Directory "/var/www/html/preset-service.local"> #(ссылка на public из папки проекта - удалить комментарий из конфига иначе будет ошибка)
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
# после копирования проекта
# выполнить команды composer install
# npm i
# npm run dev/build
# создать файл .env из файла .env.example
# выполнить php artisan key:generate

# user
# user@example.com password

# manager
# manager@example.com password


# индексы:

# presets_status_index:
# ускоряет фильтрацию пресетов по статусу
#
# presets_price_index:
# ускоряет сортировку пресетов по цене
#
# presets_status_price_index:
# ускоряет выборку по статусу с одновременной сортировкой по цене
#
# products_price_index:
# ускоряет сортировку продуктов по цене
#
# products_preset_price_index:
# ускоряет получение продуктов пресета и вычисление их общей стоимости
#

# Способ защиты от гонки - пессимистическая блокировка на уровне строк
# первы запрос блокирует запись, второй ждет освобождения

# информация по эндпоинтам api по пути /docs      (openApi курильщика)

# Основные файлы проекта:
# PresetController, ProductController, файлы в разделе Requests, соотвествующие модели, routes/api и resources/js
# бэкап базы в проекте в папке backup