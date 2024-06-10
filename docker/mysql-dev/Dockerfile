FROM mysql:8.2.0

COPY docker/mysql-dev/*.sql /docker-entrypoint-initdb.d/

COPY docker/mysql-dev/mysqld.cnf /etc/mysql/conf.d/mysqld.cnf