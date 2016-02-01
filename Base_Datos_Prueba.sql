drop database if exists prueba; ## ELiminar Base de Datos si Existe
create database prueba; ## Crea Base de Datos
use prueba; ## Seleccion la Base de Datos

## Creamos Tabla Usuarios
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

create table empleados(
	id_empleado int not null primary key auto_increment,
    id_usuario int not null,
    
    foreign key (id_usuario) references usuarios(id_usuario)
);

## Creamos Tabla Estudiantes
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