BEGIN;

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
,   ('admin:item:view-item-type')
,   ('admin:item:get-item-type')
,   ('admin:item:edit-item-type')
,   ('admin:item:delete-item-type')
,   ('admin:item:view-item-attribute-type')
,   ('admin:item:view-item-type-attribute')
,   ('admin:item:get-item-type-attribute')
,   ('admin:item:edit-item-type-attribute')
,   ('admin:item:delete-item-type-attribute')
,   ('admin:item:edit-item-type-attribute-order')
,   ('admin:item:location-item-type')
,   ('admin:item:location-available-item-type')
,   ('admin:item:add-location-item-type')
,   ('admin:item:delete-location-item-type')
,   ('default:location:view')
,   ('default:unit:view')
,   ('default:item:view-by-unit')
,   ('default:item:edit')
,   ('default:item:delete')
,   ('default:item:get-location-item-type')
,   ('default:item:get-item-type-attribute')
,   ('default:item:get-item')
,   ('default:profile:index')
,   ('default:profile:reset-password')
,   ('default:image:get')
,   ('default:image:view')
,   ('default:image:add')
,   ('default:image:delete')
,   ('default:image:make-default')
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
    (20,1),(20,2), -- admin:unit:unit-available-users
    (21,1), -- admin:item:view-item-type
    (22,1), -- admin:item:get-item-type
    (23,1), -- admin:item:edit-item-type
    (24,1), -- admin:item:delete-item-type
    (25,1), -- admin:item:view-item-attribute-type
    (26,1), -- admin:item:view-item-type-attribute
    (27,1), -- admin:item:get-item-type-attribute
    (28,1), -- admin:item:edit-item-type-attribute
    (29,1), -- admin:item:delete-item-type-attribute
    (30,1), -- admin:item:edit-item-type-attribute-order
    (31,1), -- admin:item:location-item-type
    (32,1), -- admin:item:location-available-item-type
    (33,1), -- admin:item:add-location-item-type
    (34,1), -- admin:item:delete-location-item-type
    (35,1),(35,2),(35,3), -- default:location:view
    (36,1),(36,2),(36,3), -- default:unit:view
    (37,1),(37,2),(37,3), -- default:item:view-by-unit
    (38,1),(38,2),(38,3), -- default:item:edit
    (39,1),(39,2),(39,3), -- default:item:delete
    (40,1),(40,2),(40,3), -- default:item:get-location-item-type
    (41,1),(41,2),(41,3), -- default:item:get-item-type-attribute
    (42,1),(42,2),(42,3), -- default:item:get-item
    (43,1),(43,2),(43,3), -- default:profile:index
    (44,1),(44,2),(44,3), -- default:profile:reset-password
    (45,1),(45,2),(45,3), -- default:image:get
    (46,1),(46,2),(46,3), -- default:image:view
    (47,1),(47,2),(47,3), -- default:image:add
    (48,1),(48,2),(48,3), -- default:image:delete
    (49,1),(49,2),(49,3) -- default:image:make-default
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

CREATE TABLE item_type_location (
    item_type_location_id   INT(10)     NOT NULL auto_increment,
    item_type_id            INT(10)     NOT NULL,
    location_id             INT(10)     NOT NULL,
    PRIMARY KEY (item_type_location_id),
    FOREIGN KEY (item_type_id) REFERENCES item_type(item_type_id) ON DELETE CASCADE,
    FOREIGN KEY (location_id) REFERENCES location(location_id) ON DELETE CASCADE,
    UNIQUE KEY item_type_id_location_id (item_type_id,location_id)
);

CREATE TABLE item_attribute_type (
    item_attribute_type_id  INT(10)         NOT NULL auto_increment,
    name                    VARCHAR(255)    NOT NULL,
    PRIMARY KEY (item_attribute_type_id)
);

INSERT INTO item_attribute_type
    (name)
VALUES
    ('Text'),
    ('Numbers'),
    ('Select'),
    ('MultiSelect'),
    ('TextArea')
;

CREATE TABLE item_type_attribute (
    item_type_attribute_id  INT(10)         NOT NULL auto_increment,
    item_type_id            INT(10)         NOT NULL,
    item_attribute_type_id  INT(10)         NOT NULL,
    name                    VARCHAR(255)    NOT NULL,
    value                   TEXT            NULL,
    order_number            INT(10)         NULL,
    PRIMARY KEY (item_type_attribute_id),
    FOREIGN KEY (item_type_id) REFERENCES item_type(item_type_id) ON DELETE CASCADE,
    FOREIGN KEY (item_attribute_type_id) REFERENCES item_attribute_type(item_attribute_type_id) ON DELETE CASCADE
);

CREATE TABLE item (
    item_id         INT(10)         NOT NULL auto_increment,
    item_type_id    INT(10)         NOT NULL,
    user_unit_id    INT(10)         NOT NULL,
    location_id     INT(10)         NULL,
    name            VARCHAR(255)    NOT NULL,
    description     TEXT            NULL,
    location        TEXT            NULL,
    attribute       TEXT            NULL,
    count           INT(10)         NULL,
    last_modified   TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (item_id),
    FOREIGN KEY (item_type_id) REFERENCES item_type(item_type_id),
    FOREIGN KEY (user_unit_id) REFERENCES user_unit(user_unit_id),
    UNIQUE KEY item_id_name (item_id, name)
);

CREATE TABLE item_image (
    item_image_id   INT(10)         NOT NULL auto_increment,
    item_id         INT(10)         NOT NULL,
    user_id         INT(10)         NOT NULL,
    lat             FLOAT           NULL,
    lon             FLOAT           NULL,
    insert_ts       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    default_image   BOOLEAN         NULL DEFAULT NULL,
    is_thumbnail    BOOLEAN         NULL DEFAULT false,
    PRIMARY KEY (item_image_id),
    FOREIGN KEY (item_id) REFERENCES item(item_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY item_id_default (item_id, default_image)
);

COMMIT;