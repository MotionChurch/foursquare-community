-- Foursquare Community Site
-- 
-- Copyright (C) 2011 Foursquare Church.
-- 
-- Developers: Jesse Morgan <jmorgan@foursquarestaff.com>


-- The following cleans up existing data

DROP DATABASE IF EXISTS p4scommunity;
DROP USER p4scommunity@localhost;

-- The following creates a database and user

CREATE DATABASE p4scommunity;

CREATE USER p4scommunity@localhost IDENTIFIED BY 'password';

GRANT ALL ON p4scommunity.* TO p4scommunity@localhost;

USE p4scommunity;

-- The following creates the table structure

CREATE TABLE category (
    id          INTEGER     UNSIGNED NOT NULL AUTO_INCREMENT,
    shortname   VARCHAR(30) NOT NULL,
    name        VARCHAR(30) NOT NULL,
    description VARCHAR(255) NOT NULL,
    options     SET('price') NOT NULL,

    PRIMARY KEY(id)
);

CREATE TABLE source (
    id          INTEGER     UNSIGNED NOT NULL AUTO_INCREMENT,
    name        VARCHAR(60) NOT NULL,

    PRIMARY KEY(id),
    UNIQUE  KEY(name)
);

CREATE TABLE post (
    id          INTEGER     UNSIGNED NOT NULL AUTO_INCREMENT,
    name        VARCHAR(60) NOT NULL,
    category_id INTEGER     UNSIGNED NOT NULL,
    created     DATETIME    NOT NULL,
    description TEXT        NOT NULL,
    location    VARCHAR(100) NOT NULL,
    price       DECIMAL(10,2) NULL, 
    
    email       VARCHAR(255) NOT NULL,
    secretid    VARCHAR(32)  NOT NULL,

    source_id   INTEGER     UNSIGNED NOT NULL,
    reason      VARCHAR(255) NULL,
    stage      ENUM('verification',
                    'moderation',
                    'approved',
                    'rejected',
                    'deleted') NOT NULL DEFAULT 'verification',

    PRIMARY KEY(id),
    UNIQUE  KEY(secretid)
);

CREATE TABLE image (
    id          INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    post_id     INTEGER UNSIGNED NOT NULL,

    PRIMARY KEY(id)
);

CREATE TABLE user (
    id          INTEGER     UNSIGNED NOT NULL AUTO_INCREMENT,
    name        VARCHAR(60) NOT NULL,
    email       VARCHAR(255) NOT NULL,
    password    VARCHAR(40) NOT NULL,
    source_id   INTEGER     NOT NULL,
    admin       TINYINT(1)  NOT NULL DEFAULT 0,
    notify      TINYINT(1)  NOT NULL,

    PRIMARY KEY(id),
    UNIQUE  KEY(email)
);

CREATE TABLE page (
    id          INTEGER     UNSIGNED NOT NULL AUTO_INCREMENT,
    title       VARCHAR(60) NOT NULL,
    url         VARCHAR(60) NOT NULL,
    content     TEXT NOT NULL,

    PRIMARY KEY(id),
    UNIQUE  KEY(url)
);

-- CREATE TABLE moderator_schedule (
--     position    TINYINT UNSIGNED NOT NULL,
--     user_id     INTEGER UNSIGNED NOT NULL,
-- 
--     PRIMARY KEY(position, user_id)
-- );

CREATE VIEW moderator_schedule AS
    SELECT id AS position, id AS user_id FROM user
        WHERE notify=1;

CREATE TABLE moderator_exceptions ( 
    year        INTEGER UNSIGNED NOT NULL,
    week        TINYINT UNSIGNED NOT NULL,
    user_id     INTEGER UNSIGNED NOT NULL,
    substitute  INTEGER UNSIGNED NOT NULL,


    PRIMARY KEY(year, week, user_id)
);


-- The following creates some sample data
INSERT INTO `category` (`shortname`, `name`, `description`, `options`)
VALUES
    ('free', 'Free Items', 'Do you have something of value someone could use and you want to give it away?', ''),
    ('forsale', 'For Sale', 'Do you have something you no longer need and want to sell it?', 'price'),
    ('needs', 'Needs', 'Do you need something (furniture, job, housing, etc) that someone might be able to help with?', ''),
    ('events', 'Events', 'Do you have an upcoming event (qualifying statement here?) you would like to announce?', 'price'),
    ('jobs', 'Jobs', 'Do you have a job/position to fill and you''d like to tell people about?', 'price'),
    ('housing', 'Housing', 'Do you have housing in the East Pierce County area you''d like to make people aware of?', 'price');

INSERT INTO source (name) VALUES ('Foursquare Church');

INSERT INTO user (name, email, password, source_id, admin)
    VALUES ('Jesse Morgan', 'jmorgan@foursquarestaff.com',
        'password-sha1', 1, 1);
