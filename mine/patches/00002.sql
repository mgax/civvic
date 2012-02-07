alter table user add name varchar(255) after nickname;

create table login_cookie (
  id int not null auto_increment,
  userId int not null,
  value varchar(20) not null,
  created int not null,
  modified int not null,

  primary key(id),
  key(value)
);
