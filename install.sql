DROP TABLE IF EXISTS wcf1_package_installation_licensetext;
CREATE TABLE wcf1_package_installation_licensetext (
  packageID int(10) NOT NULL default 0,
  languageID int(10) NOT NULL,
  defaultLang tinyint(1) NOT NULL default 0,
  licenseText longtext NOT NULL,
  PRIMARY KEY  (packageID,languageID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
