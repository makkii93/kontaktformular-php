create table kontakte(
    id int(11) auto_increment primary key,
    name varchar(120) not null,
    email varchar(120) not null,
    grund varchar(600) not null,
    nachricht text
);