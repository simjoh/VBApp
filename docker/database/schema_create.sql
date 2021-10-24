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
    role_id int(11) NOT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;;


-- Permissions
CREATE TABLE permissions (
  perm_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  perm_desc varchar(255) NOT NULL,
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
    title varchar(100) NOT NULL,
    event_uid char (36) NOT NULL,
    description varchar(500)  NOT NULL,
    distance DECIMAL(10,2) DEFAULT NULL,
    FOREIGN KEY (event_uid) REFERENCES event(event_uid),
    PRIMARY KEY (track_uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Site plats för en konntroll
CREATE TABLE site (
    site_uid char(36) NOT NULL,
    place varchar(100) NOT NULL,
    title varchar(100) NOT NULL,
    adress varchar(100) NOT NULL,
    description varchar(500)  NOT NULL,
    location POINT NOT NULL,
    PRIMARY KEY (site_uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE control (
    control_uid char(36) NOT NULL,
    site_uid char(36) NOT NULL,
    title varchar(100) NOT NULL,
    description varchar(500)  NOT NULL,
    distance DECIMAL(10,2) DEFAULT NULL,
    opens TIME,
    closing TIME,
    PRIMARY KEY (control_uid),
    FOREIGN KEY (site_uid) REFERENCES site(site_uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE  event_tracks (
    track_uid char(36) NOT NULL,
    event_uid char(36) NOT NULL,
    PRIMARY KEY (event_uid,track_uid),
    FOREIGN KEY (track_uid) REFERENCES track(track_uid),
    FOREIGN KEY (event_uid) REFERENCES event(event_uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE track_controls (
    track_uid char(36) NOT NULL,
    control_uid char(36) NOT NULL,
    PRIMARY KEY (control_uid,track_uid),
    FOREIGN KEY (track_uid) REFERENCES track(track_uid),
    FOREIGN KEY (control_uid) REFERENCES control(control_uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Koppla cyklister till banor och kontroller

CREATE TABLE competitor_connection_to_track (
    competitor_uid char(36) NOT NULL,
    track_uid char(36) NOT NULL,
    startnumber INTEGER UNSIGNED NOT NULL,
    PRIMARY KEY (competitor_uid,track_uid),
    FOREIGN KEY (track_uid) REFERENCES track(track_uid),
    FOREIGN KEY (competitor_uid) REFERENCES competitors(competitor_uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


