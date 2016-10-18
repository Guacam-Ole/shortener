#Simple Angular / PHP - Url Shortener

## License:
CC-BY-NC 3.0 (the "NC" isn't very strict here. Just look into the [license-document](LICENSE) or contact me if you are unsure)

##Before you begin
###Origin
This is the public copy of the url shortener used for Hurz.Me / Fade.At
Because of license Issues (I don't own the rights to copy the artwork used) and security issues some things have been removed:

###Missing Contents
All Images have been removed. So you have to create your own Artwork if you want to have this fancy.

###Database
Before you can begin, you have to create a Database and four Tables to store the data. The following Snippet creates thos tables:
**Important** If you decide to create the tables manually, make sure to use a collation for the "token" columns that are *Case-Sensitive*. 

###SQL-Creation-Script:
````SQL

CREATE TABLE `calculated` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `target` varchar(1024) NOT NULL,
  `expire` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expire` (`expire`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `calculatedaccess` (
  `id` bigint(20) NOT NULL,
  `accessdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_calc` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `custom` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `token` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `target` varchar(1024) NOT NULL,
  `expire` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `expire` (`expire`),
  KEY `token_2` (`token`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='custom urls';

CREATE TABLE `customaccess` (
  `token` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `accessdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_custom` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `calculatedaccess`
  ADD CONSTRAINT `rts_calculated` FOREIGN KEY (`id`) REFERENCES `calculated` (`id`) ON DELETE CASCADE;

ALTER TABLE `customaccess`
  ADD CONSTRAINT `idx_custom` FOREIGN KEY (`token`) REFERENCES `custom` (`token`) ON DELETE CASCADE;

````

Some Explanation:
In [custom] all custom tokens (5 characters or more) are saved, [calculated] stores all calculated redirects. The [*access] - tables store all external access to the url.

###Configuration
Just add a "custom.php" into the php-folder containing the following config:

(todo)

##Watch the Project in Action
You can see what this project does on "http://fade.at",

##Additional Stats
As you can see statistics for any address by adding "/stats" to it, you can also see statistics about all custom or all calculated redirects.

To see those just enter [yourdomain]/calculated/stats or [yourdomain]/custom/stats

##Spam protection
This project uses a very simple JavaScript-Based Spam-protection. It is based on the thoughts of David Walsh, https://davidwalsh.name/wordpress-comment-spam (yes a popular journalist who does JavaScript. How awesome is that?) ;)

I used nearly the same technique on my wordpress-blogs and successfully blocked all spam. But be aware that while this is great to trick any Spam-*bot*, it can easily fail if someone decides to write a spam-mechanism especially for your site. 

