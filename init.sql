CREATE SCHEMA IF NOT EXISTS fotm AUTHORIZATION fotm;
SET search_path TO fotm;

CREATE TABLE IF NOT EXISTS moonmembers (
    id BIGSERIAL PRIMARY KEY,
    moonname VARCHAR(64) NOT NULL,
    pronouns VARCHAR(64) DEFAULT '',
    dect VARCHAR(64) DEFAULT '',
    decription VARCHAR(256) DEFAULT '',
    badge1 VARCHAR(128) DEFAULT '',
    badge2 VARCHAR(128) DEFAULT '',
    badge3 VARCHAR(128) DEFAULT '',
    present INT DEFAULT 0,
    CONSTRAINT moonname_idx UNIQUE (moonname)
);