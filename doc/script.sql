drop database projetopontodb;
create database projetopontodb; 
use projetopontodb; 

drop table users;

create table users (
	id int not null auto_increment primary key,
	name varchar(100),
	email varchar(40),
    username varchar(30),
	password varchar(100),
    authToken text
);

select * from users;


insert into users(name,email,username,password) values 
('Walter','Waltinho@gmail.com','_tangsan',UPPER(MD5('leitecondensado')));


SELECT id FROM users 
WHERE users.username='_tangsan' AND users.password= UPPER(MD5('leitecondensado'));
