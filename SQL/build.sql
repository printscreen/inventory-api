
CREATE TABLE user_type (
    user_type_id    INT(10)         NOT NULL auto_increment,
    name            VARCHAR(255)    NOT NULL UNIQUE,
    PRIMARY KEY (user_type_id)
);

INSERT INTO user_type (name) VALUES ('admin'), ('employee'), ('end_user');

CREATE TABLE users (
    user_id         INT(10)         NOT NULL auto_increment,
    first_name      VARCHAR(255)    NOT NULL,
    last_name       VARCHAR(255)    NOT NULL,
    email           VARCHAR(255)    NOT NULL UNIQUE,
    password        VARCHAR(32)     NOT NULL,
    user_type_id    INT(10)         NOT NULL,
    active          BOOLEAN         NOT NULL,
    PRIMARY KEY (user_id),
    FOREIGN KEY (user_type_id) REFERENCES user_type(user_type_id)
);

CREATE TABLE token (
    token_id        INT(10)         NOT NULL auto_increment,
    user_id         INT(10)         NOT NULL,
    token           VARCHAR(255)    NOT NULL UNIQUE,
    insert_ts       TIMESTAMP       NOT NULL,
    update_ts       TIMESTAMP       NOT NULL,
    PRIMARY KEY (token_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    CONSTRAINT uc_user_token UNIQUE (user_id, token)
);

CREATE TABLE resource (
    resource_id     INT(10)         NOT NULL auto_increment,
    name            VARCHAR(255)    NOT NULL UNIQUE,
    PRIMARY KEY (resource_id)
);

INSERT INTO resource 
    (name) 
VALUES 
    ('default:index:index')
,   ('admin:location:get')    
,   ('admin:location:view')
,   ('admin:location:edit')
,   ('admin:user:get')
,   ('admin:user:view-employee')
,   ('admin:user:view-customer')
,   ('admin:user:edit-employee')
,   ('admin:user:edit-customer')
,   ('admin:user:view-user-location')
,   ('admin:user:add-user-location')
,   ('admin:user:delete-user-location')
,   ('admin:unit:get')
,   ('admin:unit:view-unit-by-location')
,   ('admin:unit:view-unit-by-user')
,   ('admin:unit:edit-unit')
,   ('admin:unit:add-unit-user')
,   ('admin:unit:delete-unit-user')
,   ('admin:unit:unit-users')
,   ('admin:unit:unit-available-users')
;

CREATE TABLE user_type_resource (
    user_type_resource_id   INT(10)     NOT NULL auto_increment,
    resource_id             INT(10)     NOT NULL,
    user_type_id            INT(10)     NOT NULL,
    PRIMARY KEY (user_type_resource_id),
    FOREIGN KEY (user_type_id) REFERENCES user_type(user_type_id),
    FOREIGN KEY (resource_id) REFERENCES resource(resource_id),
    CONSTRAINT uc_user_type_id_resource_id UNIQUE (resource_id, user_type_id)
);

INSERT INTO user_type_resource
    (resource_id, user_type_id)
VALUES
    (1,1),(1,2),(1,3), -- default:index:index
    (2,1), -- admin:location:get
    (3,1), -- admin:location:view
    (4,1), -- admin:location:edit
    (5,1),(5,2), -- admin:user:get
    (6,1), -- admin:user:view-employee
    (7,1),(7,2), -- admin:user:view-customer
    (8,1), -- admin:user:edit-employee
    (9,1),(9,2), -- admin:user:edit-customer
    (10,1),(10,2), -- admin:user:view-user-location
    (11,1),(11,2), -- admin:user:add-user-location
    (12,1),(12,2), -- admin:user:delete-user-location
    (13,1),(13,2), -- admin:unit:get
    (14,1),(14,2), -- admin:unit:view-unit-by-location
    (15,1),(15,2), -- admin:unit:view-unit-by-user
    (16,1),(16,2), -- admin:unit:edit-unit
    (17,1),(17,2), -- admin:unit:add-unit-users
    (18,1),(18,2), -- admin:unit:delete-users
    (19,1),(19,2), -- admin:unit:unit-users
    (20,1),(20,2) -- admin:unit:unit-available-users
;


CREATE TABLE location (
    location_id         INT(10)         NOT NULL auto_increment,
    name                VARCHAR(255)    NOT NULL UNIQUE,
    street              VARCHAR(255)    NULL,
    city                VARCHAR(255)    NULL,
    state               VARCHAR(255)    NULL,
    zip                 VARCHAR(255)    NULL,
    phone_number        VARCHAR(255)    NULL,
    active              BOOLEAN         NOT NULL DEFAULT true,
    PRIMARY KEY (location_id)
);

CREATE TABLE user_location (
    user_location_id    INT(10)         NOT NULL auto_increment,
    user_id             INT(10)         NOT NULL,
    location_id         INT(10)         NOT NULL,
    PRIMARY KEY (user_location_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (location_id) REFERENCES location(location_id),
    UNIQUE KEY user_location_user_id_location_id (user_id,location_id)
);

CREATE TABLE unit (
    unit_id             INT(10)         NOT NULL auto_increment,
    name                VARCHAR(255)    NOT NULL,
    location_id         INT(10)         NOT NULL,
    active              BOOLEAN         DEFAULT true,
    PRIMARY KEY (unit_id),
    FOREIGN KEY (location_id) REFERENCES location(location_id),
    UNIQUE KEY unit_name_location_id (name,location_id)
);

CREATE TABLE user_unit (
    user_unit_id        INT(10)         NOT NULL auto_increment,
    user_id             INT(10)         NOT NULL,
    unit_id             INT(10)         NOT NULL,
    active              BOOLEAN         NULL DEFAULT NULL,
    PRIMARY KEY (user_unit_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (unit_id) REFERENCES unit(unit_id),
    UNIQUE KEY user_unit_user_id_unit_id (user_id,unit_id),
    UNIQUE KEY user_unit_unit_id_active (unit_id,active)
);

CREATE TABLE item_type (
    item_type_id    INT(10)         NOT NULL auto_increment,
    name            VARCHAR(255)    NOT NULL UNIQUE,
    PRIMARY KEY (item_type_id)
);

CREATE TABLE item_attribute_type (
    item_attribute_type_id  INT(10)         NOT NULL auto_increment,
    name                    VARCHAR(255)    NOT NULL,
    item_value              TEXT            NULL,
    PRIMARY KEY (item_attribute_type_id)
);

CREATE TABLE item_type_attribute (
    item_type_attribute_id  INT(10)         NOT NULL auto_increment,
    item_type_id            INT(10)         NOT NULL,
    item_attribute_type_id  INT(10)         NOT NULL,
    name                    VARCHAR(255)    NOT NULL,
    order_number            INT(10)         NULL,
    PRIMARY KEY (item_type_attribute_id),
    FOREIGN KEY (item_type_id) REFERENCES item_type(item_type_id),
    FOREIGN KEY (item_attribute_type_id) REFERENCES item_attribute_type(item_attribute_type_id)
);

CREATE TABLE item (
    item_id         INT(10)         NOT NULL auto_increment,
    item_type_id    INT(10)         NOT NULL,
    user_unit_id    INT(10)         NOT NULL,
    location_id     INT(10)         NULL,
    name            VARCHAR(255)    NOT NULL UNIQUE,
    description     TEXT            NULL,
    location        TEXT            NULL,
    PRIMARY KEY (item_id),
    FOREIGN KEY (item_type_id) REFERENCES item_type(item_type_id),
    FOREIGN KEY (user_unit_id) REFERENCES user_unit(user_unit_id)
);

CREATE TABLE item_attribute_value (
    item_attribute_value_id INT(10)         NOT NULL auto_increment,
    item_type_attribute_id  INT(10)         NOT NULL,
    item_id                 INT(10)         NOT NULL,
    value                   TEXT            NOT NULL,
    PRIMARY KEY (item_attribute_value_id),
    FOREIGN KEY (item_type_attribute_id) REFERENCES item_type_attribute(item_type_attribute_id),
    FOREIGN KEY (item_id) REFERENCES item(item_id),
    UNIQUE KEY item_type_attribte_id_item_id (item_type_attribute_id, item_id)
);
