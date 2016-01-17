drop database if exists prueba;
create database prueba;
use prueba;

create table usuarios (
	id_usuario int not null primary key auto_increment,
	nombre varchar(100),
    apellido varchar(100),
    usuario varchar(100),
    clave varchar(100),
    fecha date
);
insert into usuarios(nombre, apellido, usuario, clave, fecha) values
('Walter Ramon','Corrales Diaz','walter2015','123456',current_date()),
('Andrea Carolina','Morazan Carvajal','andrea2015','123456',current_date());

create table estudiantes(
	id_estudiante int not null primary key auto_increment,
    nombre varchar(100) not null,
    apellido varchar(100) not null,
    telefono int not null,
    fecha date,
    estado boolean default 1
);
insert into estudiantes(nombre,apellido,telefono,fecha) values
('Oscar Francisco','Alfaro Villavicencio',12345678,current_date()),
('Axel Amaru','Matus Davila',12345678,current_date());