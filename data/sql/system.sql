DROP DATABASE IF EXISTS :database_name;

DROP USER IF EXISTS :user;
CREATE USER :user WITH PASSWORD :user_password;
ALTER USER :user CREATEDB
