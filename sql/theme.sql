-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- ホスト: myapp-db
-- 生成日時: 2021 年 3 月 15 日 02:21
-- サーバのバージョン： 10.5.4-MariaDB-1:10.5.4+maria~focal
-- PHP のバージョン: 7.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `theme`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `m_category`
--

CREATE TABLE `m_category` (
  `category_id` int(2) NOT NULL,
  `category_name` varchar(32) NOT NULL,
  `delete_flg` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- テーブルの構造 `m_theme`
--

CREATE TABLE `m_theme` (
  `id` int(10) NOT NULL,
  `theme_id` varchar(256) NOT NULL,
  `category_id` int(2) NOT NULL,
  `theme_name` varchar(256) NOT NULL,
  `image_name` varchar(256) NOT NULL,
  `copy_flg` tinyint(1) NOT NULL DEFAULT 0,
  `delete_flg` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- テーブルの構造 `m_user`
--

CREATE TABLE `m_user` (
  `id` int(10) NOT NULL,
  `user_name` varchar(64) NOT NULL,
  `login_id` varchar(256) NOT NULL,
  `password` varchar(64) NOT NULL,
  `delete_flg` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- テーブルの構造 `t_user_theme_rel`
--

CREATE TABLE `t_user_theme_rel` (
  `user_id` int(11) NOT NULL,
  `theme_id` varchar(256) NOT NULL,
  `delete_flg` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `m_category`
--
ALTER TABLE `m_category`
  ADD PRIMARY KEY (`category_id`);

--
-- テーブルのインデックス `m_theme`
--
ALTER TABLE `m_theme`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `m_user`
--
ALTER TABLE `m_user`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `t_user_theme_rel`
--
ALTER TABLE `t_user_theme_rel`
  ADD PRIMARY KEY (`user_id`,`theme_id`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `m_theme`
--
ALTER TABLE `m_theme`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- テーブルの AUTO_INCREMENT `m_user`
--
ALTER TABLE `m_user`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
