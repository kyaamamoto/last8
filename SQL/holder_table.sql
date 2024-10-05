-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: mysql635.db.sakura.ne.jp
-- 生成日時: 2024 年 7 月 17 日 20:39
-- サーバのバージョン： 5.7.40-log
-- PHP のバージョン: 8.2.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `zouuu_zouuu_db`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `holder_table`
--

CREATE TABLE `holder_table` (
  `id` int(12) NOT NULL,
  `name` varchar(64) NOT NULL,
  `lid` varchar(128) NOT NULL,
  `lpw` varchar(256) NOT NULL,
  `life_flg` int(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `holder_table`
--
ALTER TABLE `holder_table`
  ADD PRIMARY KEY (`id`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `holder_table`
--
ALTER TABLE `holder_table`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
