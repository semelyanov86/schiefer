UPDATE vtiger_field SET uitype = 16 WHERE columnname = 'direction' AND tablename = 'vtiger_pbxmanager';

DROP TABLE IF EXISTS vtiger_callstatus;
CREATE TABLE vtiger_callstatus (
  callstatusid int(19) NOT NULL AUTO_INCREMENT,
  callstatus varchar(200) DEFAULT NULL,
  presence int(1) NOT NULL DEFAULT '1',
  picklist_valueid int(19) NOT NULL DEFAULT '0',
  sortorderid int(2) DEFAULT '0',
  PRIMARY KEY (callstatusid)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO vtiger_callstatus (callstatusid, callstatus, presence, picklist_valueid, sortorderid) VALUES
(1, 'busy',     1,  0,  0),
(2, 'CANCEL',   1,  0,  0),
(3, 'ANSWER',   1,  0,  0),
(4, 'MISSED',   1,  0,  0);

DROP TABLE IF EXISTS vtiger_direction;
CREATE TABLE vtiger_direction (
  directionid int(19) NOT NULL AUTO_INCREMENT,
  direction varchar(200) DEFAULT NULL,
  presence int(1) NOT NULL DEFAULT '1',
  picklist_valueid int(19) NOT NULL DEFAULT '0',
  sortorderid int(2) DEFAULT '0',
  PRIMARY KEY (directionid)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- TRUNCATE vtiger_direction
INSERT INTO vtiger_direction (directionid, direction, presence, picklist_valueid, sortorderid) VALUES
(1, 'inbound',  1,  0,  0),
(2, 'outbound', 1,  0,  0),
(3, 'internal', 1,  0,  0);

CREATE TABLE vtiger_pbxopts (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(250) NOT NULL,
  value varchar(250) NULL DEFAULT '',
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
