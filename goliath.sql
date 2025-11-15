-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Paź 12, 2025 at 01:10 PM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `goliath`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `cms_access`
--

CREATE TABLE `cms_access` (
  `access_id` int(11) NOT NULL,
  `access_name` varchar(200) DEFAULT NULL,
  `access_level` int(11) DEFAULT NULL,
  `access_description` text DEFAULT NULL,
  `access_position` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `cms_access`
--

INSERT INTO `cms_access` (`access_id`, `access_name`, `access_level`, `access_description`, `access_position`, `created_at`, `updated_at`) VALUES
(1, 'ADMINISTRATOR', 1, NULL, 1, '2024-02-02 23:50:06', NULL),
(2, 'PRACOWNIK ADMINISTRACJI', 2, NULL, 2, '2024-04-04 11:16:10', NULL);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `cms_analytics`
--

CREATE TABLE `cms_analytics` (
  `analytics_id` int(11) NOT NULL,
  `analytics_tag` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cms_analytics`
--

INSERT INTO `cms_analytics` (`analytics_id`, `analytics_tag`, `created_at`, `updated_at`) VALUES
(1, '', '2025-10-12 10:51:59', '2025-10-12 10:51:59');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `cms_blog`
--

CREATE TABLE `cms_blog` (
  `blog_id` int(11) NOT NULL,
  `blog_title` varchar(800) DEFAULT NULL,
  `blog_description` mediumtext DEFAULT NULL,
  `blog_tags` text DEFAULT NULL,
  `blog_lang` tinyint(4) DEFAULT NULL,
  `blog_cat_id` int(11) DEFAULT NULL,
  `blog_view_counter` int(11) DEFAULT NULL,
  `blog_display` enum('Nie','Tak') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `cms_blog_category`
--

CREATE TABLE `cms_blog_category` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(600) DEFAULT NULL,
  `category_description` text DEFAULT NULL,
  `category_url` varchar(100) DEFAULT NULL,
  `category_lang` tinyint(4) DEFAULT NULL,
  `category_display` enum('Nie','Tak') DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `cms_cookies`
--

CREATE TABLE `cms_cookies` (
  `cookies_id` int(11) NOT NULL,
  `cookies_txt` text DEFAULT NULL,
  `cookies_active` tinyint(4) DEFAULT NULL,
  `cookies_mode` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `cms_cookies`
--

INSERT INTO `cms_cookies` (`cookies_id`, `cookies_txt`, `cookies_active`, `cookies_mode`, `created_at`, `updated_at`) VALUES
(1, '<h3>Niniejsza strona korzysta z plików cookie</h3><p>W celu świadczenia usług na najwyższym poziomie w ramach naszej strony internetowej korzystamy z plików cookies. Pliki cookies umożliwiają nam zapewnienie prawidłowego działania naszej strony internetowej oraz realizację podstawowych jej funkcji, a po uzyskaniu Twojej zgody, pliki cookies są przez nas wykorzystywane do dokonywania pomiarów i analiz korzystania ze strony internetowej, a także do celów marketingowych.</p>', 1, 2, NULL, '2025-10-07 23:32:25');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `cms_form_contact`
--

CREATE TABLE `cms_form_contact` (
  `form_id` int(11) NOT NULL,
  `form_email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `form_email_alias` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `form_return_email_status` enum('Nie','Tak') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `form_return_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `cms_form_contact`
--

INSERT INTO `cms_form_contact` (`form_id`, `form_email`, `form_email_alias`, `form_return_email_status`, `form_return_message`, `created_at`, `updated_at`) VALUES
(1, 'biuro@maxsoft.pl', '', 'Tak', '<p>Dziękujemy za wiadomość, niedługo ktoś odpowie na Twoją wiadomość!.</p>', '2025-10-07 20:53:53', '2025-10-12 10:51:25');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `cms_lang`
--

CREATE TABLE `cms_lang` (
  `lang_id` int(11) NOT NULL,
  `lang_symbol` varchar(2) DEFAULT NULL,
  `lang_name` varchar(200) DEFAULT NULL,
  `lang_description` varchar(800) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cms_lang`
--

INSERT INTO `cms_lang` (`lang_id`, `lang_symbol`, `lang_name`, `lang_description`, `created_at`, `updated_at`) VALUES
(1, 'pl', 'Polski', 'Język Polski (UTF-8)', '2024-11-20 07:20:18', NULL),
(2, 'en', 'Angielski', 'Język Angielski (UTF-8)', '2024-11-20 07:20:43', NULL);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `cms_maintenance`
--

CREATE TABLE `cms_maintenance` (
  `maintenance_id` int(11) NOT NULL,
  `maintenance_txt` text DEFAULT NULL,
  `maintenance_ip` varchar(50) DEFAULT NULL,
  `maintenance_active` tinyint(4) DEFAULT NULL,
  `maintenance_mode` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `cms_maintenance`
--

INSERT INTO `cms_maintenance` (`maintenance_id`, `maintenance_txt`, `maintenance_ip`, `maintenance_active`, `maintenance_mode`, `created_at`, `updated_at`) VALUES
(1, '<h1 style=\"text-align:center;\">Przerwa Techniczna</h1><p style=\"text-align:center;\">Przepraszamy serwis chwilowo nieczynny. Prosimy o cierpliwość niebawem strona się pojawi.&nbsp;</p>', '83.21.27.48', 0, 0, NULL, '2025-10-12 10:51:44');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `cms_navigation`
--

CREATE TABLE `cms_navigation` (
  `id` int(10) UNSIGNED NOT NULL,
  `href` varchar(150) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `text` varchar(150) DEFAULT NULL,
  `target` varchar(10) DEFAULT NULL,
  `tooltip` varchar(150) DEFAULT NULL,
  `child_id` int(11) DEFAULT NULL,
  `position` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `cms_navigation`
--

INSERT INTO `cms_navigation` (`id`, `href`, `icon`, `text`, `target`, `tooltip`, `child_id`, `position`) VALUES
(1, '/start/', 'fa-solid fa-house', 'Start', '', 'Start', 0, 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `cms_navigation_cms`
--

CREATE TABLE `cms_navigation_cms` (
  `id` int(10) UNSIGNED NOT NULL,
  `href` varchar(150) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `text` varchar(150) DEFAULT NULL,
  `target` varchar(10) DEFAULT NULL,
  `tooltip` varchar(150) DEFAULT NULL,
  `child_id` int(11) DEFAULT NULL,
  `position` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `cms_navigation_cms`
--

INSERT INTO `cms_navigation_cms` (`id`, `href`, `icon`, `text`, `target`, `tooltip`, `child_id`, `position`) VALUES
(1, '/dashboard/', 'fa-solid fa-house', 'Dashboard', '', 'Dashboard', 0, 1),
(2, '/navigation/', 'fas fa-compass', 'Nawigacja', '', 'Nawigacja Strony', 0, 2),
(3, '/slider/', 'fa-regular fa-images', 'Slider', '', 'Slider', 0, 3),
(4, '/staticblocks/', 'fas fa-file-waveform', 'Bloki statyczne', '', 'Bloki statyczne', 0, 4),
(5, '', 'fa-solid fa-gear', 'Ustawienia', '', 'Ustawienia', 0, 5),
(6, '/contactform/', 'fa-regular fa-envelope', 'Formularz Kontaktowy', '', 'Formularz Kontaktowy', 5, 6),
(7, '/maintenance/', 'fas fa-wrench', 'Przerwa Techniczna', '', 'Przerwa Techniczna', 5, 7),
(8, '/googleanalytics/', 'fas fa-chart-line', 'Google Analytics', '', 'Google Analytics', 5, 8),
(9, '/cookies/', 'fa-solid fa-cookie-bite', 'Cookies', '', 'Cookies', 5, 9),
(10, '/navigationcms/', 'fas fa-compass', 'Nawigacja CMS', '', 'Nawigacja CMS', 5, 10),
(11, '', 'fa-solid fa-address-card', 'Administracja', '', 'Administracja', 0, 6),
(12, '/users/', 'fas fa-user', 'Użytkownicy', '', 'Użytkownicy', 11, 11),
(13, '/access/', 'fas fa-universal-access', 'Uprawnenia', '', 'Uprawnenia', 11, 12),
(14, '/servicepassword/', 'fa-solid fa-unlock-keyhole', 'Hasło serwisowe', '', 'Hasło serwisowe', 11, 13);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `cms_routes`
--

CREATE TABLE `cms_routes` (
  `id` int(11) NOT NULL,
  `url` varchar(300) DEFAULT NULL,
  `method` varchar(50) DEFAULT NULL,
  `controller_name` varchar(200) DEFAULT NULL,
  `action_name` varchar(100) DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL,
  `group_type` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cms_routes`
--

INSERT INTO `cms_routes` (`id`, `url`, `method`, `controller_name`, `action_name`, `name`, `group_type`, `is_active`) VALUES
(1, 'dashboard', 'get', 'Dashboard', 'index', 'dashboard_index', 'web', 1),
(2, 'users', 'get', 'Users', 'index', 'users_index', 'web', 1),
(3, 'users/edit/{id}', 'get', 'Users', 'edit', 'users_edit', 'web', 1),
(4, 'access', 'get', 'Access', 'index', 'access_index', 'web', 1),
(5, 'access/edit/{id}', 'get', 'Access', 'edit', 'access_edit', 'web', 1),
(6, 'navigationcms', 'get', 'NavigationCms', 'index', 'navigation_cms_index', 'web', 1),
(7, 'navigationcms/edit/{id}', 'get', 'NavigationCms', 'edit', 'navigation_cms_edit', 'web', 1),
(8, 'navigation', 'get', 'Navigation', 'index', 'navigation_index', 'web', 1),
(9, 'navigation/edit/{id}', 'get', 'Navigation', 'edit', 'navigation_edit', 'web', 1),
(10, 'contactform', 'get', 'ContactForm', 'index', 'contact_form_index', 'web', 1),
(11, 'maintenance', 'get', 'Maintenance', 'index', 'maintenance_index', 'web', 1),
(12, 'googleanalytics', 'get', 'GoogleAnalytics', 'index', 'google_analytics_index', 'web', 1),
(13, 'servicepassword', 'get', 'ServicePassword', 'index', 'service_password_index', 'web', 1),
(14, 'cookies', 'get', 'Cookies', 'index', 'cookies_index', 'web', 1),
(15, 'staticblocks', 'get', 'StaticBlocks', 'index', 'static_blocks_index', 'web', 1),
(16, 'staticblocks/edit/{id}', 'get', 'StaticBlocks', 'edit', 'static_blocks_edit', 'web', 1),
(17, 'slider', 'get', 'Slider', 'index', 'slider_index', 'web', 1),
(18, 'slider/edit/{id}', 'get', 'Slider', 'edit', 'slider_edit', 'web', 1),
(19, 'users/grid', 'get', 'Users', 'grid', 'api_users_grid', 'api', 1),
(20, 'users/add', 'post', 'Users', 'add', 'api_users_add', 'api', 1),
(21, 'users/update', 'post', 'Users', 'update', 'api_users_update', 'api', 1),
(22, 'users/inline', 'post', 'Users', 'inline', 'api_users_inline', 'api', 1),
(23, 'users/remove', 'post', 'Users', 'remove', 'api_users_remove', 'api', 1),
(24, 'users/store_password', 'post', 'Users', 'storePassword', 'api_users_store_password', 'api', 1),
(25, 'access/grid', 'get', 'Access', 'grid', 'api_access_grid', 'api', 1),
(26, 'access/add', 'post', 'Access', 'add', 'api_access_add', 'api', 1),
(27, 'access/update', 'post', 'Access', 'update', 'api_access_update', 'api', 1),
(28, 'access/remove', 'post', 'Access', 'remove', 'api_access_remove', 'api', 1),
(29, 'navigationcms/getjson', 'get', 'NavigationCms', 'getJson', 'api_navigation_cms_get_json', 'api', 1),
(30, 'navigationcms/store', 'post', 'NavigationCms', 'store', 'api_navigation_cms_store', 'api', 1),
(31, 'navigation/getjson', 'get', 'Navigation', 'getJson', 'api_navigation_get_json', 'api', 1),
(32, 'navigation/store', 'post', 'Navigation', 'store', 'api_navigation_store', 'api', 1),
(33, 'servicepassword/add', 'post', 'ServicePassword', 'add', 'api_service_password_add', 'api', 1),
(34, 'maintenance/update', 'post', 'Maintenance', 'update', 'api_maintenance_update', 'api', 1),
(35, 'googleanalytics/update', 'post', 'GoogleAnalytics', 'update', 'api_google_analytics_update', 'api', 1),
(36, 'cookies/update', 'post', 'Cookies', 'update', 'api_cookies_update', 'api', 1),
(37, 'contactform/update', 'post', 'ContactForm', 'update', 'api_contact_form_update', 'api', 1),
(38, 'staticblocks/grid', 'get', 'StaticBlocks', 'grid', 'api_static_blocks_grid', 'api', 1),
(39, 'staticblocks/add', 'post', 'StaticBlocks', 'add', 'api_static_blocks_add', 'api', 1),
(40, 'staticblocks/update', 'post', 'StaticBlocks', 'update', 'api_static_blocks_update', 'api', 1),
(41, 'staticblocks/inline', 'post', 'StaticBlocks', 'inline', 'api_static_blocks_inline', 'api', 1),
(42, 'staticblocks/remove', 'post', 'StaticBlocks', 'remove', 'api_static_blocks_remove', 'api', 1),
(43, 'slider/grid', 'get', 'Slider', 'grid', 'api_slider_grid', 'api', 1),
(44, 'slider/grid_files/{id}', 'get', 'Slider', 'gridFiles', 'api_slider_grid_files', 'api', 1),
(45, 'slider/add', 'post', 'Slider', 'add', 'api_slider_add', 'api', 1),
(46, 'slider/update', 'post', 'Slider', 'update', 'api_slider_update', 'api', 1),
(47, 'slider/inline', 'post', 'Slider', 'inline', 'api_slider_inline', 'api', 1),
(48, 'slider/remove', 'post', 'Slider', 'remove', 'api_slider_remove', 'api', 1),
(49, 'slider/uploadfiles/{id}', 'post', 'Slider', 'uploadFiles', 'api_slider_upload_files', 'api', 1),
(50, 'slider/remove_files', 'post', 'Slider', 'removeFiles', 'api_slider_remove_files', 'api', 1),
(51, 'slider/downloadfiles/{id}', 'get', 'Slider', 'downloadFiles', 'api_slider_download_files', 'api', 1),
(52, 'slider/order_files', 'post', 'Slider', 'orderFiles', 'api_slider_order_files', 'api', 1),
(53, 'slider/saveconfig', 'post', 'Slider', 'saveConfig', 'api_slider_save_config', 'api', 1),
(54, 'filesupload', 'post', 'Default', 'filesUpload', 'api_default_filesupload', 'api', 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `cms_sentence`
--

CREATE TABLE `cms_sentence` (
  `id` int(11) NOT NULL,
  `sentence` text NOT NULL,
  `name` varchar(300) NOT NULL,
  `position` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `cms_sentence`
--

INSERT INTO `cms_sentence` (`id`, `sentence`, `name`, `position`) VALUES
(1, 'Ci, co wygrywają - nigdy nie odpuszczają.  Ci, co odpuszczają - nigdy nie wygrywają...', 'Vince Lombardi', 0),
(2, 'Jeśli chcesz gdzieś dojść, najlepiej znajdź kogoś, kto już tam doszedł.', 'Robert Kiyosaki', 0),
(3, 'Wybierz pracę, którą kochasz, a nie będziesz musiał pracować nawet przez jeden dzień w swoim życiu.', 'Konfucjusz', 0),
(4, 'Nie narzekaj, że masz pod górę,  gdy zmierzasz na szczyt.', 'Unknown', 0),
(5, 'Charyzma wpływowych osób jest po prostu nabytą umiejętnością i, podobnie jak wszystkich innych umiejętności, można się jej nauczyć, a następnie ją udoskonalić ? jeśli mamy odpowiednie informacje i zapał do nauki.', 'A. Pease', 0),
(6, 'Sukces wydaje się być w dużej mierze kwestią wytrwania, gdy inni rezygnują.', 'W. Feather', 0),
(7, 'Liderem jest ten, kto widzi więcej niż inni, patrzy dalej niż inni i kto dostrzega rzeczy, zanim zobaczą je inni.', 'L. Eims', 0),
(8, 'Myślenie to najcięższa praca z możliwych i pewnie dlatego tak niewielu ją podejmuje.', 'Henry Ford', 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `cms_service_passwd`
--

CREATE TABLE `cms_service_passwd` (
  `passwd_id` int(11) NOT NULL,
  `passwd_hash` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `cms_service_passwd`
--

INSERT INTO `cms_service_passwd` (`passwd_id`, `passwd_hash`, `created_at`, `updated_at`) VALUES
(1, '$2y$10$JvelOeca2adW6Lnzeip7fOyzxa3hN6U/vbl.2jAsRW4MlUiEXkYSS', '2025-10-05 22:41:49', '2025-10-05 22:41:49');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `cms_slider`
--

CREATE TABLE `cms_slider` (
  `slider_id` int(11) NOT NULL,
  `slider_identifier` varchar(300) DEFAULT NULL,
  `slider_title` varchar(200) DEFAULT NULL,
  `slider_description` varchar(800) DEFAULT NULL,
  `slider_url` varchar(600) DEFAULT NULL,
  `slider_position` int(11) DEFAULT NULL,
  `slider_display` enum('Nie','Tak') DEFAULT NULL,
  `slider_lang` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cms_slider`
--

INSERT INTO `cms_slider` (`slider_id`, `slider_identifier`, `slider_title`, `slider_description`, `slider_url`, `slider_position`, `slider_display`, `slider_lang`, `created_at`, `updated_at`) VALUES
(11, 'test2', 'test2', '<p>test</p>', '#', 0, 'Tak', 1, '2025-10-10 18:18:54', '2025-10-12 10:47:04');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `cms_sliderconfig`
--

CREATE TABLE `cms_sliderconfig` (
  `id` int(11) NOT NULL,
  `slider_width` varchar(100) NOT NULL,
  `slider_height` varchar(100) NOT NULL,
  `slider_quality` smallint(6) NOT NULL,
  `slider_format` varchar(100) NOT NULL,
  `slider_main_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cms_sliderconfig`
--

INSERT INTO `cms_sliderconfig` (`id`, `slider_width`, `slider_height`, `slider_quality`, `slider_format`, `slider_main_id`, `created_at`, `updated_at`) VALUES
(1, '1920', '1080', 85, 'webp', 1, '2025-10-10 21:33:50', '2025-10-10 21:43:31');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `cms_slider_files`
--

CREATE TABLE `cms_slider_files` (
  `file_id` int(11) NOT NULL,
  `file_name` varchar(80) DEFAULT NULL,
  `file_type` varchar(80) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `file_main_id` int(11) DEFAULT NULL,
  `file_position` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `cms_static_blocks`
--

CREATE TABLE `cms_static_blocks` (
  `block_id` int(11) NOT NULL,
  `block_identifier` varchar(150) DEFAULT NULL,
  `block_group` varchar(100) DEFAULT NULL,
  `block_title` varchar(800) DEFAULT NULL,
  `block_description` longtext DEFAULT NULL,
  `block_lang` tinyint(4) DEFAULT NULL,
  `block_display` enum('Nie','Tak') DEFAULT NULL,
  `block_position` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `cms_users`
--

CREATE TABLE `cms_users` (
  `user_id` int(11) NOT NULL,
  `user_first_name` varchar(300) DEFAULT NULL,
  `user_last_name` varchar(300) DEFAULT NULL,
  `user_stand_name` varchar(300) DEFAULT NULL,
  `user_symbol` varchar(2) DEFAULT NULL,
  `user_description` text DEFAULT NULL,
  `user_email` varchar(80) DEFAULT NULL,
  `user_phone` varchar(80) DEFAULT NULL,
  `user_password` varchar(260) DEFAULT NULL,
  `user_level` tinyint(4) DEFAULT NULL,
  `user_active` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `cms_users`
--

INSERT INTO `cms_users` (`user_id`, `user_first_name`, `user_last_name`, `user_stand_name`, `user_symbol`, `user_description`, `user_email`, `user_phone`, `user_password`, `user_level`, `user_active`, `created_at`, `updated_at`) VALUES
(1, 'Konto', 'Techniczne', 'Admin', 'KT', 'Konto Techniczne', 'g.miskow@maxsoft.pl', '791821908', '$2y$10$TWATsM74VRmf8hdLeIGUZ.iJX2nz4lpvqcOBmfFiBZ0mnQx.XvgQO', 1, 1, '2024-01-31 00:05:41', '2025-10-12 10:53:16');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `cms_access`
--
ALTER TABLE `cms_access`
  ADD PRIMARY KEY (`access_id`),
  ADD KEY `position` (`access_position`);

--
-- Indeksy dla tabeli `cms_analytics`
--
ALTER TABLE `cms_analytics`
  ADD PRIMARY KEY (`analytics_id`);

--
-- Indeksy dla tabeli `cms_blog`
--
ALTER TABLE `cms_blog`
  ADD PRIMARY KEY (`blog_id`),
  ADD KEY `blog_cat_id` (`blog_cat_id`),
  ADD KEY `blog_lang` (`blog_lang`);

--
-- Indeksy dla tabeli `cms_blog_category`
--
ALTER TABLE `cms_blog_category`
  ADD PRIMARY KEY (`category_id`),
  ADD KEY `category_lang` (`category_lang`);

--
-- Indeksy dla tabeli `cms_cookies`
--
ALTER TABLE `cms_cookies`
  ADD PRIMARY KEY (`cookies_id`);

--
-- Indeksy dla tabeli `cms_form_contact`
--
ALTER TABLE `cms_form_contact`
  ADD PRIMARY KEY (`form_id`);

--
-- Indeksy dla tabeli `cms_lang`
--
ALTER TABLE `cms_lang`
  ADD PRIMARY KEY (`lang_id`),
  ADD KEY `lang_symbol` (`lang_symbol`);

--
-- Indeksy dla tabeli `cms_maintenance`
--
ALTER TABLE `cms_maintenance`
  ADD PRIMARY KEY (`maintenance_id`);

--
-- Indeksy dla tabeli `cms_navigation`
--
ALTER TABLE `cms_navigation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `child_id` (`child_id`),
  ADD KEY `position` (`position`);

--
-- Indeksy dla tabeli `cms_navigation_cms`
--
ALTER TABLE `cms_navigation_cms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `child_id` (`child_id`),
  ADD KEY `position` (`position`);

--
-- Indeksy dla tabeli `cms_routes`
--
ALTER TABLE `cms_routes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `url` (`url`),
  ADD KEY `controller_name` (`controller_name`);

--
-- Indeksy dla tabeli `cms_sentence`
--
ALTER TABLE `cms_sentence`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `cms_service_passwd`
--
ALTER TABLE `cms_service_passwd`
  ADD PRIMARY KEY (`passwd_id`),
  ADD KEY `passwd_hash` (`passwd_hash`);

--
-- Indeksy dla tabeli `cms_slider`
--
ALTER TABLE `cms_slider`
  ADD PRIMARY KEY (`slider_id`),
  ADD KEY `slider_identifier` (`slider_identifier`),
  ADD KEY `slider_lang` (`slider_lang`);

--
-- Indeksy dla tabeli `cms_sliderconfig`
--
ALTER TABLE `cms_sliderconfig`
  ADD PRIMARY KEY (`id`),
  ADD KEY `slider_main_id` (`slider_main_id`);

--
-- Indeksy dla tabeli `cms_slider_files`
--
ALTER TABLE `cms_slider_files`
  ADD PRIMARY KEY (`file_id`);

--
-- Indeksy dla tabeli `cms_static_blocks`
--
ALTER TABLE `cms_static_blocks`
  ADD PRIMARY KEY (`block_id`),
  ADD KEY `block_identifier` (`block_identifier`),
  ADD KEY `block_lang` (`block_lang`);

--
-- Indeksy dla tabeli `cms_users`
--
ALTER TABLE `cms_users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cms_access`
--
ALTER TABLE `cms_access`
  MODIFY `access_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `cms_analytics`
--
ALTER TABLE `cms_analytics`
  MODIFY `analytics_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cms_blog`
--
ALTER TABLE `cms_blog`
  MODIFY `blog_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cms_blog_category`
--
ALTER TABLE `cms_blog_category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cms_form_contact`
--
ALTER TABLE `cms_form_contact`
  MODIFY `form_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cms_lang`
--
ALTER TABLE `cms_lang`
  MODIFY `lang_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cms_maintenance`
--
ALTER TABLE `cms_maintenance`
  MODIFY `maintenance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cms_navigation`
--
ALTER TABLE `cms_navigation`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cms_navigation_cms`
--
ALTER TABLE `cms_navigation_cms`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `cms_routes`
--
ALTER TABLE `cms_routes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `cms_service_passwd`
--
ALTER TABLE `cms_service_passwd`
  MODIFY `passwd_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cms_slider`
--
ALTER TABLE `cms_slider`
  MODIFY `slider_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `cms_sliderconfig`
--
ALTER TABLE `cms_sliderconfig`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cms_slider_files`
--
ALTER TABLE `cms_slider_files`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `cms_static_blocks`
--
ALTER TABLE `cms_static_blocks`
  MODIFY `block_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cms_users`
--
ALTER TABLE `cms_users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
