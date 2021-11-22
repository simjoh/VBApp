-- ALTER DATABASE vasterbottenbrevet_se CHARACTER SET='utf8'  COLLATE='utf8_bin';
-- -- create database --
-- CREATE DATABASE vasterbottenbrevet_se CHARACTER SET = 'utf8mb4';
ALTER DATABASE vasterbottenbrevet_se CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Api key
CREATE TABLE IF NOT EXISTS api_keys (
    task_id INT AUTO_INCREMENT PRIMARY KEY,
    api_key varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;;

-- user and competiotors
CREATE TABLE  users (
    user_uid char(36) NOT NULL,
    user_name varchar(100)  NOT NULL,
    given_name varchar(100),
    family_name varchar(100),
    password char(128),
    PRIMARY KEY (user_uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE competitors (
    competitor_uid char(36) NOT NULL,
    user_name varchar(100) NOT NULL,
    given_name varchar(100),
    family_name varchar(100),
    role_id int(11) NOT NULL,
    password char(128),
    PRIMARY KEY (competitor_uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- READ UPDATE
CREATE TABLE permission_type (
  type_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  type_desc varchar(255) NOT NULL,
  type varchar(20),
  PRIMARY KEY (type_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Permissions
CREATE TABLE permissions (
  perm_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  perm_desc varchar(255) NOT NULL,
  type_id  INTEGER UNSIGNED  NOT NULL,
  PRIMARY KEY (perm_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE roles (
  role_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  role_name varchar(255) NOT NULL,
  PRIMARY KEY (role_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE roles_permissions (
  role_id INTEGER UNSIGNED NOT NULL,
  perm_mod varchar(15) NOT NULL,
  perm_id INTEGER UNSIGNED NOT NULL,
  FOREIGN KEY (role_id) REFERENCES roles(role_id),
  FOREIGN KEY (perm_id) REFERENCES permissions(perm_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE user_role (
  role_id INTEGER UNSIGNED NOT NULL,
  user_uid varchar(36) NOT NULL,
  FOREIGN KEY (user_uid) REFERENCES users(user_uid),
  FOREIGN KEY (role_id) REFERENCES roles(role_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

----- Adress och kontaktuppgifter ----------------------------------------------
-- För användare i systemet ----------------------------------------------------
CREATE TABLE user_info (
  uid varchar(36) NOT NULL,
  user_uid varchar (50) NOT NULL,
  email varchar (50) NOT NULL,
  phone varchar (50) NOT NULL,
  PRIMARY KEY (user_uid),
  FOREIGN KEY (user_uid) REFERENCES users(user_uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- CREATE TABLE postal_adress (
--   uid INTEGER UNSIGNED NOT NULL,
--   user_uid varchar(36) NOT NULL,
--   FOREIGN KEY (user_uid) REFERENCES users(user_uid),
--   FOREIGN KEY (role_id) REFERENCES roles(role_id)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
--
-- CREATE TABLE contact_information (
--   uid INTEGER UNSIGNED NOT NULL,
--   user_uid varchar(36) NOT NULL,
--   FOREIGN KEY (user_uid) REFERENCES users(user_uid),
--   FOREIGN KEY (role_id) REFERENCES roles(role_id)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Hanteringa av event banor och kontroller ------------------------------------
--Event
CREATE TABLE event (
    event_uid char(36) NOT NULL,
    title varchar(100) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    active BOOLEAN DEFAULT false,
    canceled BOOLEAN DEFAULT false,
    completed BOOLEAN DEFAULT false,
    description varchar(500)  NOT NULL,
    PRIMARY KEY (event_uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Bana
CREATE TABLE  track (
    track_uid char(36) NOT NULL,
    title varchar(100),
    link varchar(100),
    heightdifference INTEGER UNSIGNED,
    event_uid char (36) NOT NULL,
    description varchar(500)  NOT NULL,
    distance DECIMAL(10,2) DEFAULT NULL,
    start_date_time DATETIME DEFAULT NULL,
    active BOOLEAN DEFAULT true,
    FOREIGN KEY (event_uid) REFERENCES event(event_uid),
    PRIMARY KEY (track_uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Site plats för en konntroll
CREATE TABLE site (
    site_uid char(36) NOT NULL,
    place varchar(100) NOT NULL,
    adress varchar(100) NOT NULL,
    description varchar(500)  NOT NULL,
    lat DECIMAL(10,8),
    lng DECIMAL(11,8),
    location POINT ,
    PRIMARY KEY (site_uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE checkpoint (
    checkpoint_uid char(36) NOT NULL,
    site_uid char(36) NOT NULL,
    title varchar(100) NOT NULL,
    description varchar(500)  NOT NULL,
    distance DECIMAL(10,2) DEFAULT NULL,
    opens TIME,
    closing TIME,
    PRIMARY KEY (checkpoint_uid),
    FOREIGN KEY (site_uid) REFERENCES site(site_uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE event_tracks (
    track_uid char(36) NOT NULL,
    event_uid char(36) NOT NULL,
    PRIMARY KEY (event_uid,track_uid),
    FOREIGN KEY (track_uid) REFERENCES track(track_uid),
    FOREIGN KEY (event_uid) REFERENCES event(event_uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE track_checkpoint (
    track_uid char(36) NOT NULL,
    checkpoint_uid char(36) NOT NULL,
    PRIMARY KEY (checkpoint_uid,track_uid),
    FOREIGN KEY (track_uid) REFERENCES track(track_uid),
    FOREIGN KEY (checkpoint_uid) REFERENCES checkpoint(checkpoint_uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE participant (
    participant_uid char(36) NOT NULL,
    track_uid char(36) NOT NULL,
    competitor_uid char(36) NOT NULL,
    startnumber INTEGER UNSIGNED NOT NULL,
    finished BOOLEAN DEFAULT false,
    acpkod INTEGER UNSIGNED,
    club_uid char (36),
    time Time,
    dns BOOLEAN DEFAULT false,
    dnf BOOLEAN DEFAULT false,
    brevenr INTEGER UNSIGNED,
    PRIMARY KEY ( participant_uid, track_uid ,competitor_uid),
    FOREIGN KEY (track_uid) REFERENCES track(track_uid),
    FOREIGN KEY (competitor_uid) REFERENCES competitors(competitor_uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;;

CREATE TABLE participant_checkpoint (
    participant_uid char(36) NOT NULL,
    checkpoint_uid char(36) NOT NULL,
    passed BOOLEAN DEFAULT false,
    passeded_date_time DATETIME,
    lat DECIMAL(10,8),
    lng DECIMAL(11,8),
    PRIMARY KEY (participant_uid,checkpoint_uid),
    FOREIGN KEY (checkpoint_uid) REFERENCES checkpoint(checkpoint_uid),
    FOREIGN KEY (participant_uid) REFERENCES participant(participant_uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE club (
    club_uid char(36) NOT NULL,
    acp_kod INTEGER UNSIGNED,
    title varchar (200),
    PRIMARY KEY (club_uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



---Vyer  Några vyer som kan användas bla för att se vilka cyklister som ska passera en kontroll
--Cyklister vid en kontroll. Om man i where anger en site_uid tex för tex brännäset dyker de cyklister som inte passerat default upp
create view v_partisipant_to_pass_checkpoint AS SELECT tr.track_uid, pach.participant_uid,sit.site_uid, cpo.checkpoint_uid, sit.adress, a.startnumber, cp.given_name, cp.family_name, pach.passed, pach.passeded_date_time FROM `participant` a
inner join competitors cp on a.competitor_uid = cp.competitor_uid
inner join track tr on tr.track_uid = a.track_uid
inner join participant_checkpoint  pach on pach.participant_uid = a.participant_uid
inner join checkpoint cpo on cpo.checkpoint_uid = pach.checkpoint_uid
inner join site sit on sit.site_uid = cpo.site_uid;



