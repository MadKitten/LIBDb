ALTER TABLE `eventlog` MODIFY `action` ENUM ('Add','Delete','Download', 'Error', 'Login','Logout','Update') DEFAULT NULL COMMENT 'тип события';