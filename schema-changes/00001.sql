create table users (
  id int not null auto_increment,
  openid text,
  created datetime,
  modified datetime,
  primary key (id),
  unique key (openid(300))
);

alter table raw_texts add progress tinyint(2) after script_version;
alter table raw_texts add owner int after progress;
update raw_texts set progress = 0;
