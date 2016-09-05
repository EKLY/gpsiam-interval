# gpsiam-interval

## Install MySQL and PHP5

````
apt-get install -y mysql-server php5-cli php5-mysql git
````

## Config MySQL for connect from outside

goto file `/etc/mysql/my.cnf`

add # to beginning of a line

````
# bind-address = 127.0.0.1
````

save and restart mysql server


## Add user MySQL and Create DB

````
CREATE USER 'tracking'@'localhost' IDENTIFIED BY 'tracking';

GRANT ALL PRIVILEGES ON *.* TO 'tracking'@'localhost' IDENTIFIED BY 'tracking' WITH GRANT OPTION MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;

CREATE DATABASE tracking COLLATE 'utf8_general_ci';
````


Optional for connect from outside

````
CREATE USER 'tracking_outside'@'%' IDENTIFIED BY 'tracking456';

GRANT SELECT ON *.* TO 'tracking_outside'@'%' IDENTIFIED BY 'tracking456' WITH GRANT OPTION MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;
````


Optional for connect root from outside

````
CREATE USER 'tracking_root'@'%' IDENTIFIED BY '789456zA';

GRANT ALL PRIVILEGES ON *.* TO 'tracking_root'@'%' IDENTIFIED BY '789456zA' WITH GRANT OPTION MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;
````



## Clone this Project

````
git clone https://github.com/EKLY/gpsiam-interval.git
````


## Setup Cornjob

````
php -q /root/gpsiam-interval/interval.php
````
