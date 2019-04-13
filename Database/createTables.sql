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
      DROP TABLE IF EXISTS agtodi_interactions;


/* Using agtodi prefix in case tables are loaded into a database
   with similar table names. */
CREATE TABLE agtodi_users (
	    id BIGINT NOT NULL AUTO_INCREMENT,
      email VARCHAR(50) NOT NULL,
      password VARCHAR(255) NOT NULL,
      firstName VARCHAR(20) NOT NULL,
      lastName VARCHAR(20) NOT NULL,
      tier INT DEFAULT 0,
      trollCount INT DEFAULT 0,
      isAdmin TINYINT DEFAULT 0,
      PRIMARY KEY(id)
) ENGINE=INNODB;

CREATE TABLE agtodi_threads (
      id INT NOT NULL AUTO_INCREMENT,
      creationDate DATETIME DEFAULT CURRENT_TIMESTAMP,
      title VARCHAR(255) NOT NULL,
      creatorId BIGINT NOT NULL,
      PRIMARY KEY (id),
      FOREIGN KEY (creatorId) REFERENCES agtodi_users(id) ON DELETE RESTRICT
) ENGINE=INNODB;

CREATE TABLE agtodi_topics (
      id INT NOT NULL AUTO_INCREMENT,
      threadId INT NOT NULL,
      firstPostId BIGINT,
      creationDate DATETIME DEFAULT CURRENT_TIMESTAMP,
      title VARCHAR(255) NOT NULL,
      PRIMARY KEY (id),
      FOREIGN KEY (threadId) REFERENCES agtodi_threads(id) ON DELETE RESTRICT,
      FOREIGN KEY (firstPostId) REFERENCES agtodi_posts(id) ON DELETE RESTRICT
) ENGINE=INNODB;

CREATE TABLE agtodi_posts (
      id BIGINT NOT NULL AUTO_INCREMENT,
      topicId INT NOT NULL,
      creatorId BIGINT NOT NULL,
      post TEXT NOT NULL,
      creationDate DATETIME DEFAULT CURRENT_TIMESTAMP,
      likes INT,
      dislikes INT,
      trolls INT,
      isReply BIGINT,
      PRIMARY KEY (id),
      FOREIGN KEY (topicId) REFERENCES agtodi_topics(id) ON DELETE RESTRICT,
      FOREIGN KEY (creatorId) REFERENCES agtodi_users(id) ON DELETE RESTRICT,
      FOREIGN KEY (isReply) REFERENCES agtodi_posts(id) ON DELETE RESTRICT
) ENGINE=INNODB;

CREATE TABLE agtodi_interactions (
  postId BIGINT NOT NULL,
  creatorId BIGINT NOT NULL,
  isLike TINYINT DEFAULT 0,
  isDislike TINYINT DEFAULT 0,
  isTroll TINYINT DEFAULT 0,
  PRIMARY KEY (postId, creatorId),
  FOREIGN KEY (postId) REFERENCES agtodi_posts(id) ON DELETE RESTRICT,
  FOREIGN KEY (creatorId) REFERENCES agtodi_users(id) ON DELETE RESTRICT
) ENGINE=INNODB;

/* Sets foreign key checks back ON. */
SET FOREIGN_KEY_CHECKS=1;

SELECT
  a.id,
  a.creatorId,
  a.post,
  a.creationDate,
  a.likes,
  a.dislikes,
  a.trolls,
  a.isReply,
  b.firstName,
  b.lastName,
  c.isLike,
  c.isDislike,
  c.isTroll
FROM
  agtodi_posts a WHERE topicId = ?
    JOIN agtodi_users b ON a.creatorId = b.id
    JOIN agtodi_interactions c ON c.creatorId = ?











