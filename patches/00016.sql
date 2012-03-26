alter table act_type add shortName varchar(255) after name;
update act_type set shortName = name;
