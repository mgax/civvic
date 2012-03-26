create table raw_text (
  id int not null auto_increment,
  year int not null,
  number varchar(255) not null,
  extractedText mediumblob,
  pageCount int,
  progress int,
  difficulty int,
  userId int,
  created int not null,
  modified int not null,

  primary key(id)
);
