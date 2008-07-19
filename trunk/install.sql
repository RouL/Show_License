### License-PIP MySQL4.1 table structure

DROP TABLE IF EXISTS wcf1_package_installation_licensetext;
CREATE TABLE wcf1_package_installation_licensetext (
  packageID int(10) unsigned NOT NULL default '0',
  languageID int(10) NOT NULL,
  defaultLang tinyint(1) unsigned NOT NULL default '0',
  licenseText longtext NOT NULL,
  PRIMARY KEY  (packageID,languageID),
  UNIQUE KEY packageID (packageID,defaultLang)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
