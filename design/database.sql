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
    
    email       VARCHAR(255) NOT NULL,
    secretid    VARCHAR(32)  NOT NULL,

    source_id   INTEGER     UNSIGNED NOT NULL,
    stage      ENUM('verification',
                    'moderation',
                    'approved',
                    'rejected') NOT NULL DEFAULT 'verification',

    PRIMARY KEY(id),
    UNIQUE  KEY(secretid)
);

CREATE TABLE user (
    id          INTEGER     UNSIGNED NOT NULL AUTO_INCREMENT,
    name        VARCHAR(60) NOT NULL,
    email       VARCHAR(255) NOT NULL,
    password    VARCHAR(40) NOT NULL,
    source_id   INTEGER     NOT NULL,
    admin       TINYINT(1)  NOT NULL DEFAULT 0,

    PRIMARY KEY(id),
    UNIQUE  KEY(email)
);

CREATE TABLE pages (
    id          INTEGER     UNSIGNED NOT NULL AUTO_INCREMENT,
    url         VARCHAR(60) NOT NULL,
    content     TEXT NOT NULL,

    PRIMARY KEY(id),
    UNIQUE  KEY(url)
);

-- The following creates some sample data
INSERT INTO category (name, shortname) VALUES 
    ('Jobs',    'jobs'), 
    ('Housing', 'housing'),
    ('Events',  'events'),
    ('For Sale','forsale'),
    ('Needs',   'needs');

INSERT INTO source (name) VALUES ('Foursquare Church');

INSERT INTO user (name, email, password, source_id, admin)
    VALUES ('Jesse Morgan', 'jmorgan@foursquarestaff.com',
        'password-sha1', 1, 1);
