Haru
====

Рожденный весной.  
Набор инструкция для phing, предназначенный
для автоматизации создания инструкций сборки проектов и установки проекта.

## Требования к ПО
PHP 5.3

## Установка
Скачайте и распакуйте архив необходимой версии программы [Haru](https://github.com/TheRatG/haru)  
Или  
Скопируйте репозиторий

    git clone git://github.com/TheRatG/haru.git

## Быстрый старт

Выполните следующий команды

    git clone git://github.com/TheRatG/haru.git
    cd haru
    ./phing born -Dpath=/www/kin -Dhost=kin.dv -Dlib=kin -Dget-build.deploy.setup=1
    
**Учебный проект готов**  
Для запуска сайта настройте виртуальный хост: 

    ## создайте ссылку на кофигурационный файл для apache
    ## например
    sudo ln -s /www/kin/etc/kin.conf /usr/local/apache2/conf.d/kin.conf

### Born


###Get Build