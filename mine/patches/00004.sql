create table act (
  id int not null auto_increment,
  name varchar(255) not null,
  year int not null,
  actTypeId int not null,
  status int not null,
  created int not null,
  modified int not null,

  primary key(id),
  key(year)
);
