create table reference (
  id int not null auto_increment,
  actVersionId int not null,
  actTypeId int not null,
  number varchar(255) not null,
  year int not null,
  referredActId int,
  created int not null,
  modified int not null,

  primary key(id),
  key(actTypeId, number, year),
  key(referredActId)
);
