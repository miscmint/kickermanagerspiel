CREATE TABLE tx_kickermanagerspiel_domain_model_club (
    title varchar(255) DEFAULT '' NOT NULL,
    id varchar(64) DEFAULT '' NOT NULL
);

CREATE TABLE tx_kickermanagerspiel_domain_model_player (
    id varchar(64) DEFAULT '' NOT NULL,
    mode varchar(64) DEFAULT '' NOT NULL,
    firstname varchar(255) DEFAULT '' NOT NULL,
    lastname varchar(255) DEFAULT '' NOT NULL,
    position varchar(64) DEFAULT '' NOT NULL,
    value float DEFAULT 0 NULL,
    club int(4) DEFAULT 0 NOT NULL,
    club_before_first_matchday int(4) DEFAULT 0 NOT NULL,
    points int(4) DEFAULT 0 NOT NULL,
    points_matchdays text,
    season int(4) DEFAULT 0 NOT NULL,
    league int(4) DEFAULT 0 NOT NULL
);

CREATE TABLE tx_kickermanagerspiel_domain_model_lastimport (
    hash varchar(64) DEFAULT '' NOT NULL,
    matchday int(4) DEFAULT 0 NOT NULL,
    content text,
    arraykey varchar(64) DEFAULT '' NOT NULL
);
