--
-- Estrutura da tabela `cms_apcefpb_phones`
--

CREATE TABLE IF NOT EXISTS `cms_apcefpb_phones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `phone_number` varchar(100) NOT NULL,
  `note` text NOT NULL,
  `state` tinyint(4) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `alter_date` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  `alter_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `phone_number` (`phone_number`),
  KEY `client_id` (`client_id`,`plan_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;




-- -------------------------------------------------------------------------


MIGRAÇÃO


1 - Copiar a tabela "cms_apcefpb_dependents" da base antiga e copiar para a tabela "migracao_dependents" na base nova

2 - Rodar o comando abaixo (migração dos dados da tabela "dependents" antiga para a nova)

INSERT INTO `cms_apcefpb_dependents` (
	`id`,
	`client_id`,
	`group_id`,
	`name`,
	`card_name`,
	`email`,
	`gender`,
	`birthday`,
	`end_date`,
	`docs`,
	`note`,
	`state`,
	`created_date`,
	`created_by`
) SELECT
	`id`,
	`client_id`,
	`group_id`,
	`name`,
	`name_card`,
	`email`,
	`gender`,
	`birthday`,
	`end_date`,
	`docs`,
	`note`,
	`state`,
	`created_date`,
	`created_by`
FROM `migracao_dependents`
ORDER BY `id`

3 - Migrar telefones
???

4 - Copiar dados da tabela "cms_apcefpb_dependents_group" com o valor da tabela antiga

5 - Copiar dados da tabela "migracao_dependents_files" para "cms_apcefpb_dependents_files"

6 - Copiar os arquivos do antigo "images/uploads/dependents" para o novo "images/apps/dependents"
