CREATE TABLE `api_keys`
(
    `task_id` int(11) NOT NULL,
    `api_key` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `checkpoint`
(
    `checkpoint_uid` char(36)     NOT NULL,
    `site_uid`       char(36)     NOT NULL,
    `title`          varchar(100) NOT NULL,
    `description`    varchar(500) NOT NULL,
    `distance`       decimal(10, 2) DEFAULT NULL,
    `opens`          datetime       DEFAULT NULL,
    `closing`        datetime       DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `club`
(
    `club_uid` char(36) NOT NULL,
    `acp_kod`  varchar(11)  DEFAULT NULL,
    `title`    varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `competitors`
(
    `competitor_uid` char(36)     NOT NULL,
    `user_name`      varchar(100) NOT NULL,
    `given_name`     varchar(100) DEFAULT NULL,
    `family_name`    varchar(100) DEFAULT NULL,
    `role_id`        int(11) NOT NULL,
    `password`       char(128)    DEFAULT NULL,
    `birthdate`      date         DEFAULT NULL,
    `gender`         int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `competitor_credential`
(
    `credential_uid`  char(36)     NOT NULL,
    `competitor_uid`  char(36)     NOT NULL,
    `participant_uid` char(36)     NOT NULL,
    `user_name`       varchar(100) NOT NULL,
    `password`        char(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `competitor_info`
(
    `uid`            varchar(36) NOT NULL,
    `competitor_uid` varchar(50) NOT NULL,
    `email`          varchar(50) NOT NULL,
    `phone`          varchar(50) NOT NULL,
    `adress`         varchar(200) DEFAULT NULL,
    `postal_code`    varchar(50)  DEFAULT NULL,
    `place`          varchar(100) DEFAULT NULL,
    `country`        varchar(100) DEFAULT NULL,
    `country_id`     int(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `countries`
(
    `country_id`      bigint(20) UNSIGNED NOT NULL,
    `country_name_en` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `country_name_sv` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
    `country_code`    varchar(15) COLLATE utf8mb4_unicode_ci  NOT NULL,
    `flag_url_svg`    varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
    `flag_url_png`    varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at`      timestamp NULL DEFAULT NULL,
    `updated_at`      timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `organizers`
(
    `organizer_id`   BIGINT UNSIGNED NOT NULL PRIMARY KEY, -- Primary key without auto-increment
    `name`           VARCHAR(255) NOT NULL,
    `active`         tinyint(1) DEFAULT 0,
    `confirmed`      tinyint(1) DEFAULT 0,
    `contact_person` VARCHAR(255) NOT NULL,
    `email`          VARCHAR(255) NOT NULL UNIQUE,         -- Unique email
    `phone`          VARCHAR(255) DEFAULT NULL,            -- Optional phone number
    `created_at`     TIMESTAMP NULL DEFAULT NULL,          -- Created timestamp
    `updated_at`     TIMESTAMP NULL DEFAULT NULL           -- Updated timestamp
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `event`
(
    `event_uid`   char(36)     NOT NULL,
    `title`       varchar(100) NOT NULL,
    `start_date`  date         NOT NULL,
    `end_date`    date         NOT NULL,
    `active`      tinyint(1) DEFAULT 0,
    `canceled`    tinyint(1) DEFAULT 0,
    `completed`   tinyint(1) DEFAULT 0,
    `description` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `event`
    ADD COLUMN `organizer_id` BIGINT UNSIGNED NOT NULL AFTER `event_uid`, -- Add organizer_id column
    ADD CONSTRAINT `fk_event_organizer`
    FOREIGN KEY (`organizer_id`)
    REFERENCES `organizers` (`organizer_id`)
    ON
DELETE
CASCADE ON
UPDATE CASCADE;

CREATE TABLE `event_tracks`
(
    `track_uid` char(36) NOT NULL,
    `event_uid` char(36) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `participant`
(
    `participant_uid`    char(36) NOT NULL,
    `track_uid`          char(36) NOT NULL,
    `competitor_uid`     char(36) NOT NULL,
    `startnumber`        int(10) UNSIGNED NOT NULL,
    `finished`           tinyint(1) DEFAULT 0,
    `acpkod`             int(10) UNSIGNED DEFAULT NULL,
    `club_uid`           char(36) DEFAULT NULL,
    `time`               char(36) DEFAULT NULL,
    `dns`                tinyint(1) DEFAULT 0,
    `dnf`                tinyint(1) DEFAULT 0,
    `started`            tinyint(1) DEFAULT 0,
    `brevenr`            int(10) UNSIGNED DEFAULT NULL,
    `register_date_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `participant_checkpoint`
(
    `participant_uid`    char(36) NOT NULL,
    `checkpoint_uid`     char(36) NOT NULL,
    `passed`             tinyint(1) DEFAULT 0,
    `passeded_date_time` datetime       DEFAULT NULL,
    `checkout_date_time` datetime       DEFAULT NULL,
    `checkin_date_time`  datetime       DEFAULT NULL,
    `volonteer_checkin`  tinyint(1) DEFAULT 0,
    `lat`                decimal(10, 8) DEFAULT NULL,
    `lng`                decimal(11, 8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `permissions`
(
    `perm_id`   int(10) UNSIGNED NOT NULL,
    `perm_desc` varchar(255) NOT NULL,
    `type_id`   int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `permission_type`
(
    `type_id`   int(10) UNSIGNED NOT NULL,
    `type_desc` varchar(255) NOT NULL,
    `type`      varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `roles`
(
    `role_id`   int(10) UNSIGNED NOT NULL,
    `role_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `roles_permissions`
(
    `role_id`  int(10) UNSIGNED NOT NULL,
    `perm_mod` varchar(15) NOT NULL,
    `perm_id`  int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `site`
(
    `site_uid`    char(36)     NOT NULL,
    `place`       varchar(100) NOT NULL,
    `adress`      varchar(100) NOT NULL,
    `description` varchar(500) NOT NULL,
    `lat`         decimal(10, 8) DEFAULT NULL,
    `lng`         decimal(11, 8) DEFAULT NULL,
    `location`    point          DEFAULT NULL,
    `picture`     varchar(100)   DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `track`
(
    `track_uid`        char(36)     NOT NULL,
    `title`            varchar(100)   DEFAULT NULL,
    `link`             varchar(100)   DEFAULT NULL,
    `heightdifference` int(10) UNSIGNED DEFAULT NULL,
    `event_uid`        char(36)     NOT NULL,
    `description`      varchar(500) NOT NULL,
    `distance`         decimal(10, 2) DEFAULT NULL,
    `start_date_time`  datetime       DEFAULT NULL,
    `active`           tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `track_checkpoint`
(
    `track_uid`      char(36) NOT NULL,
    `checkpoint_uid` char(36) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `users`
(
    `user_uid`    char(36)     NOT NULL,
    `user_name`   varchar(100) NOT NULL,
    `given_name`  varchar(100) DEFAULT NULL,
    `family_name` varchar(100) DEFAULT NULL,
    `password`    char(128)    DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `users`
    ADD COLUMN `organizer_id` BIGINT UNSIGNED;

CREATE TABLE `user_info`
(
    `uid`      varchar(36) NOT NULL,
    `user_uid` varchar(50) NOT NULL,
    `email`    varchar(50) NOT NULL,
    `phone`    varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user_role`
(
    `role_id`  int(10) UNSIGNED NOT NULL,
    `user_uid` varchar(36) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `v_dns_on_event_and_track`
(
    `startnumber`        int(10) unsigned,
    `started`            tinyint(1),
    `eventstart`         date,
    `eventend`           date,
    `competitor_uid`     char(36),
    `bana`               varchar(100),
    `finished`           tinyint(1),
    `track_uid`          char(36),
    `dns`                tinyint(1),
    `dnf`                tinyint(1),
    `event_uid`          char(36),
    `time`               char(36),
    `given_name`         varchar(100),
    `family_name`        varchar(100),
    `club`               varchar(200),
    `country`            varchar(100),
    `adress`             varchar(100),
    `passeded_date_time` datetime,
    `passed`             tinyint(1)
);
CREATE TABLE `v_partisipant_passed_checkpoints`
(
    `track_uid`          char(36),
    `participant_uid`    char(36),
    `site_uid`           char(36),
    `checkpoint_uid`     char(36),
    `opens`              datetime,
    `adress`             varchar(100),
    `startnumber`        int(10) unsigned,
    `given_name`         varchar(100),
    `family_name`        varchar(100),
    `passed`             tinyint(1),
    `passeded_date_time` datetime,
    `volonteer_checkin`  tinyint(1),
    `started`            tinyint(1),
    `dns`                tinyint(1),
    `dnf`                tinyint(1)
);
CREATE TABLE `v_partisipant_to_pass_checkpoint`
(
    `track_uid`          char(36),
    `participant_uid`    char(36),
    `site_uid`           char(36),
    `checkpoint_uid`     char(36),
    `opens`              datetime,
    `adress`             varchar(100),
    `startnumber`        int(10) unsigned,
    `given_name`         varchar(100),
    `family_name`        varchar(100),
    `passed`             tinyint(1),
    `passeded_date_time` datetime,
    `volonteer_checkin`  tinyint(1),
    `started`            tinyint(1),
    `dnf`                tinyint(1)
);
CREATE TABLE `v_race_statistic`
(
    `dnf`               decimal(25, 0),
    `dns`               decimal(25, 0),
    `completed`         decimal(25, 0),
    `countparticipants` bigint(21),
    `title`             varchar(100),
    `racestarts`        datetime,
    `track_uid`         char(36),
    `event_uid`         char(36),
    `eventstarts`       date,
    `eventends`         date
);
CREATE TABLE `v_result_for_event_and_track`
(
    `startnumber`        int(10) unsigned,
    `started`            tinyint(1),
    `eventstart`         date,
    `eventend`           date,
    `competitor_uid`     char(36),
    `bana`               varchar(100),
    `finished`           tinyint(1),
    `track_uid`          char(36),
    `dns`                tinyint(1),
    `dnf`                tinyint(1),
    `event_uid`          char(36),
    `time`               char(36),
    `given_name`         varchar(100),
    `family_name`        varchar(100),
    `club`               varchar(200),
    `country`            varchar(100),
    `adress`             varchar(100),
    `passeded_date_time` datetime,
    `passed`             tinyint(1)
);
CREATE TABLE `v_track_contestant_on_event_and_track`
(
    `startnumber`        int(10) unsigned,
    `participant_uid`    char(36),
    `started`            tinyint(1),
    `eventstart`         date,
    `eventend`           date,
    `competitor_uid`     char(36),
    `bana`               varchar(100),
    `finished`           tinyint(1),
    `track_uid`          char(36),
    `dns`                tinyint(1),
    `dnf`                tinyint(1),
    `event_uid`          char(36),
    `time`               char(36),
    `given_name`         varchar(100),
    `family_name`        varchar(100),
    `club`               varchar(200),
    `country`            varchar(100),
    `adress`             varchar(100),
    `passeded_date_time` datetime,
    `passed`             tinyint(1)
);
DROP TABLE IF EXISTS `v_dns_on_event_and_track`;


CREATE VIEW `v_dns_on_event_and_track` AS
SELECT DISTINCT `p`.`startnumber`         AS `startnumber`,
                `p`.`started`             AS `started`,
                `ev`.`start_date`         AS `eventstart`,
                `ev`.`end_date`           AS `eventend`,
                `p`.`competitor_uid`      AS `competitor_uid`,
                `t`.`title`               AS `bana`,
                `p`.`finished`            AS `finished`,
                `t`.`track_uid`           AS `track_uid`,
                `p`.`dns`                 AS `dns`,
                `p`.`dnf`                 AS `dnf`,
                `t`.`event_uid`           AS `event_uid`,
                `p`.`time`                AS `time`,
                `c`.`given_name`          AS `given_name`,
                `c`.`family_name`         AS `family_name`,
                `club`.`title`            AS `club`,
                `ci`.`country`            AS `country`,
                `s`.`adress`              AS `adress`,
                `pc`.`passeded_date_time` AS `passeded_date_time`,
                `pc`.`passed`             AS `passed`
FROM ((((((((`event` `ev` join `track` `t` on (`t`.`event_uid` = `ev`.`event_uid`)) join `participant` `p`
            on (`p`.`track_uid` = `t`.`track_uid`)) join `competitors` `c`
           on (`c`.`competitor_uid` = `p`.`competitor_uid`)) join `club`
          on (`club`.`club_uid` = `p`.`club_uid`)) join `competitor_info` `ci`
         on (`c`.`competitor_uid` = `ci`.`competitor_uid`)) join `participant_checkpoint` `pc`
        on (`pc`.`participant_uid` = `p`.`participant_uid`)) join `checkpoint` `cp`
       on (`cp`.`checkpoint_uid` = `pc`.`checkpoint_uid`)) join `site` `s`
      on (`s`.`site_uid` = `cp`.`site_uid` and `t`.`active` = 0 and `p`.`dns` = 1 and `p`.`started` =
                                                                                      0 and !(`pc`.`passeded_date_time` in (select `participant_checkpoint`.`passeded_date_time` from `participant_checkpoint` where `participant_checkpoint`.`passeded_date_time` = NULL))))
GROUP BY `c`.`given_name`;
DROP TABLE IF EXISTS `v_partisipant_passed_checkpoints`;

CREATE VIEW `v_partisipant_passed_checkpoints` AS
SELECT `tr`.`track_uid`            AS `track_uid`,
       `pach`.`participant_uid`    AS `participant_uid`,
       `sit`.`site_uid`            AS `site_uid`,
       `cpo`.`checkpoint_uid`      AS `checkpoint_uid`,
       `cpo`.`opens`               AS `opens`,
       `sit`.`adress`              AS `adress`,
       `a`.`startnumber`           AS `startnumber`,
       `cp`.`given_name`           AS `given_name`,
       `cp`.`family_name`          AS `family_name`,
       `pach`.`passed`             AS `passed`,
       `pach`.`passeded_date_time` AS `passeded_date_time`,
       `pach`.`volonteer_checkin`  AS `volonteer_checkin`,
       `a`.`started`               AS `started`,
       `a`.`dns`                   AS `dns`,
       `a`.`dnf`                   AS `dnf`
FROM (((((`participant` `a` join `competitors` `cp` on (`a`.`competitor_uid` = `cp`.`competitor_uid`)) join `track` `tr`
         on (`tr`.`track_uid` = `a`.`track_uid`)) join `participant_checkpoint` `pach`
        on (`pach`.`participant_uid` = `a`.`participant_uid`)) join `checkpoint` `cpo`
       on (`cpo`.`checkpoint_uid` = `pach`.`checkpoint_uid`)) join `site` `sit`
      on (`sit`.`site_uid` = `cpo`.`site_uid`))
WHERE `pach`.`passed` = 1
  AND `a`.`started` = 1;
DROP TABLE IF EXISTS `v_partisipant_to_pass_checkpoint`;

CREATE VIEW `v_partisipant_to_pass_checkpoint` AS
SELECT `tr`.`track_uid`            AS `track_uid`,
       `pach`.`participant_uid`    AS `participant_uid`,
       `sit`.`site_uid`            AS `site_uid`,
       `cpo`.`checkpoint_uid`      AS `checkpoint_uid`,
       `cpo`.`opens`               AS `opens`,
       `sit`.`adress`              AS `adress`,
       `a`.`startnumber`           AS `startnumber`,
       `cp`.`given_name`           AS `given_name`,
       `cp`.`family_name`          AS `family_name`,
       `pach`.`passed`             AS `passed`,
       `pach`.`passeded_date_time` AS `passeded_date_time`,
       `pach`.`volonteer_checkin`  AS `volonteer_checkin`,
       `a`.`started`               AS `started`,
       `a`.`dnf`                   AS `dnf`
FROM (((((`participant` `a` join `competitors` `cp` on (`a`.`competitor_uid` = `cp`.`competitor_uid`)) join `track` `tr`
         on (`tr`.`track_uid` = `a`.`track_uid`)) join `participant_checkpoint` `pach`
        on (`pach`.`participant_uid` = `a`.`participant_uid`)) join `checkpoint` `cpo`
       on (`cpo`.`checkpoint_uid` = `pach`.`checkpoint_uid`)) join `site` `sit`
      on (`sit`.`site_uid` = `cpo`.`site_uid`));
DROP TABLE IF EXISTS `v_race_statistic`;

CREATE VIEW `v_race_statistic` AS
SELECT sum(`p`.`dnf`)               AS `dnf`,
       sum(`p`.`dns`)               AS `dns`,
       sum(`p`.`finished`)          AS `completed`,
       count(`p`.`participant_uid`) AS `countparticipants`,
       `t`.`title`                  AS `title`,
       `t`.`start_date_time`        AS `racestarts`,
       `t`.`track_uid`              AS `track_uid`,
       `ev`.`event_uid`             AS `event_uid`,
       `ev`.`start_date`            AS `eventstarts`,
       `ev`.`end_date`              AS `eventends`
FROM ((`participant` `p` join `track` `t` on (`t`.`track_uid` = `p`.`track_uid`)) join `event` `ev`
      on (`ev`.`event_uid` = `t`.`event_uid`))
GROUP BY `t`.`title`
ORDER BY `t`.`title` ASC;
DROP TABLE IF EXISTS `v_result_for_event_and_track`;

CREATE VIEW `v_result_for_event_and_track` AS
SELECT `sa`.`startnumber`        AS `startnumber`,
       `sa`.`started`            AS `started`,
       `sa`.`eventstart`         AS `eventstart`,
       `sa`.`eventend`           AS `eventend`,
       `sa`.`competitor_uid`     AS `competitor_uid`,
       `sa`.`bana`               AS `bana`,
       `sa`.`finished`           AS `finished`,
       `sa`.`track_uid`          AS `track_uid`,
       `sa`.`dns`                AS `dns`,
       `sa`.`dnf`                AS `dnf`,
       `sa`.`event_uid`          AS `event_uid`,
       `sa`.`time`               AS `time`,
       `sa`.`given_name`         AS `given_name`,
       `sa`.`family_name`        AS `family_name`,
       `sa`.`club`               AS `club`,
       `sa`.`country`            AS `country`,
       `sa`.`adress`             AS `adress`,
       `sa`.`passeded_date_time` AS `passeded_date_time`,
       `sa`.`passed`             AS `passed`
FROM (select `p`.`startnumber`         AS `startnumber`,
             `p`.`started`             AS `started`,
             `ev`.`start_date`         AS `eventstart`,
             `ev`.`end_date`           AS `eventend`,
             `p`.`competitor_uid`      AS `competitor_uid`,
             `t`.`title`               AS `bana`,
             `p`.`finished`            AS `finished`,
             `t`.`track_uid`           AS `track_uid`,
             `p`.`dns`                 AS `dns`,
             `p`.`dnf`                 AS `dnf`,
             `t`.`event_uid`           AS `event_uid`,
             `p`.`time`                AS `time`,
             `c`.`given_name`          AS `given_name`,
             `c`.`family_name`         AS `family_name`,
             `club`.`title`            AS `club`,
             `ci`.`country`            AS `country`,
             `s`.`adress`              AS `adress`,
             `pc`.`passeded_date_time` AS `passeded_date_time`,
             `pc`.`passed`             AS `passed`
      from ((((((((`event` `ev` join `track` `t` on (`t`.`event_uid` = `ev`.`event_uid`)) join `participant` `p`
                  on (`p`.`track_uid` = `t`.`track_uid`)) join `competitors` `c`
                 on (`c`.`competitor_uid` = `p`.`competitor_uid`)) join `club`
                on (`club`.`club_uid` = `p`.`club_uid`)) join `competitor_info` `ci`
               on (`c`.`competitor_uid` = `ci`.`competitor_uid`)) join `participant_checkpoint` `pc`
              on (`pc`.`participant_uid` = `p`.`participant_uid`)) join `checkpoint` `cp`
             on (`cp`.`checkpoint_uid` = `pc`.`checkpoint_uid`)) join `site` `s`
            on (`s`.`site_uid` = `cp`.`site_uid` and `pc`.`passeded_date_time` = (select max(`d`.`passeded_date_time`)
                                                                                  from `participant_checkpoint` `d`
                                                                                  where `pc`.`passed` = 1
                                                                                    and `pc`.`participant_uid` = `d`.`participant_uid`) and
                `t`.`active` = 0))
      order by `p`.`finished` desc) AS `sa`;
DROP TABLE IF EXISTS `v_track_contestant_on_event_and_track`;

CREATE VIEW `v_track_contestant_on_event_and_track` AS
SELECT `sa`.`startnumber`        AS `startnumber`,
       `sa`.`participant_uid`    AS `participant_uid`,
       `sa`.`started`            AS `started`,
       `sa`.`eventstart`         AS `eventstart`,
       `sa`.`eventend`           AS `eventend`,
       `sa`.`competitor_uid`     AS `competitor_uid`,
       `sa`.`bana`               AS `bana`,
       `sa`.`finished`           AS `finished`,
       `sa`.`track_uid`          AS `track_uid`,
       `sa`.`dns`                AS `dns`,
       `sa`.`dnf`                AS `dnf`,
       `sa`.`event_uid`          AS `event_uid`,
       `sa`.`time`               AS `time`,
       `sa`.`given_name`         AS `given_name`,
       `sa`.`family_name`        AS `family_name`,
       `sa`.`club`               AS `club`,
       `sa`.`country`            AS `country`,
       `sa`.`adress`             AS `adress`,
       `sa`.`passeded_date_time` AS `passeded_date_time`,
       `sa`.`passed`             AS `passed`
FROM (select `p`.`startnumber`         AS `startnumber`,
             `pc`.`participant_uid`    AS `participant_uid`,
             `p`.`started`             AS `started`,
             `ev`.`start_date`         AS `eventstart`,
             `ev`.`end_date`           AS `eventend`,
             `p`.`competitor_uid`      AS `competitor_uid`,
             `t`.`title`               AS `bana`,
             `p`.`finished`            AS `finished`,
             `t`.`track_uid`           AS `track_uid`,
             `p`.`dns`                 AS `dns`,
             `p`.`dnf`                 AS `dnf`,
             `t`.`event_uid`           AS `event_uid`,
             `p`.`time`                AS `time`,
             `c`.`given_name`          AS `given_name`,
             `c`.`family_name`         AS `family_name`,
             `club`.`title`            AS `club`,
             `ci`.`country`            AS `country`,
             `s`.`adress`              AS `adress`,
             `pc`.`passeded_date_time` AS `passeded_date_time`,
             `pc`.`passed`             AS `passed`
      from ((((((((`event` `ev` join `track` `t` on (`t`.`event_uid` = `ev`.`event_uid`)) join `participant` `p`
                  on (`p`.`track_uid` = `t`.`track_uid`)) join `competitors` `c`
                 on (`c`.`competitor_uid` = `p`.`competitor_uid`)) join `club`
                on (`club`.`club_uid` = `p`.`club_uid`)) join `competitor_info` `ci`
               on (`c`.`competitor_uid` = `ci`.`competitor_uid`)) join `participant_checkpoint` `pc`
              on (`pc`.`participant_uid` = `p`.`participant_uid`)) join `checkpoint` `cp`
             on (`cp`.`checkpoint_uid` = `pc`.`checkpoint_uid`)) join `site` `s`
            on (`s`.`site_uid` = `cp`.`site_uid` and `pc`.`passeded_date_time` = (select max(`d`.`passeded_date_time`)
                                                                                  from `participant_checkpoint` `d`
                                                                                  where `pc`.`passed` = 1
                                                                                    and `pc`.`participant_uid` = `d`.`participant_uid`)))
      order by `p`.`finished` desc) AS `sa`;


ALTER TABLE `api_keys`
    ADD PRIMARY KEY (`task_id`);

ALTER TABLE `checkpoint`
    ADD PRIMARY KEY (`checkpoint_uid`),
  ADD KEY `site_uid` (`site_uid`);

ALTER TABLE `club`
    ADD PRIMARY KEY (`club_uid`);

ALTER TABLE `competitors`
    ADD PRIMARY KEY (`competitor_uid`);

ALTER TABLE `competitor_credential`
    ADD PRIMARY KEY (`credential_uid`, `participant_uid`, `competitor_uid`),
  ADD KEY `competitor_uid` (`competitor_uid`),
  ADD KEY `participant_uid` (`participant_uid`);

ALTER TABLE `competitor_info`
    ADD PRIMARY KEY (`uid`),
  ADD KEY `competitor_uid` (`competitor_uid`);

ALTER TABLE `event`
    ADD PRIMARY KEY (`event_uid`);

ALTER TABLE `event_tracks`
    ADD PRIMARY KEY (`event_uid`, `track_uid`),
  ADD KEY `track_uid` (`track_uid`);

ALTER TABLE `participant`
    ADD PRIMARY KEY (`participant_uid`, `track_uid`, `competitor_uid`),
  ADD KEY `track_uid` (`track_uid`),
  ADD KEY `competitor_uid` (`competitor_uid`);

ALTER TABLE `participant_checkpoint`
    ADD PRIMARY KEY (`participant_uid`, `checkpoint_uid`),
  ADD KEY `checkpoint_uid` (`checkpoint_uid`);

ALTER TABLE `permissions`
    ADD PRIMARY KEY (`perm_id`);

ALTER TABLE `permission_type`
    ADD PRIMARY KEY (`type_id`);

ALTER TABLE `roles`
    ADD PRIMARY KEY (`role_id`);

ALTER TABLE `roles_permissions`
    ADD KEY `role_id` (`role_id`),
  ADD KEY `perm_id` (`perm_id`);

ALTER TABLE `site`
    ADD PRIMARY KEY (`site_uid`);

ALTER TABLE `track`
    ADD PRIMARY KEY (`track_uid`),
  ADD KEY `event_uid` (`event_uid`);

ALTER TABLE `track_checkpoint`
    ADD PRIMARY KEY (`checkpoint_uid`, `track_uid`),
  ADD KEY `track_uid` (`track_uid`);

ALTER TABLE `users`
    ADD PRIMARY KEY (`user_uid`);

ALTER TABLE `user_info`
    ADD PRIMARY KEY (`user_uid`);

ALTER TABLE `user_role`
    ADD KEY `user_uid` (`user_uid`),
  ADD KEY `role_id` (`role_id`);


ALTER TABLE `api_keys`
    MODIFY `task_id` int (11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `permissions`
    MODIFY `perm_id` int (10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `permission_type`
    MODIFY `type_id` int (10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `roles`
    MODIFY `role_id` int (10) UNSIGNED NOT NULL AUTO_INCREMENT;


ALTER TABLE `checkpoint`
    ADD CONSTRAINT `checkpoint_ibfk_1` FOREIGN KEY (`site_uid`) REFERENCES `site` (`site_uid`);

ALTER TABLE `competitor_credential`
    ADD CONSTRAINT `competitor_credential_ibfk_1` FOREIGN KEY (`competitor_uid`) REFERENCES `competitors` (`competitor_uid`),
  ADD CONSTRAINT `competitor_credential_ibfk_2` FOREIGN KEY (`participant_uid`) REFERENCES `participant` (`participant_uid`);

ALTER TABLE `competitor_info`
    ADD CONSTRAINT `competitor_info_ibfk_1` FOREIGN KEY (`competitor_uid`) REFERENCES `competitors` (`competitor_uid`);

ALTER TABLE `event_tracks`
    ADD CONSTRAINT `event_tracks_ibfk_1` FOREIGN KEY (`track_uid`) REFERENCES `track` (`track_uid`),
  ADD CONSTRAINT `event_tracks_ibfk_2` FOREIGN KEY (`event_uid`) REFERENCES `event` (`event_uid`);

ALTER TABLE `participant`
    ADD CONSTRAINT `participant_ibfk_1` FOREIGN KEY (`track_uid`) REFERENCES `track` (`track_uid`),
  ADD CONSTRAINT `participant_ibfk_2` FOREIGN KEY (`competitor_uid`) REFERENCES `competitors` (`competitor_uid`);

ALTER TABLE `participant_checkpoint`
    ADD CONSTRAINT `participant_checkpoint_ibfk_1` FOREIGN KEY (`checkpoint_uid`) REFERENCES `checkpoint` (`checkpoint_uid`),
  ADD CONSTRAINT `participant_checkpoint_ibfk_2` FOREIGN KEY (`participant_uid`) REFERENCES `participant` (`participant_uid`);

ALTER TABLE `roles_permissions`
    ADD CONSTRAINT `roles_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`),
  ADD CONSTRAINT `roles_permissions_ibfk_2` FOREIGN KEY (`perm_id`) REFERENCES `permissions` (`perm_id`);

ALTER TABLE `track`
    ADD CONSTRAINT `track_ibfk_1` FOREIGN KEY (`event_uid`) REFERENCES `event` (`event_uid`);

ALTER TABLE `track_checkpoint`
    ADD CONSTRAINT `track_checkpoint_ibfk_1` FOREIGN KEY (`track_uid`) REFERENCES `track` (`track_uid`),
  ADD CONSTRAINT `track_checkpoint_ibfk_2` FOREIGN KEY (`checkpoint_uid`) REFERENCES `checkpoint` (`checkpoint_uid`);

ALTER TABLE `user_info`
    ADD CONSTRAINT `user_info_ibfk_1` FOREIGN KEY (`user_uid`) REFERENCES `users` (`user_uid`);

ALTER TABLE `user_role`
    ADD CONSTRAINT `user_role_ibfk_1` FOREIGN KEY (`user_uid`) REFERENCES `users` (`user_uid`),
  ADD CONSTRAINT `user_role_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);
COMMIT;

CREATE TABLE `acpreports`
(
    `report_uid`                      char(36) NOT NULL,
    `track_uid`                       char(36) NOT NULL,
    `organizer_id`                    BIGINT UNSIGNED NOT NULL,
    `ready_for_approval`              tinyint(1) DEFAULT 0,
    `marked_as_ready_for_approval_by` tinyint(1) DEFAULT 0,
    `approved`                        tinyint(1) DEFAULT 0,
    `approved_by`                     char(36),
    `created_at`                      TIMESTAMP NULL DEFAULT NULL,
    `updated_at`                      TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `acpreports`
    ADD PRIMARY KEY (`report_uid`);

ALTER TABLE `acpreports`
    ADD CONSTRAINT `fk_acpreports_organizer`
        FOREIGN KEY (`organizer_id`)
            REFERENCES `organizers` (`organizer_id`)
            ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `acpreports`
    ADD CONSTRAINT `fk_acpreports_users`
        FOREIGN KEY (`approved_by`)
            REFERENCES `users` (`user_uid`)
            ON DELETE CASCADE ON UPDATE CASCADE;


CREATE TABLE svg_files
(
    `id`       INT AUTO_INCREMENT PRIMARY KEY,
    `name`     VARCHAR(255),
    `organizer_id` BIGINT UNSIGNED NOT NULL,
    `svg_blob` LONGBLOB,
    `created_at`                      TIMESTAMP NULL DEFAULT NULL,
    `updated_at`                      TIMESTAMP NULL DEFAULT NULL
);

