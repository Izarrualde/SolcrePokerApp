-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 30, 2019 at 11:25 AM
-- Server version: 5.6.43
-- PHP Version: 7.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lmsuy_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `awards`
--

CREATE TABLE `awards` (
  `id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `name` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `awards`
--

INSERT INTO `awards` (`id`, `created_at`, `name`, `class`, `description`) VALUES
(1, '2019-04-23 16:27:42', '100 horas', 'icon-award-4', 'Cien horas de juego.'),
(2, '2019-04-23 16:27:42', 'Invicto mas de diez sesiones', 'icon-award-5', 'Mas de diez sesiones sin perder.');

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE `currencies` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `symbol` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `currencies`
--

INSERT INTO `currencies` (`id`, `name`, `symbol`) VALUES
(1, 'Pesos Uruguayos', 'UYU'),
(2, 'Dolares', 'USD');

-- --------------------------------------------------------

--
-- Table structure for table `grupos`
--

CREATE TABLE `grupos` (
  `id` int(11) NOT NULL,
  `grupo` varchar(255) NOT NULL,
  `protegido` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `grupos`
--

INSERT INTO `grupos` (`id`, `grupo`, `protegido`) VALUES
(1, 'Usuarios', 0);

-- --------------------------------------------------------

--
-- Table structure for table `oauth_access_tokens`
--

CREATE TABLE `oauth_access_tokens` (
  `access_token` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `scope` varchar(2000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `oauth_access_tokens`
--

INSERT INTO `oauth_access_tokens` (`access_token`, `client_id`, `user_id`, `expires`, `scope`) VALUES
('003f2d2523a69703dfd457bc1e8993ab9e113afe', 'web', '12345678', '2019-04-29 19:20:46', NULL),
('02b1e36e0e462e7031bd1bb6d11fbd06d609767e', 'web', '12345678', '2019-04-25 13:38:49', NULL),
('07f3ccf782a2c7b2b729482004e7adb383e2ca7e', 'web', '47900671', '2019-04-23 18:50:26', NULL),
('0813eaedc524e160fdd15ffa6c822068abbacde5', 'web', '12345678', '2019-04-27 02:30:43', NULL),
('0e84b9e96ee1e2f516fac00d75b7793db162ea71', 'web', '12345678', '2019-04-26 07:49:55', NULL),
('0f3694fe50beb2244d81e8e715372f5767673d6b', 'web', '12345678', '2019-04-27 02:52:48', NULL),
('0fdd5e21a26a98756a5aee04eef72399bdb8ab95', 'web', '12345678', '2019-04-26 20:02:44', NULL),
('1078ee7d62349e6dcc4c8b658b04dd0c22cfdcbb', 'web', '12345678', '2019-04-26 22:50:07', NULL),
('129f5ceccbc192e322e67dbf51c9efd89792a78d', 'web', '12345678', '2019-04-30 10:33:52', NULL),
('15efb814dbf7333c1ed41a5af24cae9c44b55763', 'web', '12345678', '2019-04-26 21:49:45', NULL),
('167a84b1ab9ecef3b6a316db9f2bb28503740d22', 'web', '091222333', '2019-04-26 16:37:33', NULL),
('179a2f072bc2a0a1a8ed3b17c181b980c928156d', 'web', '12345678', '2019-04-29 19:30:26', NULL),
('19aeb5afa7b4751e349607e7fbeee6f335784f70', 'web', '12345678', '2019-04-25 14:42:50', NULL),
('1b9a4a626a3e2b409ca7ae7565fe6f0b9c4f0aa8', 'web', '12345678', '2019-04-27 02:59:46', NULL),
('1cd47f5b7a0d97a91c4a15742c30524e0fb6c23a', 'web', '12345678', '2019-04-24 01:27:47', NULL),
('21870d1903d90ded6ce5752106eedebe6902202b', 'web', '12345678', '2019-04-27 02:33:38', NULL),
('21b7514ce6a55c04828d3d4adc1f671b61987353', 'web', '091222333', '2019-04-26 16:19:54', NULL),
('24f3fc0013bf45271c5d5a60cbf089ad2b84d8c3', 'web', '12345678', '2019-04-26 20:33:34', NULL),
('2691623c39526bf5600befb7dfcc5a43d7b88d4b', 'web', '12345678', '2019-04-25 15:12:32', NULL),
('2cfbc65a7d153484fe72bfcfa495687ed13efe7a', 'web', '12345678', '2019-04-29 19:32:38', NULL),
('30117c1440f62c6ab93371f7afc485646bb4d14c', 'web', '12345678', '2019-04-25 14:31:33', NULL),
('31677e2929bdbbfcc4c0aa8136f8078c247fd3f2', 'web', '12345678', '2019-04-24 02:33:52', NULL),
('323834d19fc7347cb51622affbde1b8ac6b5e4c9', 'web', '12345678', '2019-04-26 02:06:10', NULL),
('32e1a434c54f9ab237bbab8a46cea1c71c717321', 'web', '12345678', '2019-04-27 03:11:25', NULL),
('336ef2edf5a0b0df67f1ebebc2dc80f0f870533d', 'web', '12345678', '2019-04-27 01:58:40', NULL),
('34168e67aceac08b0453fc3db3b1a6ff20c4d6b0', 'web', '12345678', '2019-04-27 03:16:37', NULL),
('37e4f406b1eb6e3175566f4dedaf3965ec5c1f3c', 'web', '12345678', '2019-04-30 06:17:32', NULL),
('38b7786f1890a783faccf560d8bed8b905518e5c', 'web', '12345678', '2019-04-27 02:31:49', NULL),
('3dbe3ac67e6d4e7aed8868a82fd8905719732139', 'web', '12345678', '2019-04-25 15:12:57', NULL),
('42f90bb780637700c4e7d658850418d639da267e', 'web', '091222333', '2019-04-26 16:11:07', NULL),
('43ab2469efe68f14d8b7e590232ef2f31e95712e', 'web', '47900671', '2019-04-24 01:22:32', NULL),
('454b0c3d17fc3e21fdc92d678d72892e60345c2a', 'web', '47900671', '2019-04-23 20:39:13', NULL),
('4595d3492787e8bcb40506e055d9ba0453721efc', 'web', '12345678', '2019-04-26 19:33:53', NULL),
('45d22fbb80a30ecb55debf7c6768651284e0f499', 'web', '12345678', '2019-04-26 07:39:50', NULL),
('46c1781bbea08b8b85335b1cb6a3f996ac3326ce', 'web', '12345678', '2019-04-24 02:33:54', NULL),
('4712b68cc6b7fdb47caf200547450adeb1b527a5', 'web', '12345678', '2019-04-27 01:45:15', NULL),
('47f1564174c9d0f47e32221cc4120edcf6ddbb93', 'web', '12345678', '2019-04-27 01:47:32', NULL),
('49258d52ef47a251d9e42a5ed4a9d4943193340b', 'web', '47900671', '2019-04-23 18:49:19', NULL),
('4b704432e713c1131c649e166c1d7e9d3c546f2c', 'web', '12345678', '2019-04-26 21:50:23', NULL),
('4c8f8544c3e4ed9fcfb7dc8c5241d9632b7af9cc', 'web', '1', '2019-04-30 21:21:38', NULL),
('54040d4e17cba9db4cd9200b05208e1c797ea741', 'web', '12345678', '2019-04-26 20:35:01', NULL),
('596f678c68e449df25cf32847d80d3ff99e898c9', 'web', '12345678', '2019-04-24 01:25:03', NULL),
('5d5aaa21a2e9268563e11fc5fe09305389eef718', 'web', '12345678', '2019-04-26 01:09:49', NULL),
('60b8fd43348c8cf97c6560981488240db4212418', 'web', '12345678', '2019-04-27 02:30:46', NULL),
('626dd5e3f6b2f680c816f685696cae09f7105292', 'web', '12345678', '2019-04-24 02:33:49', NULL),
('664e9a443da7669b80f6063a94fcbfcaeaa62e20', 'web', '12345678', '2019-04-24 02:15:21', NULL),
('68670c5c11d6231de5e6dc401ea273d4381ae594', 'web', '12345678', '2019-04-26 02:19:29', NULL),
('6a1fb583cb7f6689251de02c529ab4acb39a07cd', 'web', '12345678', '2019-04-27 01:56:43', NULL),
('6bc1bd80267dc013c4ffce8255651ea0b5e808bc', 'web', '47900671', '2019-04-23 18:59:47', NULL),
('6fd4ffa13a20937e94c95576b6d783a2e9216cd9', 'web', '12345678', '2019-04-24 02:15:17', NULL),
('71317d1469b429badaaeb03e0fab854b9dfb4c17', 'web', '12345678', '2019-04-22 23:13:36', NULL),
('721ad649bee567a6c7e504a699c78c90ed3d4f4a', 'web', '1', '2019-04-30 21:18:55', NULL),
('72d2d40fbbe443a2722a6bb362205ac9f85f3b4e', 'web', '12345678', '2019-04-26 03:36:22', NULL),
('73012c26722fda03eec3bcbbe0afe40887538a9b', 'web', '12345678', '2019-04-27 02:30:48', NULL),
('754efab03b3b8fe382a88fd938655c27d4c22f69', 'web', '12345678', '2019-04-24 02:33:55', NULL),
('7625a0ec974a552ae6b69317827ed460e068b992', 'web', '47900671', '2019-04-24 01:22:27', NULL),
('76dec7831b4eb1e4eaff36b0d6c6d3d21383c38b', 'web', '12345679', '2019-04-30 10:33:00', NULL),
('7863a9667f4a5220c6488924582bbdb95353ce50', 'web', '12345678', '2019-04-24 18:41:38', NULL),
('78a413ebba69dd043e520a633c867bb1873021ba', 'web', '12345678', '2019-04-23 17:18:02', NULL),
('7a1d8259e550f5222209f4f38033e2a1dbd4b1f2', 'web', '47900671', '2019-04-23 19:00:06', NULL),
('7af64532c56995bcd57eb33eb90a16726f5a7758', 'web', '12345678', '2019-04-26 03:08:43', NULL),
('7c644960e4ef7c6cbd7ca3a9299c5a913878bea2', 'web', '12345678', '2019-04-25 12:54:42', NULL),
('7c7b6935580b344469c28ac4735050dd624c8618', 'web', '12345678', '2019-04-26 16:38:01', NULL),
('7ea9a7c985885c491b1bde7a2d20e4c362e2d470', 'web', '12345678', '2019-04-26 22:49:27', NULL),
('7fb7688f4e25989afcf968f71167564f30df76e6', 'web', '12345678', '2019-04-27 02:31:47', NULL),
('80b6cdeb6e95d0de7d463bfe8d3376c2cbb39010', 'web', '12345678', '2019-04-26 03:10:55', NULL),
('8126296ec5dbb22ea07d82079e7b0e3271ac8426', 'web', '12345678', '2019-04-26 02:18:58', NULL),
('81ceb68afee072a390c47dcfd2aa08485fc5b0ee', 'web', '091222333', '2019-04-26 16:14:48', NULL),
('8210bca822f291ab0de88f4e70e37bcc71a96166', 'web', '12345678', '2019-04-25 13:13:15', NULL),
('86d51723126bba38cd6709e26a34beb6a7f349de', 'web', '12345678', '2019-04-23 17:10:56', NULL),
('88ce509e283efe664254541a7bc08858e62c3d91', 'web', '12345678', '2019-04-24 02:22:39', NULL),
('894a161076b6f4acb882d5cf56f2a153ae5fbea5', 'web', '091222333', '2019-04-26 16:10:43', NULL),
('8db8f3951cceaaf95f6d7d888514e819e6390903', 'web', '12345678', '2019-04-24 02:22:41', NULL),
('8ecda2c48c42e69e3dfe058848860692c82392b3', 'web', '12345678', '2019-04-26 20:06:11', NULL),
('9632686edbb492efe461f1b508cccd2fc775d3eb', 'web', '12345678', '2019-04-24 02:37:52', NULL),
('9a5ab003b30f6c5a6c6c1de85770a1258d0407e6', 'web', '12345678', '2019-04-30 21:23:31', NULL),
('9b3d5845ccca8e26cbe35e1cfc5799bfe88d817e', 'web', '1', '2019-04-30 21:18:36', NULL),
('9b515391cf6cb56094625cc1e431b5f1f39c22bd', 'web', '12345678', '2019-04-26 15:22:54', NULL),
('9f3ba1bc4c7e2dfaa9396d7f990197352d3d00be', 'web', '12345678', '2019-04-24 02:15:20', NULL),
('9f847b691986f2e5a6f0ef0e32fa4808d6a57f29', 'web', '12345678', '2019-04-26 20:01:49', NULL),
('a6ec7ac929dff481d2b822d93e6ee0ed188e178b', 'web', '12345678', '2019-04-28 06:45:31', NULL),
('a759fdf889a07a1b3af9c4882f21c15608b20922', 'web', '12345678', '2019-04-24 02:19:30', NULL),
('a831fceeae3b3c0b29f6ba341e52b3d724c937c2', 'web', '12345678', '2019-04-30 00:55:27', NULL),
('adaa130f1d8ce59773de5a2223627101affd8069', 'web', '12345678', '2019-04-26 01:29:20', NULL),
('ade2c68651a64b57e247c7f6f24c37bf6f1b3070', 'web', '12345678', '2019-04-24 02:38:07', NULL),
('b20bc5b38b7d232a1868a2525faa7fba8d213a42', 'web', '12345678', '2019-04-25 12:45:34', NULL),
('b2ced3cbf951e7eb41c6b1ec7dbec91dfef431ab', 'web', '12345678', '2019-04-27 01:46:25', NULL),
('b3d26ea321a1c121599d689b2e489c8828122ced', 'web', '12345678', '2019-04-27 02:49:07', NULL),
('b404eac8765ce8f57cdfd82ae11ceaf4ffdeee1f', 'web', '12345678', '2019-04-26 20:03:47', NULL),
('b7f92a731945bee2b02404598bc01c5235427c51', 'web', '12345678', '2019-04-24 02:26:44', NULL),
('bda31bb573f60d8464f2dfb47329ec280782a7d4', 'web', '12345678', '2019-04-26 20:23:39', NULL),
('c2d9e2ce533fe5de0691e70fcf84c012441c23f1', 'web', '12345678', '2019-04-29 19:34:23', NULL),
('c31d619a9dcd292f95d2ddd632fe08d40c479bcd', 'web', '12345678', '2019-04-25 09:04:09', NULL),
('c3e3070e7107b1eca9080bbc38b00a4f0660bf37', 'web', '12345678', '2019-04-30 10:32:07', NULL),
('c4d44a5faa6fcc2358b81d27e8d6c2cebee16512', 'web', '12345678', '2019-04-25 15:13:09', NULL),
('c612cfbd8ce18425c73cd5c7e7e037df4e7630ef', 'web', '12345678', '2019-04-24 01:45:52', NULL),
('c86100ec14c9bd463074ab3e3bd01c349f3f5d1c', 'web', '1', '2019-04-30 21:18:56', NULL),
('c89cca1aa6452dba5388c48884251db1d47c357b', 'web', '12345678', '2019-04-23 18:57:48', NULL),
('c9e5e608c69e58845d8bad20ed2da4a3435c7d5f', 'web', '12345678', '2019-04-27 02:31:54', NULL),
('ccfbbadea96b4227e7d378e9a88ba25ce8916acf', 'web', '12345678', '2019-04-29 05:16:57', NULL),
('d4fb23b675e6e34888e9d503b61d1f83e42e3f81', 'web', '12345678', '2019-04-24 02:19:32', NULL),
('d789bf35a4aea0dd7201b37d33d48db5d648bb93', 'web', '12345678', '2019-04-24 02:06:41', NULL),
('d7e2c388dc08b5e1fccf2abbf419500858133e4c', 'web', '12345678', '2019-04-24 02:26:04', NULL),
('d855345c3896a31f7de6a7caf11b286334d2db9f', 'web', '12345678', '2019-04-26 03:30:44', NULL),
('daf075965d03fa53968067a1a75a6b10da1035b9', 'web', '12345678', '2019-04-23 17:00:05', NULL),
('dbb3746cc8773f2ff81678dab2e75c38d4775d7e', 'web', '12345678', '2019-04-27 02:30:56', NULL),
('dc99a90973d1a20ec8d79f0d9cdcf8dbfd1106d0', 'web', '12345678', '2019-04-24 02:19:31', NULL),
('df3864d3099d20fb27069904fb7aad15e2abc998', 'web', '12345678', '2019-04-27 03:18:39', NULL),
('dfd9bfa933bb062d902736fae064e3a0abe4c377', 'web', '12345678', '2019-04-25 21:36:33', NULL),
('e0c6e1e5147bfd09ee668e805a78671517688d6d', 'web', '091222333', '2019-04-26 15:56:35', NULL),
('e31e1310d230b60c224dc890d7d85a4a11e791fc', 'web', '12345678', '2019-04-26 16:41:22', NULL),
('e76ea594b549d900c7441eefd5ca075e37370017', 'web', '12345678', '2019-04-22 23:35:09', NULL),
('e86cc680c82493711735e75a9d05f66c16cfc50f', 'web', '12345678', '2019-04-26 20:04:08', NULL),
('ed48f5e05fb127e6acfc0157c1f7a3208a55ebff', 'web', '12345678', '2019-04-26 07:38:32', NULL),
('ee785800f5d2801e6a5da40268b12140f23dca39', 'web', '12345678', '2019-04-24 02:15:22', NULL),
('eee8410826239dba32a103f5c627719a1818c6c9', 'web', '12345678', '2019-04-24 02:37:56', NULL),
('ef8e1652c195f39311a46cc5735055b165c2bfda', 'web', '12345678', '2019-04-26 16:19:25', NULL),
('f31ef2978a90e2745b6d33b17db090952ea96e55', 'web', '12345678', '2019-04-24 01:45:42', NULL),
('f347be76a80d1046887126362a0c5fa3bbebfeaf', 'web', '12345678', '2019-04-27 02:32:05', NULL),
('f48f4fb8ef04a4e11d29dab9ae9019a84ceae95d', 'web', '12345678', '2019-04-24 02:19:45', NULL),
('f70bb75f7e52f56cfa5f31a5cea70ff8c96569df', 'web', '091222333', '2019-04-26 16:36:53', NULL),
('fd067eaf23bb63aa31d75732d1b3a2a95ec4979d', 'web', '12345678', '2019-04-24 02:33:51', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `oauth_authorization_codes`
--

CREATE TABLE `oauth_authorization_codes` (
  `authorization_code` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `redirect_uri` varchar(2000) DEFAULT NULL,
  `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `scope` varchar(2000) DEFAULT NULL,
  `id_token` varchar(2000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_clients`
--

CREATE TABLE `oauth_clients` (
  `client_id` varchar(80) NOT NULL,
  `client_secret` varchar(80) DEFAULT NULL,
  `redirect_uri` varchar(2000) DEFAULT NULL,
  `grant_types` varchar(80) DEFAULT NULL,
  `scope` varchar(2000) DEFAULT NULL,
  `user_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `oauth_clients`
--

INSERT INTO `oauth_clients` (`client_id`, `client_secret`, `redirect_uri`, `grant_types`, `scope`, `user_id`) VALUES
('manager', NULL, NULL, NULL, NULL, NULL),
('web', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `oauth_jwt`
--

CREATE TABLE `oauth_jwt` (
  `client_id` varchar(80) NOT NULL,
  `subject` varchar(80) DEFAULT NULL,
  `public_key` varchar(2000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_refresh_tokens`
--

CREATE TABLE `oauth_refresh_tokens` (
  `refresh_token` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `scope` varchar(2000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `oauth_refresh_tokens`
--

INSERT INTO `oauth_refresh_tokens` (`refresh_token`, `client_id`, `user_id`, `expires`, `scope`) VALUES
('00c3d2a94b2ac57bdf376f9510a76ba925e84929', 'web', '12345678', '2019-05-07 22:33:49', NULL),
('0b9948214b1818c1f1cd081d5b4ace2526842740', 'web', '12345678', '2019-05-10 16:01:49', NULL),
('0bbbecdfe2c5c0aba686a4f67a90a51e53a0f7fa', 'web', '12345678', '2019-05-07 22:22:39', NULL),
('0d624f90284dfdef67323ce68be9873f2cac67ed', 'web', '12345678', '2019-05-12 02:45:31', NULL),
('106376765c4c5cb39a9490d1944008b65c36b37e', 'web', '091222333', '2019-05-10 11:56:35', NULL),
('10a7d9018ffb71fb35ded8b93242ed7ba389ce8d', 'web', '12345678', '2019-05-10 22:30:46', NULL),
('125257733c23e51d7f422a3623323563b1ebd9b6', 'web', '12345678', '2019-05-07 14:57:48', NULL),
('136ab11d2e8f2a854c6f1fef63a77079366b407a', 'web', '12345678', '2019-05-07 22:19:45', NULL),
('1419c0b902ae7b765d034be09bdb6d8ffdcd8cfb', 'web', '12345678', '2019-05-10 12:38:01', NULL),
('158059288963e88d9e66ca39d1974e5c05d6c6db', 'web', '1', '2019-05-14 17:18:36', NULL),
('17edb50a19ca7208d7a7b2742383b576f0b5fdc8', 'web', '12345678', '2019-05-09 22:19:29', NULL),
('1abc561502111fb56b82746a0d5657eddba04752', 'web', '12345678', '2019-05-07 22:19:31', NULL),
('1c5d31174060b716ff96a64cf4b44e4d6e63ddf9', 'web', '12345678', '2019-05-10 22:30:43', NULL),
('1d5b7f58c32896c3661f2768d615e53c53375377', 'web', '12345678', '2019-05-10 17:50:23', NULL),
('1ed1f0a551273e2b5c915e80094a1b239b8fc7b3', 'web', '12345678', '2019-05-10 21:46:25', NULL),
('212540ce76c8dbb16c89ed96dca2773d9d04f677', 'web', '12345678', '2019-05-10 23:11:25', NULL),
('215bcfa00b317f6afc825a9be78e872075f2b0c7', 'web', '12345678', '2019-05-10 17:49:45', NULL),
('22f0184ba3b7fe630ddfe77703a981afbbeb8795', 'web', '12345678', '2019-05-10 16:02:44', NULL),
('242c8bbbd4ec81ef286f08a906cf03e5527b243a', 'web', '12345678', '2019-05-09 08:54:42', NULL),
('25f6cfaeb38d76e5f353e74eb1cf7662a08e7171', 'web', '12345678', '2019-05-06 19:13:36', NULL),
('2d725cb15dde8bbdc5c932df48b1161bf1f362e5', 'web', '12345678', '2019-05-07 22:33:51', NULL),
('2daa058078a64a47a0302f42184c764feb3b8fb7', 'web', '12345678', '2019-05-09 22:18:58', NULL),
('2dad73527da01b88af67c98b8890ac7270fc0102', 'web', '12345678', '2019-05-10 21:47:32', NULL),
('3833c0cb7c8a4ebcbe4f8b8671a5ee347459651e', 'web', '47900671', '2019-05-07 16:39:13', NULL),
('3bc113533dd04f50efbd94059029cd7f1d5a25d0', 'web', '12345678', '2019-05-10 21:45:15', NULL),
('3d4486248864a367f22ee1f9906d6a173295dd13', 'web', '12345678', '2019-05-09 08:45:34', NULL),
('408c2057ff74b1aea6993b5d3c6aedc370642604', 'web', '12345678', '2019-05-07 22:19:30', NULL),
('4290c526bf60e8706e725bb40d540d3b42ffeafb', 'web', '12345678', '2019-05-10 16:04:08', NULL),
('43051770fe52283f576e6a75a906d0c6bb67a215', 'web', '47900671', '2019-05-07 14:49:19', NULL),
('45db209435ad810f2c78d6107e96e31c0b56d7ca', 'web', '12345678', '2019-05-09 22:06:10', NULL),
('46f29bc184b156cae42dd3801266dcf85a05c3e8', 'web', '12345678', '2019-05-10 16:06:11', NULL),
('474b390960970cb8b0dbae229893e81431ecb8f6', 'web', '12345678', '2019-05-10 16:35:01', NULL),
('49248cc5a8577e75fdae4d3b3b27f8aafd5f3b5f', 'web', '47900671', '2019-05-07 21:22:32', NULL),
('4a3871544e1664b21a708629d03717593659632a', 'web', '12345678', '2019-05-09 21:29:20', NULL),
('4aa79ce2c0e711e4b0baa158b0ff7cac504c3fe9', 'web', '12345678', '2019-05-10 18:50:07', NULL),
('4ffc675cf3a961faf71660bacd0b1ac3c081264f', 'web', '12345678', '2019-05-07 22:06:41', NULL),
('507f225868cf928cf678940513a37b70659fd6bc', 'web', '091222333', '2019-05-10 12:14:48', NULL),
('55574069936afb976be6ea136d46c86f4ac528c4', 'web', '12345678', '2019-05-09 05:04:09', NULL),
('581698e0b112c2a2005155492d64aeb197737e58', 'web', '47900671', '2019-05-07 15:00:06', NULL),
('59940a084d607d8236c6948d0f9ab6805cea196a', 'web', '12345678', '2019-05-07 22:37:52', NULL),
('5a4affd3f1946be35c61d43cf4043e5f7ef0774b', 'web', '47900671', '2019-05-07 21:22:27', NULL),
('5d513237f4a4dd30ff5320d7ad86489a9c535c47', 'web', '12345678', '2019-05-10 22:59:46', NULL),
('5f2119aa37ef759ac9341fdd2319dd88f62d59aa', 'web', '12345678', '2019-05-07 21:45:42', NULL),
('5f5fad91d3bbb2cab642e09582e7b62fad6e57ea', 'web', '12345678', '2019-05-09 11:13:09', NULL),
('6084a2e3ba18b1a6ee3a287faed3f17ae4a1aa85', 'web', '12345678', '2019-05-10 23:16:37', NULL),
('631ce2e6a4398cf7b18d7d3fe2564e7db920ab98', 'web', '12345678', '2019-05-10 03:49:55', NULL),
('6331c8057755fad57e4ee00749d2151b18ee179e', 'web', '12345678', '2019-05-07 22:22:41', NULL),
('646ddc0e1df68d3c0a7dc0003c2717e4511103fe', 'web', '12345678', '2019-05-10 16:33:34', NULL),
('68fdeb0b2eb342dcabfb90e0fb6ac426ac032daf', 'web', '12345678', '2019-05-10 22:33:38', NULL),
('6c50ae6922409b65880e99288c366ac63bfe9aba', 'web', '12345678', '2019-05-14 06:32:07', NULL),
('6eec58248ef8a97712eac8c370e5b5d6701caf14', 'web', '12345678', '2019-05-07 22:15:20', NULL),
('715f243bab7addc033cbfcc037b6fc96455f78f6', 'web', '12345678', '2019-05-14 06:33:52', NULL),
('71650332796be07466c6384b8d4a9944d98c38bd', 'web', '12345678', '2019-05-07 21:45:52', NULL),
('72aeb879783f44c18501157a27bafac1325deacd', 'web', '1', '2019-05-14 17:18:56', NULL),
('74234e0393c3e3d709562a569278b0ed8a9d8a25', 'web', '12345678', '2019-05-07 22:37:56', NULL),
('75f0c66b25321951b582c9c0f4a8d5c2a205be5c', 'web', '1', '2019-05-14 17:18:55', NULL),
('764cf8bf72913485a99e8de3b75ba8cdfeea0e8e', 'web', '12345678', '2019-05-07 13:00:05', NULL),
('7894f6d779318d947a77e7d982f644140df13fbf', 'web', '091222333', '2019-05-10 12:36:53', NULL),
('790f962204387a3fbf2bb7a66b3fd66c3a9f5e1e', 'web', '1', '2019-05-14 17:21:39', NULL),
('79444d493cac17465720729fc0947b036e9c7b75', 'web', '12345678', '2019-05-10 15:33:53', NULL),
('7e43153548b24c18da093f8fdc12db57ff028bff', 'web', '12345678', '2019-05-07 22:33:55', NULL),
('7e5de57ba4ea198445c53f4a2f33cb7bd3af81e5', 'web', '12345678', '2019-05-08 14:41:38', NULL),
('803625799b5f888564d0c9cda664fb2af88cf21a', 'web', '12345678', '2019-05-09 09:13:15', NULL),
('860cecbf138333bd430cefb5ccad10963fe8624c', 'web', '12345678', '2019-05-10 16:03:47', NULL),
('869c7c8400b1e1cdf5db33d422cc02e6eac72331', 'web', '12345678', '2019-05-09 23:30:44', NULL),
('88a494ee48d8e9a867d8f6ec4559ca3134be6977', 'web', '12345678', '2019-05-09 17:36:33', NULL),
('8979e1807c549e034e26f5c1d0c49e2a782c67d8', 'web', '12345678', '2019-05-07 22:38:07', NULL),
('8d1b2a604c37582c7ec6e8ac78bbb2fb182586f7', 'web', '12345678', '2019-05-07 13:10:56', NULL),
('8e6b6b1cca33e82131c677825e01aa4364d9cf8f', 'web', '12345678', '2019-05-09 21:09:49', NULL),
('90e5c6581e5c6b105a79f8273690cfe543a59155', 'web', '12345678', '2019-05-07 22:26:44', NULL),
('9415492143705ecd62d54865265e8d50c09fd210', 'web', '47900671', '2019-05-07 14:59:47', NULL),
('94a66779844d5fde74c4157d68ec33d5c6264b9c', 'web', '12345678', '2019-05-10 22:31:49', NULL),
('96abc0eda579f0e480096bfcad1062d5ae961e8d', 'web', '12345678', '2019-05-07 22:26:04', NULL),
('98a948139f03e5fc390851f12ad75849945c46d8', 'web', '12345678', '2019-05-10 12:41:22', NULL),
('9dac83c0331f20f45ff408a96244858eab93996e', 'web', '12345678', '2019-05-07 22:33:52', NULL),
('9dc7779e179981f09cd26891f2bfcabdbb874631', 'web', '12345678', '2019-05-10 22:52:48', NULL),
('9ef52da295fc4b35db41195d39c75220e73e9c9f', 'web', '091222333', '2019-05-10 12:11:07', NULL),
('a00dc27153e289f8f2e5a128ed50f890b1ccf30e', 'web', '12345678', '2019-05-06 19:35:09', NULL),
('a07f82d2e50574b8f06435e4d094b0fb7b093778', 'web', '12345678', '2019-05-10 22:30:48', NULL),
('a11d9abb68c2d8be0fc5acd7910d733e3ada7a71', 'web', '12345678', '2019-05-10 21:58:40', NULL),
('a1f47fdd7db706f00c26817c43a7b80a0e6a2476', 'web', '12345678', '2019-05-10 12:19:25', NULL),
('a37dfaac9bfa23471e7fbd680ccf69c558585913', 'web', '12345678', '2019-05-09 11:12:57', NULL),
('a8cec7287ba44bdff2375db462af544fb21c1727', 'web', '47900671', '2019-05-07 14:50:26', NULL),
('acc864e9b7b78bfba1f6c21ec95681975d448a5b', 'web', '12345678', '2019-05-10 23:18:39', NULL),
('ad29fb7768002af875d00a894002173e4ac53b98', 'web', '091222333', '2019-05-10 12:19:54', NULL),
('ae45d10bb58223e2b3757b5610cfff9b0574dabf', 'web', '12345678', '2019-05-10 22:30:56', NULL),
('b4914e011eae2171dde66f0d4f65195cd7dd94ad', 'web', '12345678', '2019-05-10 16:23:39', NULL),
('b5a4000166b2fb8971d17690aa3bb34ac649a47a', 'web', '091222333', '2019-05-10 12:37:33', NULL),
('b728a0d75bf94219273baf962da45945a12d4ffa', 'web', '12345678', '2019-05-07 22:15:22', NULL),
('bcd1316f1a2198ee5e02385e0feb5bae77b11589', 'web', '12345679', '2019-05-14 06:33:00', NULL),
('be168ab6ae668be1e133acbf37561d81c795b99b', 'web', '12345678', '2019-05-09 10:31:33', NULL),
('beab5e5cf4e48ffe5f7f409d514aacd54b3937ac', 'web', '12345678', '2019-05-07 21:25:03', NULL),
('c59f521fc07fa422a0299cab817fe0bc36d34197', 'web', '091222333', '2019-05-10 12:10:43', NULL),
('cf3f9e54bcd26cd37f70ea8b80bb954f001ae3bc', 'web', '12345678', '2019-05-07 21:27:47', NULL),
('d07b1783f20e5cb60444f7ba637f25532abde1c6', 'web', '12345678', '2019-05-09 11:12:32', NULL),
('d30bdf0a377263a9b123830409cbef6701c733a0', 'web', '12345678', '2019-05-09 23:10:55', NULL),
('d8c938c7d56ccb681c352557654a6389ed8f1b15', 'web', '12345678', '2019-05-10 18:49:27', NULL),
('dc0f91c34d941e7992972337c662e0872e1b3019', 'web', '12345678', '2019-05-09 10:42:50', NULL),
('dfdab892bd82afe7c760c2b9f666593302c46ed8', 'web', '12345678', '2019-05-10 22:32:05', NULL),
('e00ad2be349d860566a07cf49c0aa93ed544e777', 'web', '12345678', '2019-05-07 22:19:32', NULL),
('e6dc13d715b0c793aa03cbb9362b551b05ccb1d5', 'web', '12345678', '2019-05-09 23:36:22', NULL),
('e70525d4bd634b2f4f53fd9c35f75a22c8916e65', 'web', '12345678', '2019-05-13 15:20:46', NULL),
('edcfb483b6476b3c5d4e48814a041a76e9c1af13', 'web', '12345678', '2019-05-10 22:31:47', NULL),
('eeaa68ba4b0e7c309697d6999b442eb7064514ce', 'web', '12345678', '2019-05-07 22:15:21', NULL),
('eefb2a12d25ac2e7b287b343fd429e6eb32a539e', 'web', '12345678', '2019-05-07 13:18:02', NULL),
('efe7154383d8bd5962fa925609f315eb6d0c4cae', 'web', '12345678', '2019-05-09 23:08:43', NULL),
('f13f43d1a2c2b478c9c76ce0181cc6afe2980243', 'web', '12345678', '2019-05-10 22:31:54', NULL),
('f7efef749ff963da9f1975959ee4e9f1a6b5875d', 'web', '12345678', '2019-05-07 22:15:17', NULL),
('f8877fcbf9b0e1b955b762e7df5466f897793f81', 'web', '12345678', '2019-05-07 22:33:54', NULL),
('fa5ffbfd753fda09dc2def7db79ce7229d910412', 'web', '12345678', '2019-05-10 22:49:07', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `oauth_scopes`
--

CREATE TABLE `oauth_scopes` (
  `type` varchar(255) NOT NULL DEFAULT 'supported',
  `scope` varchar(2000) DEFAULT NULL,
  `client_id` varchar(80) DEFAULT NULL,
  `is_default` smallint(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_users`
--

CREATE TABLE `oauth_users` (
  `username` varchar(255) NOT NULL,
  `password` varchar(2000) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `permisos`
--

CREATE TABLE `permisos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `permisos`
--

INSERT INTO `permisos` (`id`, `nombre`, `descripcion`) VALUES
(1, 'usuarios', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `permisos_grupos`
--

CREATE TABLE `permisos_grupos` (
  `id` int(11) NOT NULL,
  `id_grupo` int(11) NOT NULL,
  `id_permiso` int(11) NOT NULL,
  `d` tinyint(4) DEFAULT NULL,
  `r` tinyint(4) DEFAULT NULL,
  `w` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `permisos_grupos`
--

INSERT INTO `permisos_grupos` (`id`, `id_grupo`, `id_permiso`, `d`, `r`, `w`) VALUES
(1, 1, 1, 0, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `permisos_usuarios`
--

CREATE TABLE `permisos_usuarios` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_permiso` int(11) NOT NULL,
  `d` tinyint(4) DEFAULT NULL,
  `r` tinyint(4) DEFAULT NULL,
  `w` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL,
  `created_at` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  `title` varchar(255) NOT NULL,
  `description` text,
  `count_of_seats` smallint(6) NOT NULL DEFAULT '9',
  `start_at` datetime NOT NULL,
  `real_start_at` datetime DEFAULT NULL,
  `end_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `created_at`, `title`, `description`, `count_of_seats`, `start_at`, `real_start_at`, `end_at`) VALUES
(1, '2019-04-28 00:00:00', 'Mesa de prueba', 'Mesa de prueba', 3, '2019-04-29 10:00:00', '2019-04-29 11:00:00', '2019-04-29 12:00:00'),
(2, '2019-04-28 00:00:00', 'Mesa de prueba 2', 'Mesa de prueba 2', 7, '2019-04-28 10:00:00', '2019-04-28 11:00:00', '2019-04-28 12:00:00'),
(3, '2019-04-27 00:00:00', 'Mesa de prueba 3', 'Mesa de prueba 3', 5, '2019-04-30 10:00:00', '2019-04-30 11:00:00', '2019-04-30 12:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `sessions_users`
--

CREATE TABLE `sessions_users` (
  `id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `points` int(11) NOT NULL,
  `cashout` int(11) DEFAULT NULL,
  `start_at` datetime DEFAULT NULL,
  `end_at` datetime DEFAULT NULL,
  `is_approved` tinyint(1) NOT NULL,
  `session_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sessions_users`
--

INSERT INTO `sessions_users` (`id`, `created_at`, `points`, `cashout`, `start_at`, `end_at`, `is_approved`, `session_id`, `user_id`) VALUES
(1, '2019-04-29 00:00:00', 452, 950000, '2019-04-29 11:10:00', '2019-04-29 11:40:00', 1, 1, 1),
(2, '2019-04-29 00:00:00', 897, 150000, '2019-04-28 11:10:00', '2019-04-28 11:40:00', 1, 2, 1),
(3, '2019-04-30 00:00:00', 100, 200000, '2019-04-30 11:10:00', '2019-04-30 11:40:00', 1, 3, 1),
(5, '2019-04-30 00:00:00', 200, 200000, '2019-04-30 11:41:00', '2019-04-30 11:46:00', 1, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `session_buyins`
--

CREATE TABLE `session_buyins` (
  `id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `amount_of_cash_money` int(11) NOT NULL,
  `amount_of_credit_money` int(11) NOT NULL,
  `approved` tinyint(1) NOT NULL,
  `session_user_id` int(11) NOT NULL,
  `currency_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `session_buyins`
--

INSERT INTO `session_buyins` (`id`, `created_at`, `amount_of_cash_money`, `amount_of_credit_money`, `approved`, `session_user_id`, `currency_id`) VALUES
(1, '2019-04-29 19:02:35', 100, 200, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `session_service_tips`
--

CREATE TABLE `session_service_tips` (
  `id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `service_tip` int(11) NOT NULL,
  `session_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Dumping data for table `session_service_tips`
--

INSERT INTO `session_service_tips` (`id`, `created_at`, `service_tip`, `session_id`) VALUES
(1, '2019-04-29 00:00:00', 900, 1),
(2, '2019-04-29 00:00:00', 50, 2),
(3, '2019-04-29 00:00:00', 100, 3);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `created_date` datetime NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `cashin` int(11) NOT NULL DEFAULT '0',
  `points` int(11) NOT NULL DEFAULT '0',
  `hours` int(11) NOT NULL DEFAULT '0',
  `sessions` int(11) NOT NULL DEFAULT '0',
  `results` decimal(10,0) NOT NULL DEFAULT '0',
  `multiplier` decimal(10,0) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `avatar_hashed_filename` varchar(255) DEFAULT NULL,
  `avatar_visible_filename` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `created_date`, `username`, `password`, `name`, `last_name`, `email`, `cashin`, `points`, `hours`, `sessions`, `results`, `multiplier`, `is_active`, `avatar_hashed_filename`, `avatar_visible_filename`) VALUES
(1, '2019-04-22 19:07:07', '12345678', '$2a$10$COniMnHQV9h7RKybhKSGmOD4dDYLUOd0FRyPKjaUdZ8/VYEsljprK', 'Matías', 'Fuster', 'matias.fuster@solcre.com', 0, 10000, 10, 100, '0', '0', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_awards`
--

CREATE TABLE `users_awards` (
  `user_id` int(11) NOT NULL,
  `award_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_awards`
--

INSERT INTO `users_awards` (`user_id`, `award_id`) VALUES
(1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `usuarios_pertenece`
--

CREATE TABLE `usuarios_pertenece` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_grupo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `usuarios_pertenece`
--

INSERT INTO `usuarios_pertenece` (`id`, `id_usuario`, `id_grupo`) VALUES
(1, 1, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `awards`
--
ALTER TABLE `awards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `grupos`
--
ALTER TABLE `grupos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oauth_access_tokens`
--
ALTER TABLE `oauth_access_tokens`
  ADD PRIMARY KEY (`access_token`);

--
-- Indexes for table `oauth_authorization_codes`
--
ALTER TABLE `oauth_authorization_codes`
  ADD PRIMARY KEY (`authorization_code`);

--
-- Indexes for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  ADD PRIMARY KEY (`client_id`);

--
-- Indexes for table `oauth_jwt`
--
ALTER TABLE `oauth_jwt`
  ADD PRIMARY KEY (`client_id`);

--
-- Indexes for table `oauth_refresh_tokens`
--
ALTER TABLE `oauth_refresh_tokens`
  ADD PRIMARY KEY (`refresh_token`);

--
-- Indexes for table `oauth_users`
--
ALTER TABLE `oauth_users`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indexes for table `permisos_grupos`
--
ALTER TABLE `permisos_grupos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `is_grupo` (`id_grupo`,`id_permiso`),
  ADD KEY `id_grupo` (`id_grupo`),
  ADD KEY `id_permiso` (`id_permiso`);

--
-- Indexes for table `permisos_usuarios`
--
ALTER TABLE `permisos_usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_usuario` (`id_usuario`,`id_permiso`),
  ADD KEY `id_usuario_2` (`id_usuario`),
  ADD KEY `id_permiso` (`id_permiso`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions_users`
--
ALTER TABLE `sessions_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `session_id` (`session_id`);

--
-- Indexes for table `session_buyins`
--
ALTER TABLE `session_buyins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `session_user_id_on_session_buyins` (`session_user_id`),
  ADD KEY `currency_id_on_session_buyings` (`currency_id`);

--
-- Indexes for table `session_service_tips`
--
ALTER TABLE `session_service_tips`
  ADD PRIMARY KEY (`id`),
  ADD KEY `session_id` (`session_id`) USING BTREE;

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_awards`
--
ALTER TABLE `users_awards`
  ADD KEY `user_id` (`user_id`),
  ADD KEY `award_id` (`award_id`);

--
-- Indexes for table `usuarios_pertenece`
--
ALTER TABLE `usuarios_pertenece`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fila_unica` (`id_usuario`,`id_grupo`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_grupo` (`id_grupo`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `awards`
--
ALTER TABLE `awards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `grupos`
--
ALTER TABLE `grupos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `permisos`
--
ALTER TABLE `permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `permisos_grupos`
--
ALTER TABLE `permisos_grupos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `permisos_usuarios`
--
ALTER TABLE `permisos_usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sessions_users`
--
ALTER TABLE `sessions_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `session_buyins`
--
ALTER TABLE `session_buyins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `session_service_tips`
--
ALTER TABLE `session_service_tips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `usuarios_pertenece`
--
ALTER TABLE `usuarios_pertenece`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `permisos_grupos`
--
ALTER TABLE `permisos_grupos`
  ADD CONSTRAINT `permisos_grupos_ibfk_1` FOREIGN KEY (`id_grupo`) REFERENCES `grupos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `permisos_grupos_ibfk_2` FOREIGN KEY (`id_permiso`) REFERENCES `permisos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `permisos_usuarios`
--
ALTER TABLE `permisos_usuarios`
  ADD CONSTRAINT `permisos_usuarios_ibfk_2` FOREIGN KEY (`id_permiso`) REFERENCES `permisos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `permisos_usuarios_ibfk_3` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sessions_users`
--
ALTER TABLE `sessions_users`
  ADD CONSTRAINT `sessions_users_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sessions_users_ibfk_3` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `session_buyins`
--
ALTER TABLE `session_buyins`
  ADD CONSTRAINT `currency_id_on_session_buyings` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `session_user_id_on_session_buyins` FOREIGN KEY (`session_user_id`) REFERENCES `sessions_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `session_service_tips`
--
ALTER TABLE `session_service_tips`
  ADD CONSTRAINT `session_id_on_session_service_tips` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users_awards`
--
ALTER TABLE `users_awards`
  ADD CONSTRAINT `users_awards_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_awards_ibfk_4` FOREIGN KEY (`award_id`) REFERENCES `awards` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `usuarios_pertenece`
--
ALTER TABLE `usuarios_pertenece`
  ADD CONSTRAINT `usuarios_pertenece_ibfk_2` FOREIGN KEY (`id_grupo`) REFERENCES `grupos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `usuarios_pertenece_ibfk_3` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
