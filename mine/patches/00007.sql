alter table act add monitorId int not null after issueDate, add key(monitorId);
alter table act change year year int;

create table monitor (
  id int not null auto_increment,
  number varchar(255) not null,
  year int not null,
  issueDate date not null,
  created int not null,
  modified int not null,

  primary key(id),
  key(year)
);
