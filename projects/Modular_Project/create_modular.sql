-- ISTE 240
-- Modified Spring 2023


DROP table IF EXISTS  modularSite; 

CREATE TABLE modularSite (
   ID smallint UNSIGNED ZEROFILL PRIMARY KEY AUTO_INCREMENT,
   page varchar(14),
   content mediumtext,
   CSS_Internal mediumtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 

INSERT INTO modularSite(ID,page,content,CSS_Internal)
  VALUES(
	"1",
	"home",
	"<h2>This site is about fishing</h2>
         <p>Data coming from table moduleSite</p>
         <p>Living in upstate NY provides excellent fishing locations
            - Fishing in Florida in the summer is also fantastic</p>",
         NULL
        );

