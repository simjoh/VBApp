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
CREATE TABLE IF NOT EXISTS users (
    user_uid char(36) NOT NULL,
    user_name varchar(100)  NOT NULL,
    given_name varchar(100),
    family_name varchar(100),
    role_id int(11) NOT NULL,
    password char(128),
    PRIMARY KEY (user_uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;;


CREATE TABLE IF NOT EXISTS competitors (
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
  perm_mod varchar(15) NOT NULL,
  perm_id int(11) NOT NULL,
  perm_desc varchar(255) NOT NULL,
  PRIMARY KEY (perm_mod,perm_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE roles (
  role_id int(11) NOT NULL,
  role_name varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE roles
  ADD PRIMARY KEY (role_id),
  ADD UNIQUE KEY role_name (role_name);

ALTER TABLE roles
  MODIFY role_id int(11) NOT NULL AUTO_INCREMENT;

CREATE TABLE roles_permissions (
  role_id int(11) NOT NULL,
  perm_mod varchar(15) NOT NULL,
  perm_id int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE roles_permissions
  ADD PRIMARY KEY (role_id,perm_mod,perm_id);

