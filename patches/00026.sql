rename table reference to act_reference;

create table monitor_reference (
  id int not null auto_increment,
  actVersionId int not null,
  number varchar(255) not null,
  year int not null,
  monitorId int,
  created int not null,
  modified int not null,

  primary key(id),
  key(number, year),
  key(monitorId)
);
