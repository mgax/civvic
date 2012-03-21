drop table if exists act_author;

create table act_author (
  id int not null auto_increment,
  actId int not null,
  authorId int not null,
  rank int not null,
  created int not null,
  modified int not null,
  primary key(id),
  key(actId),
  key(authorId)
);

insert into act_author (actId, authorId) select id, authorId from act;

alter table act drop authorId;
