CREATE TABLE tx_easyshop_payment_log_products_mm (
  uid_local int(11) DEFAULT '0' NOT NULL,
  uid_foreign int(11) DEFAULT '0' NOT NULL,
  tablenames varchar(30) DEFAULT '' NOT NULL,
  sorting int(11) DEFAULT '0' NOT NULL,
  num int(11) NOT NULL DEFAULT '0',
  prop int(11) NOT NULL DEFAULT '0',
  price tinytext COLLATE utf8_slovenian_ci,
  web_price tinytext COLLATE utf8_slovenian_ci,
  KEY uid_local (uid_local),
  KEY uid_foreign (uid_foreign)
);