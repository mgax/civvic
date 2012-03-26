alter table act
  drop status,
  add number varchar(255) after name,
  add issueDate date not null after actTypeId;

create table act_version (
  id int not null auto_increment,
  actId int not null,
  modifyingActId int,
  status int,
  contents longtext,
  diff longtext,
  versionNumber int not null,
  current tinyint(1),

  created int not null,
  modified int not null,

  primary key(id),
  key(actId)
);
