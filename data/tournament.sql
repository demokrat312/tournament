-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Дек 07 2018 г., 14:55
-- Версия сервера: 5.7.24-0ubuntu0.16.04.1
-- Версия PHP: 7.1.16-1+ubuntu16.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `tournament`
--
CREATE DATABASE IF NOT EXISTS `tournament` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `tournament`;

-- --------------------------------------------------------

--
-- Структура таблицы `match_result`
--

DROP TABLE IF EXISTS `match_result`;
CREATE TABLE `match_result` (
  `id` int(11) NOT NULL,
  `match_id` int(11) DEFAULT NULL,
  `team_win_id` int(11) DEFAULT NULL,
  `result` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Дамп данных таблицы `match_result`
--

INSERT INTO `match_result` (`id`, `match_id`, `team_win_id`, `result`) VALUES
(208, 362, 25, '2:0'),
(209, 363, 29, '2:1'),
(210, 364, 20, '9:2'),
(211, 365, 33, '2:0'),
(212, 366, 31, '8:0'),
(213, 367, 21, '3:2'),
(214, 368, 32, '7:5'),
(215, 369, 24, '7:6');

-- --------------------------------------------------------

--
-- Структура таблицы `team`
--

DROP TABLE IF EXISTS `team`;
CREATE TABLE `team` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Дамп данных таблицы `team`
--

INSERT INTO `team` (`id`, `title`) VALUES
(18, '123'),
(19, 'ÐšÐ¾Ð¼Ð°Ð½Ð´Ð° 2'),
(20, 'ÐšÐ¾Ð¼Ð°Ð½Ð´Ð° 3'),
(21, 'ÐšÐ¾Ð¼Ð°Ð½Ð´Ð° 4'),
(22, 'ÐšÐ¾Ð¼Ð°Ð½Ð´Ð° 5'),
(23, 'ÐšÐ¾Ð¼Ð°Ð½Ð´Ð° 6'),
(24, 'ÐšÐ¾Ð¼Ð°Ð½Ð´Ð° 7'),
(25, 'ÐšÐ¾Ð¼Ð°Ð½Ð´Ð° 8'),
(26, 'ÐšÐ¾Ð¼Ð°Ð½Ð´Ð° 9'),
(27, 'ÐšÐ¾Ð¼Ð°Ð½Ð´Ð° 12'),
(28, 'ÐšÐ¾Ð¼Ð°Ð½Ð´Ð° 10'),
(29, 'ÐšÐ¾Ð¼Ð°Ð½Ð´Ð° 11'),
(30, 'ÐšÐ¾Ð¼Ð°Ð½Ð´Ð° 13'),
(31, 'ÐšÐ¾Ð¼Ð°Ð½Ð´Ð° 14'),
(32, 'ÐšÐ¾Ð¼Ð°Ð½Ð´Ð° 15'),
(33, 'ÐšÐ¾Ð¼Ð°Ð½Ð´Ð° 16');

-- --------------------------------------------------------

--
-- Структура таблицы `team_match`
--

DROP TABLE IF EXISTS `team_match`;
CREATE TABLE `team_match` (
  `id` int(11) NOT NULL,
  `team1` int(11) DEFAULT NULL,
  `team2` int(11) DEFAULT NULL,
  `group_name` varchar(10) DEFAULT NULL,
  `type_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Дамп данных таблицы `team_match`
--

INSERT INTO `team_match` (`id`, `team1`, `team2`, `group_name`, `type_id`) VALUES
(362, 25, 27, 'A', 1),
(363, 29, 23, 'A', 1),
(364, 20, 18, 'A', 1),
(365, 33, 30, 'A', 1),
(366, 31, 19, 'B', 1),
(367, 21, 22, 'B', 1),
(368, 32, 28, 'B', 1),
(369, 24, 26, 'B', 1),
(370, 20, 21, 'C', 2),
(371, 31, 25, 'C', 2),
(372, 32, 29, 'C', 2),
(373, 24, 33, 'C', 2);

-- --------------------------------------------------------

--
-- Структура таблицы `team_split`
--

DROP TABLE IF EXISTS `team_split`;
CREATE TABLE `team_split` (
  `id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  `group_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Дамп данных таблицы `team_split`
--

INSERT INTO `team_split` (`id`, `team_id`, `group_name`) VALUES
(177, 29, 'A'),
(178, 27, 'A'),
(179, 25, 'A'),
(180, 18, 'A'),
(181, 30, 'A'),
(182, 33, 'A'),
(183, 20, 'A'),
(184, 23, 'A'),
(185, 31, 'B'),
(186, 21, 'B'),
(187, 24, 'B'),
(188, 28, 'B'),
(189, 19, 'B'),
(190, 22, 'B'),
(191, 32, 'B'),
(192, 26, 'B');

-- --------------------------------------------------------

--
-- Структура таблицы `tournament_status`
--

DROP TABLE IF EXISTS `tournament_status`;
CREATE TABLE `tournament_status` (
  `id` int(11) NOT NULL,
  `status_id` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='На каком этапе сейчас турник';

--
-- Дамп данных таблицы `tournament_status`
--

INSERT INTO `tournament_status` (`id`, `status_id`) VALUES
(9, 5);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `match_result`
--
ALTER TABLE `match_result`
  ADD PRIMARY KEY (`id`),
  ADD KEY `match_result_team_id_fk` (`team_win_id`),
  ADD KEY `match_result_match_id_fk` (`match_id`);

--
-- Индексы таблицы `team`
--
ALTER TABLE `team`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `team_match`
--
ALTER TABLE `team_match`
  ADD PRIMARY KEY (`id`),
  ADD KEY `match_team_id_fk` (`team1`),
  ADD KEY `match_team_id_fk_2` (`team2`),
  ADD KEY `match_team_split_id_fk` (`group_name`);

--
-- Индексы таблицы `team_split`
--
ALTER TABLE `team_split`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `team_split_team_id_uindex` (`team_id`);

--
-- Индексы таблицы `tournament_status`
--
ALTER TABLE `tournament_status`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `match_result`
--
ALTER TABLE `match_result`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=216;
--
-- AUTO_INCREMENT для таблицы `team`
--
ALTER TABLE `team`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
--
-- AUTO_INCREMENT для таблицы `team_match`
--
ALTER TABLE `team_match`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=374;
--
-- AUTO_INCREMENT для таблицы `team_split`
--
ALTER TABLE `team_split`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=193;
--
-- AUTO_INCREMENT для таблицы `tournament_status`
--
ALTER TABLE `tournament_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `match_result`
--
ALTER TABLE `match_result`
  ADD CONSTRAINT `match_result_match_id_fk` FOREIGN KEY (`match_id`) REFERENCES `team_match` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `match_result_team_id_fk` FOREIGN KEY (`team_win_id`) REFERENCES `team` (`id`);

--
-- Ограничения внешнего ключа таблицы `team_match`
--
ALTER TABLE `team_match`
  ADD CONSTRAINT `match_team_id_fk` FOREIGN KEY (`team1`) REFERENCES `team` (`id`),
  ADD CONSTRAINT `match_team_id_fk_2` FOREIGN KEY (`team2`) REFERENCES `team` (`id`);

--
-- Ограничения внешнего ключа таблицы `team_split`
--
ALTER TABLE `team_split`
  ADD CONSTRAINT `team_split_team_id_fk` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
