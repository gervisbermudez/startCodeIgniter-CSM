-- Dar permisos a ci_user para todas las bases de datos
GRANT ALL PRIVILEGES ON *.* TO 'ci_user'@'%' WITH GRANT OPTION;
FLUSH PRIVILEGES;
