CREATE TABLE `tab_agendador` (
  `agn_id` int(11) NOT NULL AUTO_INCREMENT,
  `agn_data_disparo` datetime DEFAULT NULL,
  `agn_template` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
  `agn_status` enum('enviado','nao_enviado') COLLATE utf8_unicode_ci DEFAULT 'nao_enviado',
  `agn_assunto` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`agn_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `tab_contatos` (
  `id_contato` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) DEFAULT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `status_permissao_envio` enum('inativo','ativo') DEFAULT 'ativo',
  `status_envio` enum('inativo','ativo') DEFAULT 'inativo',
  `motivo_remocao` text,
  `dinamic_content` text,
  PRIMARY KEY (`id_contato`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `tab_relatorio` (
  `id_relatorio` int(11) NOT NULL AUTO_INCREMENT,
  `fkid_contato` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `data_status` datetime DEFAULT NULL,
  PRIMARY KEY (`id_relatorio`),
  KEY `tab_relatorio_ibfk_1` (`fkid_contato`),
  CONSTRAINT `tab_relatorio_ibfk_1` FOREIGN KEY (`fkid_contato`) REFERENCES `tab_contatos` (`id_contato`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `tab_sendmail` (
  `snd_id` int(11) NOT NULL AUTO_INCREMENT,
  `snd_email_envio` varchar(255) DEFAULT NULL,
  `snd_nome` varchar(255) DEFAULT NULL,
  `snd_login` varchar(255) DEFAULT NULL,
  `snd_senha` varchar(255) DEFAULT NULL,
  `snd_ssl_protocolo` enum('ssl','tls','inativo') DEFAULT 'inativo',
  `snd_smtp` varchar(255) DEFAULT NULL,
  `snd_port` int(11) DEFAULT NULL,
  PRIMARY KEY (`snd_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
