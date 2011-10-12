CREATE TABLE IF NOT EXISTS users (
	users_id smallint unsigned auto_increment primary key,
	name varchar(20) not null,
	password char(32),
	email varchar(35)
) ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS categories (
	categories_id smallint unsigned auto_increment primary key,
	name tinytext not null
) ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS tasks (
	tasks_id int unsigned auto_increment primary key,
	created date not null,
	created_by smallint unsigned,
	priority tinyint unsigned not null default 0,
	deadline datetime,
	status tinyint unsigned not null default 1,
	categories_id1 smallint unsigned,
	categories_id2 smallint unsigned,
	name tinytext not null,
	index (categories_id1),
	foreign key (categories_id1) references categories(categories_id1)
		on delete no action,
	index (categories_id2),
	foreign key (categories_id2) references categories(categories_id2)
		on delete no action,
	index (created_by),
	foreign key (created_by) references users(users_id)
		on delete no action
) ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS tasks_info (
	tasks_info_id int unsigned auto_increment primary key,
	tasks_id int unsigned,
	users_id smallint unsigned,
	updated timestamp,
	body text,
	index (tasks_id),
	foreign key (tasks_id) references tasks(tasks_id) on delete cascade,
	index (users_id),
	foreign key (users_id) references users(users_id) on delete no action
) ENGINE=INNODB;


DROP FUNCTION IF EXISTS auth_user;
DELIMITER $$
CREATE FUNCTION auth_user (users_name VARCHAR(20), provided_password VARCHAR(35))
RETURNS TINYINT UNSIGNED
DETERMINISTIC
BEGIN
	DECLARE md5_password CHAR(32);
	DECLARE res TINYINT UNSIGNED DEFAULT 0;

	SELECT password INTO md5_password FROM users WHERE name=users_name;
	
	IF MD5(provided_password) = md5_password THEN SET res=1;
	END IF;
	
	RETURN res;
END $$
DELIMITER ;


DROP PROCEDURE IF EXISTS s_create;
DELIMITER $$
CREATE PROCEDURE s_create (category_name tinytext, task_name tinytext, task_body text)
BEGIN
	DECLARE l_category_id SMALLINT UNSIGNED;
	DECLARE l_task_id INT UNSIGNED;

	SELECT categories_id INTO l_category_id FROM categories WHERE name=category_name;

	INSERT INTO tasks (created, created_by, deadline, categories_id1, name) VALUES (NOW(), 1, NOW() + INTERVAL 1 DAY, l_category_id, task_name);

	SELECT LAST_INSERT_ID() INTO l_task_id;

	INSERT INTO tasks_info (tasks_id, users_id, body) VALUES (l_task_id, 1, task_body);

	SELECT l_task_id;
END $$
DELIMITER ;



CREATE OR REPLACE VIEW task_list AS SELECT
	t.tasks_id AS task,
	t.status AS status,
	DATE_FORMAT(t.deadline,'%c/%d') as due,
	c.name as category,
	t.name as title,
	(SELECT body FROM tasks_info WHERE tasks_info.tasks_id = task ORDER BY tasks_info_id DESC LIMIT 1) AS recent
	FROM tasks t
	INNER JOIN categories c ON t.categories_id1 = c.categories_id
	-- WHERE t.status > 0
	ORDER BY t.priority DESC, t.deadline ASC;

CREATE OR REPLACE VIEW daily_history AS SELECT 
  TIME(updated) AS time,
  REPLACE(REPLACE(SUBSTR(body,1,64),'\r',''),'\n','') AS snippet
  FROM tasks_info
  WHERE DATE(updated)=CURRENT_DATE;

DROP PROCEDURE IF EXISTS task_report;
DELIMITER $$
CREATE PROCEDURE task_report (status_level SMALLINT UNSIGNED)
BEGIN
	SELECT * FROM task_list WHERE status = status_level;
END $$
DELIMITER ;

DROP PROCEDURE IF EXISTS task_history;
DELIMITER $$
CREATE PROCEDURE task_history (tasknumber int unsigned)
BEGIN
	SELECT i.updated, i.body FROM tasks_info i WHERE tasks_id=tasknumber ORDER BY tasks_info_id ASC;
END $$
DELIMITER ;


DROP PROCEDURE IF EXISTS s_update;
DELIMITER $$
CREATE PROCEDURE s_update (tasknum INT UNSIGNED, details text)
BEGIN
	INSERT INTO tasks_info (tasks_id, users_id, body) VALUES (tasknum, 1, details);
	SELECT * FROM task_list WHERE task=tasknum;
END $$
DELIMITER ;


