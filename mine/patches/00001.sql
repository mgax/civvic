create table user (
  id int not null auto_increment,
  identity text,
  nickname varchar(255),
  email varchar(255),
  admin tinyint(1),
  created int,
  modified int,

  primary key(id),
  unique key(identity(300))
);

create table variable (
  id int not null auto_increment,
  name varchar(100) not null,
  value varchar(100) not null,
  created int,
  modified int,

  primary key(id),
  unique key(name)
);
