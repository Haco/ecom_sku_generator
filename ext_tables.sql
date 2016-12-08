#
# Table structure for table 'tx_ecomskugenerator_domain_model_configuration'
#
CREATE TABLE tx_ecomskugenerator_domain_model_configuration (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	content int(11) unsigned DEFAULT '0' NOT NULL,

	title varchar(255) DEFAULT '' NOT NULL,
	sku varchar(255) DEFAULT '' NOT NULL,
	parts int(11) unsigned DEFAULT '0' NOT NULL,
	pricing int(11) unsigned DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid)

);

#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content (

	sku_generator_part_groups int(11) unsigned DEFAULT '0' NOT NULL,
	sku_generator_configurations int(11) unsigned DEFAULT '0' NOT NULL,
	sku_generator_pricing int(11) unsigned DEFAULT '0' NOT NULL,
	sku_generator_pricing_enabled tinyint(1) unsigned DEFAULT '0' NOT NULL,
	sku_generator_allow_custom_config tinyint(1) unsigned DEFAULT '0' NOT NULL

);

#
# Table structure for table 'tx_ecomskugenerator_domain_model_partgroup'
#
CREATE TABLE tx_ecomskugenerator_domain_model_partgroup (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	content int(11) unsigned DEFAULT '0' NOT NULL,

	title varchar(255) DEFAULT '' NOT NULL,
	icon int(11) unsigned NOT NULL default '0',
	prompt text NOT NULL,
	prompt_wrap int(11) DEFAULT '0' NOT NULL,
	settings int(11) DEFAULT '0' NOT NULL,
	parts int(11) unsigned DEFAULT '0' NOT NULL,
	dependent_notes int(11) unsigned DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,
	fe_group varchar(100) DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
	KEY language (l10n_parent,sys_language_uid)

);

#
# Table structure for table 'tx_ecomskugenerator_domain_model_part'
#
CREATE TABLE tx_ecomskugenerator_domain_model_part (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	part_group int(11) unsigned DEFAULT '0' NOT NULL,

	title varchar(255) DEFAULT '' NOT NULL,
	image int(11) unsigned NOT NULL default '0',
	hint text NOT NULL,
	incompatible_note text NOT NULL,
	pricing int(11) unsigned DEFAULT '0' NOT NULL,
	min_order_quantity int(11) unsigned DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,
	fe_group varchar(100) DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
	KEY language (l10n_parent,sys_language_uid)

);

#
# Table structure for table 'tx_ecomskugenerator_domain_model_dependentnote'
#
CREATE TABLE tx_ecomskugenerator_domain_model_dependentnote (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	part_group int(11) unsigned DEFAULT '0' NOT NULL,

	note text NOT NULL,
	note_wrap int(11) DEFAULT '0' NOT NULL,
	use_logical_and tinyint(1) unsigned DEFAULT '0' NOT NULL,
	dependent_parts int(11) unsigned DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
	KEY language (l10n_parent,sys_language_uid)

);

#
# Table structure for table 'tx_ecomconfigcodegenerator_domain_model_price'
#
CREATE TABLE tx_ecomconfigcodegenerator_domain_model_price (

	content  int(11) unsigned DEFAULT '0' NOT NULL,
	sku_configuration int(11) unsigned DEFAULT '0' NOT NULL,
	sku_part int(11) unsigned DEFAULT '0' NOT NULL

);

#
# Table structure for table 'tx_ecomconfigcodegenerator_domain_model_log'
#
CREATE TABLE tx_ecomconfigcodegenerator_domain_model_log (

  incompatible_config tinyint(4) unsigned DEFAULT '0' NOT NULL,
	configured_parts_sku text

);

#
# Table structure for table 'tx_ecomskugenerator_configuration_part_mm'
#
CREATE TABLE tx_ecomskugenerator_configuration_part_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_ecomskugenerator_dependentnote_part_mm'
#
CREATE TABLE tx_ecomskugenerator_dependentnote_part_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);
