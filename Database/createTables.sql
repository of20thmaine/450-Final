/**
* Team Adequate CSC-450 Project
* Database initialization instructions.
* Execute with source command.
*/

/* CHANGE NAME IF USING A DIFFERENT DATABASE;
   ELSE: Create a database (command line or with
   PHPMyAdmin GUI) called agtodi_db */
USE agtodi_db;

/* Lets us wipe old tables without foreign key errors */
SET FOREIGN_KEY_CHECKS=0;
      DROP TABLE IF EXISTS agtodi_users;
      DROP TABLE IF EXISTS agtodi_admins;
      DROP TABLE IF EXISTS agtodi_threads;
      DROP TABLE IF EXISTS agtodi_topics;
      DROP TABLE IF EXISTS agtodi_posts;


/* Using agtodi prefix in case tables are loaded into a database
   with similar table names. */
CREATE TABLE agtodi_users (
	  id INT NOT NULL AUTO_INCREMENT,
      email VARCHAR(50) NOT NULL,
      password VARCHAR(255) NOT NULL,
      firstName VARCHAR(20) NOT NULL,
      lastName VARCHAR(20) NOT NULL,
      tier INT NOT NULL,
      PRIMARY KEY(id)
) ENGINE=INNODB;

CREATE TABLE agtodi_admins (
	  id INT NOT NULL AUTO_INCREMENT,
      email VARCHAR(50) NOT NULL,
      password VARCHAR(255) NOT NULL,
      firstName VARCHAR(20) NOT NULL,
      lastName VARCHAR(20) NOT NULL,
      PRIMARY KEY(id)
) ENGINE=INNODB;

CREATE TABLE agtodi_threads (
      id INT NOT NULL AUTO_INCREMENT,
      creationDate DATETIME DEFAULT CURRENT_TIMESTAMP,
      title VARCHAR(255) NOT NULL,
      creatorId VARCHAR(50),
      PRIMARY KEY (id),
      FOREIGN KEY (creatorId) REFERENCES agtodi_admins(email) ON DELETE SET NULL
) ENGINE=INNODB;

CREATE TABLE agtodi_topics (
      id INT NOT NULL AUTO_INCREMENT,
      threadId INT NOT NULL,
      firstPostId BIGINT NOT NULL,
      creationDate DATETIME DEFAULT CURRENT_TIMESTAMP,
      title VARCHAR(255) NOT NULL,
      PRIMARY KEY (id),
      FOREIGN KEY (threadId) REFERENCES agtodi_threads(id) ON DELETE RESTRICT,
      FOREIGN KEY (firstPostId) REFERENCES agtodi_posts(id) ON DELETE RESTRICT
) ENGINE=INNODB;

CREATE TABLE agtodi_posts (
      id BIGINT NOT NULL AUTO_INCREMENT,
      topicId INT NOT NULL,
      creatorId VARCHAR(50),
      post TEXT NOT NULL,
      creationDate DATETIME DEFAULT CURRENT_TIMESTAMP,
      likes INT,
      dislikes INT,
      trolls INT,
      isReply BIGINT,
      PRIMARY KEY (id),
      FOREIGN KEY (topicId) REFERENCES agtodi_topics(id) ON DELETE RESTRICT,
      FOREIGN KEY (creatorId) REFERENCES agtodi_users(email) ON DELETE SET NULL,
      FOREIGN KEY (isReply) REFERENCES agtodi_posts(id) ON DELETE RESTRICT
) ENGINE=INNODB;

/* Sets foreign key checks back ON. */
SET FOREIGN_KEY_CHECKS=1;
