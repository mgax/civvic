create table place (
  id int not null auto_increment,
  name varchar(255) not null,
  created int not null,
  modified int not null,

  primary key(id)
);

alter table act add placeId int after monitorId;
