
CREATE TABLE files (
   id int(255) DEFAULT '0' NOT NULL auto_increment,
   catid int(255) DEFAULT '0' NOT NULL,
   folderid int(255) DEFAULT '0' NOT NULL,
   filesize varchar(255) NOT NULL,
   filename varchar(255) NOT NULL,
   filetype varchar(255) NOT NULL,
   description text NOT NULL,
   votes int(255) DEFAULT '0' NOT NULL,
   allvotes int(255) DEFAULT '0' NOT NULL,
   downloads int(255) DEFAULT '0' NOT NULL,
   url text NOT NULL,
   UNIQUE id (id)
);

CREATE TABLE files_catagory (
   id int(255) DEFAULT '0' NOT NULL auto_increment,
   name varchar(255) NOT NULL,
   description text NOT NULL,
   files int(255) DEFAULT '0' NOT NULL,
   upload int(1) DEFAULT '0' NOT NULL,
   UNIQUE id (id)
);

CREATE TABLE files_folders (
   id int(255) DEFAULT '0' NOT NULL auto_increment,
   catid int(255) DEFAULT '0' NOT NULL,
   parentid int(255) DEFAULT '0' NOT NULL,
   name varchar(255) NOT NULL,
   description text NOT NULL,
   files int(255) DEFAULT '0' NOT NULL,
   UNIQUE id (id)
);
