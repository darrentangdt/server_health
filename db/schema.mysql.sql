GRANT ALL PRIVILEGES ON *.* TO 'sh'@'%' identified by '545124315@qq.com' with grant option;
GRANT ALL PRIVILEGES ON *.* TO 'sh'@'localhost' identified by '545124315@qq.com' with grant option;
GRANT ALL PRIVILEGES ON *.* TO 'sh'@'127.0.0.1' identified by '545124315@qq.com' with grant option;

drop database if exists sh;

create database sh;

use sh;

create table tbl_server
(
  ip VARCHAR(128) NOT NULL,
  username VARCHAR(128) NOT NULL,
  password VARCHAR(128) NOT NULL,
  so VARCHAR(128) NOT NULL,
  name VARCHAR(128) NOT NULL,
  identify_updated DATETIME NOT NULL,
  auth INT(11) NOT NULL,
  health_updated DATETIME NOT NULL,
  health_ret INT(11) NOT NULL,
  health_out TEXT NOT NULL,
  ext1 TEXT NOT NULL,
  ext2 TEXT NOT NULL,
  ext3 TEXT NOT NULL,
  PRIMARY KEY(ip)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
