. .env; docker-compose exec webserver_mysql mysql -u root -p$MYSQL_ROOT_PASSWORD -e "USE dbctf; ALTER TABLE flags MODIFY  COLUMN CHALLID varchar(120)"
