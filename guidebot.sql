SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `locations` (
`id` int(11) NOT NULL,
`user_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
`title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
`address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
`latitude` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
`longitude` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `users` (
`id` int(11) NOT NULL,
`user_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
`translate` tinyint(1) NOT NULL DEFAULT '0',
`echo` tinyint(1) NOT NULL DEFAULT '0',
`place` tinyint(1) NOT NULL DEFAULT '0',
`weather` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `locations`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `user_id` (`user_id`);

ALTER TABLE `users`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `user_id` (`user_id`) USING BTREE;

ALTER TABLE `locations`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;