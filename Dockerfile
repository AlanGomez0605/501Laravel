FROM mysql:8.0
ENV MYSQL_ROOT_PASSWORD=
ENV MYSQL_DATABASE=laravel_auth
ENV MYSQL_USER=admin
ENV MYSQL_PASSWORD=admin1234
EXPOSE 3306   
# Copia el archivo SQL al contenedor
COPY laravel_auth.sql /docker-entrypoint-initdb.d/

# Asegúrate de que el archivo tenga permisos correctos
RUN chmod 644 /docker-entrypoint-initdb.d/laravel_auth.sql   
