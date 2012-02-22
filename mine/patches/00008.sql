create table author (
  id int not null auto_increment,
  institution varchar(255),
  position varchar(255),
  title varchar(255),
  name varchar(255),
  created int not null,
  modified int not null,

  primary key(id)
);

alter table act add authorId int after issuedate;
