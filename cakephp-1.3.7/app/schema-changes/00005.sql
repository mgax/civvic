alter table users add admin tinyint(1) after openid;
update users set admin = 1 where openid like '%catalin.francu%';
