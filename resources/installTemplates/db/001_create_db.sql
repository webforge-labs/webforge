CREATE USER 'hagedorn'@'localhost' IDENTIFIED BY '%db.password%';

GRANT USAGE ON * . * TO '%db.user%'@'localhost' IDENTIFIED BY '%db.password%' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;

CREATE DATABASE IF NOT EXISTS `%db.name%` ;
GRANT ALL PRIVILEGES ON `%db.name%` . * TO '%db.user%'@'localhost';

CREATE DATABASE IF NOT EXISTS `%db.name%_tests`;
GRANT ALL PRIVILEGES ON `%db.name%_tests` . * TO '%db.user%'@'localhost';
