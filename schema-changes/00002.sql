alter table pdf_documents add md5_sum varchar(100) not null, add page_count int not null, add created datetime, add modified datetime;
alter table raw_texts drop pdf_md5;
