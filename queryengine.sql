-- phpMyAdmin SQL Dump
-- version 4.1.4
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Sam 05 Décembre 2015 à 22:28
-- Version du serveur :  5.6.15-log
-- Version de PHP :  5.4.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `queryengine`
--

-- --------------------------------------------------------

--
-- Structure de la table `article_tbl`
--

CREATE TABLE IF NOT EXISTS `article_tbl` (
  `ID_ARTICLE` int(11) NOT NULL AUTO_INCREMENT,
  `TITRE_ARTICLE` varchar(50) NOT NULL,
  `RESUME_ARTICLE` varchar(150) NOT NULL,
  `CONTENT_ARTICLE` text NOT NULL,
  `AUTEUR` int(11) NOT NULL,
  PRIMARY KEY (`ID_ARTICLE`),
  KEY `AUTEUR` (`AUTEUR`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `article_tbl`
--

INSERT INTO `article_tbl` (`ID_ARTICLE`, `TITRE_ARTICLE`, `RESUME_ARTICLE`, `CONTENT_ARTICLE`, `AUTEUR`) VALUES
(1, 'article 1', 'Ceci est un article test', '<h2>Titre</h2>\r\n<p>Ceci est un article de remplissage.</p>\r\n<p>Il contient des mot clefs tel que téléphone, assurance, tarifs ou service</p>\r\n<h2>Service</h2>\r\n<p>Ici je presente des service mais sans les décrire</p>', 1),
(2, 'Forfait 1', 'Présentation du forfait 1', '<h2>Définition</h2>\r\n<p>Forfait à destination de personnes qui ont l''assurance de trouver un emploi prochainement</p>\r\n<h2>Tarifs</h2>\r\n<p>Le forfait est un forfait qui propose 2 grille de tarifs.\r\n<ul>\r\n<li>Un forfait à 12.99 € par mois sans internet</li>\r\n<li>Un forfait à 24.75 € par mois avec 6 Go d''internet</li>\r\n</ul>\r\n</div>\r\n</p>', 1),
(3, 'Assurance', 'Ici un article sur les assurances', '<h2>Définition</h2>\r\n<p>Mon activité peut présenter des tarif d''<b>assurance</b> intéressant.</p>\r\n<h2>Tarifs</h2>\r\n<p>Le forfait d''assurance proposé a un tarif très attractif.</p>\r\n<p>Il existe <b>2 tarifs</b> proposés.</p>', 1),
(4, 'article 2', 'Ceci est un article de remplissage', '<h2>Présentation</h2>\r\n<p>Il s''agit d''un article de remplissage avec un tableau</p>\r\n<table>\r\n<tr>\r\n<th>Titre 1</th>\r\n<th>Prix</th>\r\n</tr>\r\n<tr>\r\n<td>Forfait 1</td>\r\n<td>12.99 ou 24.70 €</td>\r\n</tr>\r\n<tr>\r\n<td>Assurance</td>\r\n<td>pas de tarifs communiqués</td>\r\n</tr>\r\n</table>', 1);

-- --------------------------------------------------------

--
-- Structure de la table `auteur_tbl`
--

CREATE TABLE IF NOT EXISTS `auteur_tbl` (
  `ID_AUTEUR` int(11) NOT NULL AUTO_INCREMENT,
  `NOM_AUTEUR` varchar(40) NOT NULL,
  `PRENOM_AUTEUR` varchar(40) NOT NULL,
  PRIMARY KEY (`ID_AUTEUR`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `auteur_tbl`
--

INSERT INTO `auteur_tbl` (`ID_AUTEUR`, `NOM_AUTEUR`, `PRENOM_AUTEUR`) VALUES
(1, 'DESMARTIN', 'Paul');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
