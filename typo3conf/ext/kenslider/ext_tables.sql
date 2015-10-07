#
# Table structure for table 'tx_kenslider_entry'
#
CREATE TABLE tx_kenslider_entry (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	image text,
	thumb text,
	initialzoom tinytext,
	finalzoom tinytext,
	link tinytext,
	linktarget tinyint(3) DEFAULT '0' NOT NULL,
	iframelink tinytext,
	text1 text,
	text2 text,
	text3 text,
	style int(11) DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
) ENGINE=InnoDB;