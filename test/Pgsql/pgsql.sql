/**
 * This is the database schema for testing PostgreSQL support of yii Active Record.
 * To test this feature, you need to create a database named 'test' on 'localhost'
 * and create an account 'postgres/postgres' which owns this test database.
 */
DROP TABLE IF EXISTS "composite_fk" CASCADE;
DROP TABLE IF EXISTS "order_item" CASCADE;
DROP TABLE IF EXISTS "item" CASCADE;
DROP TABLE IF EXISTS "order_item_with_null_fk" CASCADE;
DROP TABLE IF EXISTS "order" CASCADE;
DROP TABLE IF EXISTS "order_with_null_fk" CASCADE;
DROP TABLE IF EXISTS "category" CASCADE;
DROP TABLE IF EXISTS "customer" CASCADE;
DROP TABLE IF EXISTS "profile" CASCADE;
DROP TABLE IF EXISTS "type" CASCADE;
DROP TABLE IF EXISTS "null_values" CASCADE;
DROP TABLE IF EXISTS "constraints" CASCADE;
DROP TABLE IF EXISTS "bool_values" CASCADE;
DROP TABLE IF EXISTS "drop_primary_test" CASCADE;

CREATE TABLE "constraints"
(
  id     INTEGER NOT NULL,
  field1 VARCHAR(255)
);

CREATE TABLE "profile" (
  id          SERIAL       NOT NULL PRIMARY KEY,
  description VARCHAR(128) NOT NULL
);

CREATE TABLE "customer" (
  id          SERIAL       NOT NULL PRIMARY KEY,
  email       VARCHAR(128) NOT NULL,
  name        VARCHAR(128),
  address     TEXT,
  status      INTEGER DEFAULT 0,
  bool_status BOOLEAN DEFAULT FALSE,
  profile_id  INTEGER
);

COMMENT ON COLUMN public.customer.email IS 'someone@example.com';

CREATE TABLE "category" (
  id   SERIAL       NOT NULL PRIMARY KEY,
  name VARCHAR(128) NOT NULL
);

CREATE TABLE "item" (
  id          SERIAL       NOT NULL PRIMARY KEY,
  name        VARCHAR(128) NOT NULL,
  category_id INTEGER      NOT NULL REFERENCES "category" (id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE "order" (
  id          SERIAL         NOT NULL PRIMARY KEY,
  customer_id INTEGER        NOT NULL REFERENCES "customer" (id) ON UPDATE CASCADE ON DELETE CASCADE,
  created_at  INTEGER        NOT NULL,
  total       DECIMAL(10, 0) NOT NULL
);

CREATE TABLE "order_with_null_fk" (
  id          SERIAL         NOT NULL PRIMARY KEY,
  customer_id INTEGER,
  created_at  INTEGER        NOT NULL,
  total       DECIMAL(10, 0) NOT NULL
);

CREATE TABLE "order_item" (
  order_id INTEGER        NOT NULL REFERENCES "order" (id) ON UPDATE CASCADE ON DELETE CASCADE,
  item_id  INTEGER        NOT NULL REFERENCES "item" (id) ON UPDATE CASCADE ON DELETE CASCADE,
  quantity INTEGER        NOT NULL,
  subtotal DECIMAL(10, 0) NOT NULL,
  PRIMARY KEY (order_id, item_id)
);

CREATE TABLE "order_item_with_null_fk" (
  order_id INTEGER,
  item_id  INTEGER,
  quantity INTEGER        NOT NULL,
  subtotal DECIMAL(10, 0) NOT NULL
);

CREATE TABLE "composite_fk" (
  id       INTEGER NOT NULL,
  order_id INTEGER NOT NULL,
  item_id  INTEGER NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT FK_composite_fk_order_item FOREIGN KEY (order_id, item_id) REFERENCES "order_item" (order_id, item_id) ON DELETE CASCADE
);

CREATE TABLE "null_values" (
  id        INT NOT NULL,
  var1      INT NULL,
  var2      INT NULL,
  var3      INT         DEFAULT NULL,
  stringcol VARCHAR(32) DEFAULT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE "type" (
  int_col      INTEGER          NOT NULL,
  int_col2     INTEGER                   DEFAULT '1',
  smallint_col SMALLINT                  DEFAULT '1',
  char_col     CHAR(100)        NOT NULL,
  char_col2    VARCHAR(100)              DEFAULT 'something',
  char_col3    TEXT,
  float_col    DOUBLE PRECISION NOT NULL,
  float_col2   DOUBLE PRECISION          DEFAULT '1.23',
  blob_col     BYTEA,
  numeric_col  DECIMAL(5, 2)             DEFAULT '33.22',
  time         TIMESTAMP        NOT NULL DEFAULT '2002-01-01 00:00:00',
  bool_col     BOOLEAN          NOT NULL,
  bool_col2    BOOLEAN                   DEFAULT TRUE,
  ts_default   TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  bit_col      BIT(8)           NOT NULL DEFAULT B'10000010'
);

CREATE TABLE "bool_values" (
  id            SERIAL  NOT NULL PRIMARY KEY,
  bool_col      BOOL,
  default_true  BOOL    NOT NULL DEFAULT TRUE,
  default_false BOOLEAN NOT NULL DEFAULT FALSE
);

INSERT INTO "profile" (description) VALUES ('profile customer 1');
INSERT INTO "profile" (description) VALUES ('profile customer 3');

INSERT INTO "customer" (email, name, address, status, bool_status, profile_id)
VALUES ('user1@example.com', 'user1', 'address1', 1, TRUE, 1);
INSERT INTO "customer" (email, name, address, status, bool_status)
VALUES ('user2@example.com', 'user2', 'address2', 1, TRUE);
INSERT INTO "customer" (email, name, address, status, bool_status, profile_id)
VALUES ('user3@example.com', 'user3', 'address3', 2, FALSE, 2);

INSERT INTO "category" (name) VALUES ('Books');
INSERT INTO "category" (name) VALUES ('Movies');

INSERT INTO "item" (name, category_id) VALUES ('Agile Web Application Development with Yii1.1 and PHP5', 1);
INSERT INTO "item" (name, category_id) VALUES ('Yii 1.1 Application Development Cookbook', 1);
INSERT INTO "item" (name, category_id) VALUES ('Ice Age', 2);
INSERT INTO "item" (name, category_id) VALUES ('Toy Story', 2);
INSERT INTO "item" (name, category_id) VALUES ('Cars', 2);

INSERT INTO "order" (customer_id, created_at, total) VALUES (1, 1325282384, 110.0);
INSERT INTO "order" (customer_id, created_at, total) VALUES (2, 1325334482, 33.0);
INSERT INTO "order" (customer_id, created_at, total) VALUES (2, 1325502201, 40.0);

INSERT INTO "order_with_null_fk" (customer_id, created_at, total) VALUES (1, 1325282384, 110.0);
INSERT INTO "order_with_null_fk" (customer_id, created_at, total) VALUES (2, 1325334482, 33.0);
INSERT INTO "order_with_null_fk" (customer_id, created_at, total) VALUES (2, 1325502201, 40.0);

INSERT INTO "order_item" (order_id, item_id, quantity, subtotal) VALUES (1, 1, 1, 30.0);
INSERT INTO "order_item" (order_id, item_id, quantity, subtotal) VALUES (1, 2, 2, 40.0);
INSERT INTO "order_item" (order_id, item_id, quantity, subtotal) VALUES (2, 4, 1, 10.0);
INSERT INTO "order_item" (order_id, item_id, quantity, subtotal) VALUES (2, 5, 1, 15.0);
INSERT INTO "order_item" (order_id, item_id, quantity, subtotal) VALUES (2, 3, 1, 8.0);
INSERT INTO "order_item" (order_id, item_id, quantity, subtotal) VALUES (3, 2, 1, 40.0);

INSERT INTO "order_item_with_null_fk" (order_id, item_id, quantity, subtotal) VALUES (1, 1, 1, 30.0);
INSERT INTO "order_item_with_null_fk" (order_id, item_id, quantity, subtotal) VALUES (1, 2, 2, 40.0);
INSERT INTO "order_item_with_null_fk" (order_id, item_id, quantity, subtotal) VALUES (2, 4, 1, 10.0);
INSERT INTO "order_item_with_null_fk" (order_id, item_id, quantity, subtotal) VALUES (2, 5, 1, 15.0);
INSERT INTO "order_item_with_null_fk" (order_id, item_id, quantity, subtotal) VALUES (2, 3, 1, 8.0);
INSERT INTO "order_item_with_null_fk" (order_id, item_id, quantity, subtotal) VALUES (3, 2, 1, 40.0);

/**
 * (Postgres-)Database Schema for validator tests
 */

DROP TABLE IF EXISTS "validator_main" CASCADE;
DROP TABLE IF EXISTS "validator_ref" CASCADE;

CREATE TABLE "validator_main" (
  id     INTEGER NOT NULL PRIMARY KEY,
  field1 VARCHAR(255)
);

CREATE TABLE "validator_ref" (
  id      INTEGER NOT NULL PRIMARY KEY,
  a_field VARCHAR(255),
  ref     INTEGER
);

INSERT INTO "validator_main" (id, field1) VALUES (1, 'just a string1');
INSERT INTO "validator_main" (id, field1) VALUES (2, 'just a string2');
INSERT INTO "validator_main" (id, field1) VALUES (3, 'just a string3');
INSERT INTO "validator_main" (id, field1) VALUES (4, 'just a string4');
INSERT INTO "validator_ref" (id, a_field, ref) VALUES (1, 'ref_to_2', 2);
INSERT INTO "validator_ref" (id, a_field, ref) VALUES (2, 'ref_to_2', 2);
INSERT INTO "validator_ref" (id, a_field, ref) VALUES (3, 'ref_to_3', 3);
INSERT INTO "validator_ref" (id, a_field, ref) VALUES (4, 'ref_to_4', 4);
INSERT INTO "validator_ref" (id, a_field, ref) VALUES (5, 'ref_to_4', 4);
INSERT INTO "validator_ref" (id, a_field, ref) VALUES (6, 'ref_to_5', 5);

CREATE TABLE "drop_primary_test" (
  order_id INTEGER        NOT NULL REFERENCES "order" (id) ON UPDATE CASCADE ON DELETE CASCADE,
  item_id  INTEGER        NOT NULL REFERENCES "item" (id) ON UPDATE CASCADE ON DELETE CASCADE,
  profile_id INTEGER NOT NULL CONSTRAINT "fk_profile_id" REFERENCES "profile" (id),
  PRIMARY KEY (order_id, item_id)
);