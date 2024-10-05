-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: localhost
-- 生成日時: 2024 年 6 月 24 日 15:49
-- サーバのバージョン： 10.4.28-MariaDB
-- PHP のバージョン: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `zouuu_db`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `member_table`
--

CREATE TABLE `member_table` (
  `id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `birthDay` date NOT NULL,
  `tel` varchar(11) NOT NULL,
  `email` varchar(128) NOT NULL,
  `jusho1` text NOT NULL,
  `jusho2` text NOT NULL,
  `jusho3` text NOT NULL,
  `jusho4` text NOT NULL,
  `kanshin1` text NOT NULL,
  `kanshin2` text NOT NULL,
  `kanshin3` text NOT NULL,
  `food` text NOT NULL,
  `travel` text NOT NULL,
  `shumi` text NOT NULL,
  `action` text NOT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `member_table`
--

INSERT INTO `member_table` (`id`, `name`, `birthDay`, `tel`, `email`, `jusho1`, `jusho2`, `jusho3`, `jusho4`, `kanshin1`, `kanshin2`, `kanshin3`, `food`, `travel`, `shumi`, `action`, `datetime`) VALUES
(1, 'テスト太郎', '2024-06-15', '0', 'pp@pppp.jp', '北海道', 'aa', '千葉県', 'aa', 'あまり興味・関心はない', 'あまり興味・関心はない', 'とても興味・関心がある', '肉', '山・高原, 海, 川', '読書, 筋トレ, スポーツ, サーフィン', '仕事, ボランティア, 承継, 空き家, 教育', '2024-06-22 20:08:31'),
(2, 'テスト太郎', '2024-06-15', '0', 'pp@pppp.jp', '岩手県', 'aa', '岩手県', 'aa', 'やや興味・関心がある', 'とても興味・関心がある', 'やや興味・関心がある', '肉, 魚介', '山・高原, 海', '散歩, 読書', '仕事, ボランティア, 承継', '2024-06-22 20:09:11'),
(3, '大谷翔平', '2024-06-13', '0', 'uu@uuu.jp', '北海道', 'aa', '広島県', 'mm', 'あまり興味・関心はない', 'やや興味・関心がある', 'とても興味・関心がある', '魚介', '海', '読書', 'ボランティア', '2024-06-23 09:30:11'),
(4, 'zouuu', '2024-06-13', '1111111', 'xx@xx.jp', '京都府', '京都市', '群馬県', '前橋市', 'まったく興味・関心はない', 'やや興味・関心がある', 'やや興味・関心がある', '肉, 魚介', '山・高原, 海', '散歩, 飲食', 'ボランティア, 承継', '2024-06-23 09:35:36'),
(5, 'テスト小太郎', '2024-06-01', '0', 'aa@aaa.jp', '北海道', 'zz', '香川県', 'zzz', 'やや興味・関心がある', 'やや興味・関心がある', 'やや興味・関心がある', '魚介', '海', '映画', 'ボランティア', '2024-06-23 10:41:29'),
(6, 'テストこじろう', '2024-06-01', '000000000', 'testkojiro@test.jp', '青森県', 'cc', '福岡県', 'cc', 'あまり興味・関心はない', 'あまり興味・関心はない', 'やや興味・関心がある', '果物', '歴史', 'ドライブ', '承継', '2024-06-23 10:43:01'),
(7, 'ロロノア・ゾロ', '2024-06-07', '00000000', 'zozo@zoro.jp', '高知県', 'zz', '青森県', 'zz', 'あまり興味・関心はない', 'とても興味・関心がある', 'あまり興味・関心はない', '魚介', '海', 'ドライブ', 'ボランティア', '2024-06-23 12:48:52'),
(8, '加藤茶', '2024-06-01', '11111111', 'cha@cha.jp', '東京都', 'a', '福井県', 'a', 'あまり興味・関心はない', 'あまり興味・関心はない', 'とても興味・関心がある', '飲料', '歴史', '料理', '空き家', '2024-06-23 13:01:41'),
(9, '城島茂', '2024-05-31', '0000000', 'shigeru@tokio.jp', '神奈川県', 'a', '奈良県', 'a', 'とても興味・関心がある', 'とても興味・関心がある', 'とても興味・関心がある', '米', '島', '読書', '承継', '2024-06-23 22:05:04'),
(10, '国分太一', '2024-06-12', '0000000000', 'taichi@tokioba.jp', '岐阜県', 'a', '岡山県', 'a', 'とても興味・関心がある', 'とても興味・関心がある', 'とても興味・関心がある', '肉', '山・高原', '散歩', '仕事', '2024-06-23 22:06:35'),
(11, '松岡昌宏', '2024-06-27', '000000000', 'matsuoka@tokio.jp', '福島県', 'a', '岐阜県', 'a', 'とても興味・関心がある', 'とても興味・関心がある', 'とても興味・関心がある', '果物, 米', '山・高原, 海, 川, 島', '散歩, 読書, 飲食', '仕事, ボランティア, 承継, 空き家', '2024-06-24 06:55:07');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `member_table`
--
ALTER TABLE `member_table`
  ADD PRIMARY KEY (`id`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `member_table`
--
ALTER TABLE `member_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
