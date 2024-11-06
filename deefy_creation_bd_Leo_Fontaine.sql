-- Adminer 4.8.1 MySQL 5.5.5-10.3.11-MariaDB-1:10.3.11+maria~bionic dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `playlist`;
CREATE TABLE `playlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `playlist` (`id`, `nom`) VALUES
(1,	'Best of Bruno Mars'),
(2,	'Best of Imagine Dragons'),
(3,	'80’s'),
(4,	'Podcasts');


DROP TABLE IF EXISTS `track`;
CREATE TABLE `track` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(100) NOT NULL,
  `genre` varchar(30) DEFAULT NULL,
  `duree` int(3) DEFAULT NULL,
  `filename` varchar(100) DEFAULT NULL,
  `type` varchar(30) DEFAULT NULL,
  `artiste_album` varchar(30) DEFAULT NULL,
  `titre_album` varchar(30) DEFAULT NULL,
  `annee_album` int(4) DEFAULT NULL,
  `numero_album` int(11) DEFAULT NULL,
  `auteur_podcast` varchar(100) DEFAULT NULL,
  `date_podcast` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `track` (`id`, `titre`, `genre`, `duree`, `filename`, `type`, `artiste_album`, `titre_album`, `annee_album`, `numero_album`, `auteur_podcast`, `date_podcast`) VALUES
(1,	'Grenade',	'pop',	222,	'grenade.mp3',	'A',	'Bruno Mars',	'Doo-Wops & Hooligans',	2010,	1,	NULL,	NULL),
(2,	'The Lazy Song',	'pop',	190,	'thelazysong.mp3',	'A',	'Bruno Mars',	'Doo-Wops & Hooligans',	2010,	2,	NULL,	NULL),
(3,	'Believer',	'rock',	204,	'believer.mp3',	'A',	'Imagine Dragons',	'Evolve',	2017,	1,	NULL,	NULL),
(4,	'Sharks',	'rock',	191,	'sharks.mp3',	'A',	'Imagine Dragons',	'Mercury – Acts 1 & 2',	2022,	1,	NULL,	NULL),
(5,	'L’aventurier',	'rock',	233,	'laventurier.mp3',	'A',	'Indochine',	'L’aventurier',	1982,	1,	NULL,	NULL),
(6,	'Cendrillon',	'rock',	239,	'cendrillon.mp3',	'A',	'Téléphone',	'Dure Limite',	1982,	1,	NULL,	NULL),
(7,	'Le système solaire',	'sciences',	1580,	'cpsLeSystemeSolaire.mp3',	'P',	NULL,	NULL,	NULL,	NULL,	'Jamy Gourmaud',	'2013-05-23'),
(8,	'Le soleil', 'sciences',	1562,	'cpsLeSoleil.mp3',	'P',	NULL,	NULL,	NULL,	NULL,	'Jamy Gourmaud',	'2013-05-23');

DROP TABLE IF EXISTS `User`;
CREATE TABLE `User` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(256) NOT NULL,
  `passwd` varchar(256) NOT NULL,
  `role` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `playlist2track`;
CREATE TABLE `playlist2track` (
                                  `id_pl` int(11) NOT NULL,
                                  `id_track` int(11) NOT NULL,
                                  `no_piste_dans_liste` int(3) NOT NULL,
                                  PRIMARY KEY (`id_pl`,`id_track`, `no_piste_dans_liste`),
                                  KEY `id_track` (`id_track`),
                                  CONSTRAINT `playlist2track_ibfk_1` FOREIGN KEY (`id_pl`) REFERENCES `playlist` (`id`),
                                  CONSTRAINT `playlist2track_ibfk_2` FOREIGN KEY (`id_track`) REFERENCES `track` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `playlist2track` (`id_pl`, `id_track`, `no_piste_dans_liste`) VALUES
                                                                              (1,	1,	1),
                                                                              (1,	2,	2),
                                                                              (2,	3,	1),
                                                                              (2,	4,	2),
                                                                              (3,	5,	1),
                                                                              (3,	6,	2),
                                                                              (4,	7,	1),
                                                                              (4,	8,	2);


INSERT INTO `User` (`id`, `email`, `passwd`, `role`) VALUES
(1,	'user1@mail.com',	'$2y$12$e9DCiDKOGpVs9s.9u2ENEOiq7wGvx7sngyhPvKXo2mUbI3ulGWOdC',	1),
(2,	'user2@mail.com',	'$2y$12$4EuAiwZCaMouBpquSVoiaOnQTQTconCP9rEev6DMiugDmqivxJ3AG',	1),
(3,	'user3@mail.com',	'$2y$12$5dDqgRbmCN35XzhniJPJ1ejM5GIpBMzRizP730IDEHsSNAu24850S',	1),
(4,	'user4@mail.com',	'$2y$12$ltC0A0zZkD87pZ8K0e6TYOJPJeN/GcTSkUbpqq0kBvx6XdpFqzzqq',	1),
(5,	'admin@mail.com',	'$2y$12$JtV1W6MOy/kGILbNwGR2lOqBn8PAO3Z6MupGhXpmkeCXUPQ/wzD8a',	100);

DROP TABLE IF EXISTS `user2playlist`;
CREATE TABLE `user2playlist` (
  `id_user` int(11) NOT NULL,
  `id_pl` int(11) NOT NULL,
  PRIMARY KEY (`id_user`,`id_pl`),
  KEY `id_pl` (`id_pl`),
  CONSTRAINT `user2playlist_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `User` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `user2playlist_ibfk_2` FOREIGN KEY (`id_pl`) REFERENCES `playlist` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `user2playlist` (`id_user`, `id_pl`) VALUES
(1,	1),
(1,	2),
(2,	3),
(3,	4);

-- 2022-10-14 12:55:42
