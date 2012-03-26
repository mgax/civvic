create table if not exists cropped_image (
  id int not null auto_increment,
  name varchar(255) not null,
  contents longblob not null default '',
  monitorNumber varchar(255) not null,
  monitorYear int not null,
  monitorPage int not null,
  zoom int not null,
  x0 int not null,
  y0 int not null,
  width int not null,
  height int not null,
  created int not null,
  modified int not null,

  primary key(id),
  unique key(name)
);
