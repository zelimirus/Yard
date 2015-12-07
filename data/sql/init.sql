--
-- MYSQL
--

--
-- Drop all tables first
-- 

DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS resources;
DROP TABLE IF EXISTS permissions;
DROP TABLE IF EXISTS admin_users;
DROP TABLE IF EXISTS admin_menu_items;
DROP TABLE IF EXISTS roles_admin_menu_items;
DROP TABLE IF EXISTS log_admin_actions;
DROP TABLE IF EXISTS menu_items_icons;

DROP TABLE IF EXISTS languages;
DROP TABLE IF EXISTS translate_keys;
DROP TABLE IF EXISTS translate_messages;
DROP TABLE IF EXISTS countries;

DROP TABLE IF EXISTS medias;
DROP TABLE IF EXISTS media_types;
DROP TABLE IF EXISTS media_libraries;
DROP TABLE IF EXISTS media_libraries_medias;

--
-- roles 
--
CREATE TABLE roles
(
  id serial NOT NULL,
  name text NOT NULL,
  CONSTRAINT roles_pkey PRIMARY KEY (id)
);

-- 
-- resources. name = module:action
--
CREATE TABLE resources
(
  id serial NOT NULL,
  name text NOT NULL,
  description text,
  CONSTRAINT resources_pkey PRIMARY KEY (id)
);

-- 
-- permissions 
-- 
CREATE TABLE permissions
(
  id serial NOT NULL,
  role_id integer NOT NULL,
  resource_id integer NOT NULL,
  action text default NULL,
  is_allowed boolean NOT NULL DEFAULT false,
  CONSTRAINT permissions_pkey PRIMARY KEY (id)
);

-- 
-- admin_users
-- 
CREATE TABLE admin_users
(
  id serial NOT NULL,
  password text NOT NULL,
  first_name text,
  last_name text,
  email text NOT NULL,
  role_id integer NOT NULL,
  image text,
  is_active boolean NOT NULL DEFAULT true,
  CONSTRAINT admin_users_pkey PRIMARY KEY (id)
);

--
-- admin_menu_items table
--
CREATE TABLE admin_menu_items
(
  id serial NOT NULL,
  name text NOT NULL,
  title text NOT NULL,
  module text,
  controller text,
  action text,
  params text,
  is_active boolean NOT NULL DEFAULT true,
  order_id integer NOT NULL,
  icon_id integer,
  parent_id integer,
  CONSTRAINT admin_menu_items_pkey PRIMARY KEY (id)
);

--
-- roles_admin_menu_items table
--
CREATE TABLE roles_admin_menu_items
(
  id serial NOT NULL,
  role_id integer NOT NULL,
  admin_menu_item_id integer NOT NULL,
  CONSTRAINT roles_admin_menu_items_pkey PRIMARY KEY (id)
);

--
-- log_admin_actions
--
CREATE TABLE log_admin_actions
(
  id serial NOT NULL,
  user_id integer NOT NULL,
  action text NOT NULL,
  affected_table text NOT NULL,  
  params text,
  date timestamp DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT log_admin_actions_pkey PRIMARY KEY (id)
);

--
-- menu_items_icons
--
CREATE TABLE menu_items_icons
(
  id serial NOT NULL,
  icon text NOT NULL,
  CONSTRAINT menu_items_icons_pkey PRIMARY KEY (id)
);

--
-- languages
--
CREATE TABLE languages
(
  id serial NOT NULL,
  country_code TEXT NOT NULL,
  name TEXT NOT NULL,
  is_active boolean NOT NULL DEFAULT FALSE,
  CONSTRAINT translate_countries_pkey PRIMARY KEY (id)
);

--
-- translate_messages
--
CREATE TABLE translate_messages
(
  id serial NOT NULL,
  value TEXT,
  key_id integer NOT NULL,
  language_id integer NOT NULL,
  CONSTRAINT translate_messages_pkey PRIMARY KEY (id)
);

--
-- translate_keys
--
CREATE TABLE translate_keys
(
  id serial NOT NULL,
  `key` TEXT NOT NULL,
  description TEXT NOT NULL,
  CONSTRAINT translate_keys_pkey PRIMARY KEY (id)
);

--
-- countries
--
CREATE TABLE countries
(
  id serial NOT NULL,
  country_code TEXT NOT NULL,
  name TEXT NOT NULL,
  is_active boolean NOT NULL DEFAULT FALSE,
  language_id integer,
  calling_code integer,
  CONSTRAINT countries_pkey PRIMARY KEY (id)
);


--
-- medias
--
CREATE TABLE medias
(
  id serial NOT NULL,
  title text NOT NULL,
  file_name text NOT NULL,
  media_type_id integer NOT NULL,
  url text,
  description text,  
  created timestamp DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT medias_pkey PRIMARY KEY (id)
);

--
-- media_types
--
CREATE TABLE media_types
(
  id serial NOT NULL,
  name text NOT NULL,
  label text NOT NULL,
  CONSTRAINT media_types_id PRIMARY KEY (id)
);

--
-- media_libraries
--
CREATE TABLE media_libraries
(
  id serial NOT NULL,
  name text NOT NULL,
  path text NOT NULL,
  CONSTRAINT media_libraries_id PRIMARY KEY (id)
);


-- media_libraries_medias
--
CREATE TABLE media_libraries_medias
(
  id serial NOT NULL,
  media_library_id integer NOT NULL,
  media_id integer NOT NULL,
  CONSTRAINT media_libraries_medias_id PRIMARY KEY (id)
);

INSERT INTO media_types (id, name, label) VALUES (1, 'image', 'Image'), (2, 'video', 'Video'),(3,'youtube','Youtube');

INSERT INTO roles (id, name) VALUES (1, 'Super Admin');
INSERT INTO roles (id, name) VALUES (2, 'Admin');

INSERT INTO admin_users (password, first_name, last_name, email, role_id) VALUES (md5('superadmin'), 'Superadmin', '', 'superadmin@yard.com', 1);
INSERT INTO admin_users (password, first_name, last_name, email, role_id) VALUES (md5('miljkovic'), 'Admin', '', 'zupskaavlija@markicevic.com', 2);

INSERT INTO languages (id, country_code, name, is_active) VALUES (1, 'sr_RS', 'Srpski', true);
INSERT INTO languages (id, country_code, name, is_active) VALUES (2, 'en_GB', 'English', true);


INSERT INTO resources (id, name) VALUES (1, 'admin:error');
INSERT INTO resources (id, name) VALUES (2, 'admin:index');
INSERT INTO resources (id, name) VALUES (3, 'admin:auth');
INSERT INTO resources (id, name) VALUES (4, 'admin:admin-users');
INSERT INTO resources (id, name) VALUES (5, 'admin:admin-usersgroups');
INSERT INTO resources (id, name) VALUES (6, 'admin:menu-items');
INSERT INTO resources (id, name) VALUES (7, 'locale:error');
INSERT INTO resources (id, name) VALUES (8, 'locale:countries');
INSERT INTO resources (id, name) VALUES (9, 'locale:languages');
INSERT INTO resources (id, name) VALUES (10, 'locale:translate-keys');
INSERT INTO resources (id, name) VALUES (11, 'locale:translate-messages');
INSERT INTO resources (id, name) VALUES (12, 'cms:error');
INSERT INTO resources (id, name) VALUES (13, 'cms:medias');
INSERT INTO resources (id, name) VALUES (14, 'cms:media-libraries');

INSERT INTO permissions (role_id, resource_id, is_allowed) VALUES (1, 1, true);
INSERT INTO permissions (role_id, resource_id, is_allowed) VALUES (1, 2, true);
INSERT INTO permissions (role_id, resource_id, is_allowed) VALUES (1, 3, true);
INSERT INTO permissions (role_id, resource_id, is_allowed) VALUES (1, 4, true);
INSERT INTO permissions (role_id, resource_id, is_allowed) VALUES (1, 5, true);
INSERT INTO permissions (role_id, resource_id, is_allowed) VALUES (1, 6, true);
INSERT INTO permissions (role_id, resource_id, is_allowed) VALUES (1, 7, true);
INSERT INTO permissions (role_id, resource_id, is_allowed) VALUES (1, 8, true);
INSERT INTO permissions (role_id, resource_id, is_allowed) VALUES (1, 9, true);
INSERT INTO permissions (role_id, resource_id, is_allowed) VALUES (1, 10, true);
INSERT INTO permissions (role_id, resource_id, is_allowed) VALUES (1, 11, true);
INSERT INTO permissions (role_id, resource_id, is_allowed) VALUES (1, 12, true);
INSERT INTO permissions (role_id, resource_id, is_allowed) VALUES (1, 13, true);
INSERT INTO permissions (role_id, resource_id, is_allowed) VALUES (1, 14, true);
INSERT INTO permissions (role_id, resource_id, is_allowed) VALUES (2, 1, true);
INSERT INTO permissions (role_id, resource_id, is_allowed) VALUES (2, 2, true);
INSERT INTO permissions (role_id, resource_id, is_allowed) VALUES (2, 3, true);
INSERT INTO permissions (role_id, resource_id, is_allowed) VALUES (2, 7, true);
INSERT INTO permissions (role_id, resource_id, is_allowed) VALUES (2, 12, true);
INSERT INTO permissions (role_id, resource_id, is_allowed) VALUES (2, 13, true);
INSERT INTO permissions (role_id, resource_id, action, is_allowed) VALUES (2, 4, 'index', false);
INSERT INTO permissions (role_id, resource_id, action, is_allowed) VALUES (2, 4, 'edit', true);
INSERT INTO permissions (role_id, resource_id, action, is_allowed) VALUES (2, 4, 'delete', false);
INSERT INTO permissions (role_id, resource_id, action, is_allowed) VALUES (2, 4, 'new', false);
INSERT INTO permissions (role_id, resource_id, action, is_allowed) VALUES (2, 11, 'index', true);
INSERT INTO permissions (role_id, resource_id, action, is_allowed) VALUES (2, 11, 'edit', true);
INSERT INTO permissions (role_id, resource_id, action, is_allowed) VALUES (2, 11, 'download-csv', false);
INSERT INTO permissions (role_id, resource_id, action, is_allowed) VALUES (2, 11, 'upload', false);
INSERT INTO permissions (role_id, resource_id, action, is_allowed) VALUES (2, 11, 'show', false);
INSERT INTO permissions (role_id, resource_id, action, is_allowed) VALUES (2, 11, 'delete', false);
INSERT INTO permissions (role_id, resource_id, action, is_allowed) VALUES (2, 11, 'new', true);
INSERT INTO permissions (role_id, resource_id, action, is_allowed) VALUES (2, 14, 'index', true);
INSERT INTO permissions (role_id, resource_id, action, is_allowed) VALUES (2, 14, 'edit', false);
INSERT INTO permissions (role_id, resource_id, action, is_allowed) VALUES (2, 14, 'delete', false);
INSERT INTO permissions (role_id, resource_id, action, is_allowed) VALUES (2, 14, 'new', false);

INSERT INTO admin_menu_items (id, name, title, module, controller, action, is_active, order_id, icon_id, parent_id) 
                                VALUES (1,'settings','Main Menu','','','',true,2, null, null);
INSERT INTO admin_menu_items (id, name, title, module, controller, action, is_active, order_id, icon_id, parent_id) 
                                VALUES (2,'backoffice_users','Admin Users','admin','admin-users','index',true,2, 14, 1);
INSERT INTO admin_menu_items (id, name, title, module, controller, action, is_active, order_id, icon_id, parent_id) 
                                VALUES (3,'translate','Translate','locale','translate-messages','index',true,3, 53, 1);
INSERT INTO admin_menu_items (id, name, title, module, controller, action, is_active, order_id, icon_id, parent_id) 
                                VALUES (4,'media-libraries','Gallery','cms','media-libraries','index',true,4, 66, 1);

INSERT INTO roles_admin_menu_items (role_id, admin_menu_item_id) VALUES (1,1);
INSERT INTO roles_admin_menu_items (role_id, admin_menu_item_id) VALUES (1,2); 
INSERT INTO roles_admin_menu_items (role_id, admin_menu_item_id) VALUES (1,3); 
INSERT INTO roles_admin_menu_items (role_id, admin_menu_item_id) VALUES (1,4);
INSERT INTO roles_admin_menu_items (role_id, admin_menu_item_id) VALUES (2,1); 
INSERT INTO roles_admin_menu_items (role_id, admin_menu_item_id) VALUES (2,3);                                 
INSERT INTO roles_admin_menu_items (role_id, admin_menu_item_id) VALUES (2,4);

insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-asterisk');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-plus');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-euro');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-minus');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-cloud');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-envelope');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-pencil');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-glass');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-music');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-search');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-heart');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-star');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-star-empty');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-user');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-film');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-th-large');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-th');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-th-list');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-ok');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-remove');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-zoom-in');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-zoom-out');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-off');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-signal');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-cog');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-trash');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-home');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-file');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-time');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-road');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-download-alt');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-download');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-upload');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-inbox');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-play-circle');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-repeat');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-refresh');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-list-alt');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-lock');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-flag');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-headphones');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-volume-off');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-volume-down');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-volume-up');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-qrcode');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-barcode');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-tag');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-tags');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-book');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-bookmark');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-print');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-camera');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-font');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-bold');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-italic');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-text-height');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-text-width');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-align-left');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-align-center');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-align-right');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-align-justify');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-list');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-indent-left');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-indent-right');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-facetime-video');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-picture');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-map-marker');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-adjust');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-tint');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-edit');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-share');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-check');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-move');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-step-backward');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-fast-backward');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-backward');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-play');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-pause');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-stop');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-forward');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-fast-forward');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-step-forward');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-eject');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-chevron-left');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-chevron-right');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-plus-sign');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-minus-sign');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-remove-sign');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-ok-sign');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-question-sign');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-info-sign');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-screenshot');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-remove-circle');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-ok-circle');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-ban-circle');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-arrow-left');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-arrow-right');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-arrow-up');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-arrow-down');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-share-alt');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-resize-full');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-resize-small');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-exclamation-sign');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-gift');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-leaf');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-fire');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-eye-open');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-eye-close');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-warning-sign');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-plane');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-calendar');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-random');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-comment');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-magnet');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-chevron-up');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-chevron-down');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-retweet');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-shopping-cart');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-folder-close');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-folder-open');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-resize-vertical');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-resize-horizontal');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-hdd');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-bullhorn');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-bell');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-certificate');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-thumbs-up');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-thumbs-down');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-hand-right');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-hand-left');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-hand-up');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-hand-down');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-circle-arrow-right');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-circle-arrow-left');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-circle-arrow-up');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-circle-arrow-down');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-globe');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-wrench');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-tasks');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-filter');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-briefcase');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-fullscreen');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-dashboard');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-paperclip');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-heart-empty');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-link');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-phone');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-pushpin');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-usd');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-gbp');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-sort');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-sort-by-alphabet');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-sort-by-alphabet-alt');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-sort-by-order');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-sort-by-order-alt');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-sort-by-attributes');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-sort-by-attributes-alt');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-unchecked');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-expand');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-collapse-down');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-collapse-up');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-log-in');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-flash');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-log-out');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-new-window');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-record');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-save');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-open');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-saved');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-import');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-export');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-send');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-floppy-disk');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-floppy-saved');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-floppy-remove');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-floppy-save');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-floppy-open');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-credit-card');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-transfer');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-cutlery');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-header');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-compressed');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-earphone');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-phone-alt');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-tower');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-stats');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-sd-video');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-hd-video');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-subtitles');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-sound-stereo');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-sound-dolby');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-sound-5-1');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-sound-6-1');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-sound-7-1');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-copyright-mark');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-registration-mark');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-cloud-download');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-cloud-upload');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-tree-conifer');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-tree-deciduous');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-cd');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-save-file');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-open-file');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-level-up');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-copy');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-paste');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-alert');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-equalizer');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-king');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-queen');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-pawn');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-bishop');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-knight');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-baby-formula');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-tent');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-blackboard');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-bed');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-apple');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-erase');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-hourglass');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-lamp');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-duplicate');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-piggy-bank');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-scissors');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-bitcoin');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-yen');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-ruble');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-scale');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-ice-lolly');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-ice-lolly-tasted');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-education');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-option-horizontal');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-option-vertical');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-menu-hamburger');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-modal-window');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-oil');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-grain');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-sunglasses');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-text-size');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-text-color');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-text-background');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-object-align-top');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-object-align-bottom');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-object-align-horizontal');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-object-align-left');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-object-align-vertical');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-object-align-right');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-triangle-right');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-triangle-left');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-triangle-bottom');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-triangle-top');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-console');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-superscript');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-subscript');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-menu-left');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-menu-right');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-menu-down');
insert into menu_items_icons (icon) VALUES ('glyphicon glyphicon-menu-up');