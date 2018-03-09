CREATE DATABASE IF NOT EXISTS dbproject_app;
USE dbproject_app;
# -----------------------------------------------------------------------------
#       TABLE : UTILISATEUR
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS UTILISATEUR
 (
   IDUSER INTEGER NOT NULL  AUTO_INCREMENT,
   USERNAME CHAR(32) NULL  ,
   NAME CHAR(32) NULL  ,
   DATEINS DATETIME DEFAULT CURRENT_TIMESTAMP  ,
   ADRESSEML CHAR(32) NULL  ,
   MOTPASS CHAR(32) NULL  ,
   AVATAR char(140) NULL
   , PRIMARY KEY (IDUSER)
 )
 comment = "";

# -----------------------------------------------------------------------------
#       TABLE : TWEET
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS TWEET
 (
   IDTWEET INTEGER NOT NULL  AUTO_INCREMENT,
   IDUSER INTEGER NOT NULL  ,
   IDTWEET_REPONDRE INTEGER NULL  ,
   MICROMES CHAR(140) NULL  ,
   DATEPUB DATETIME DEFAULT CURRENT_TIMESTAMP
   , PRIMARY KEY (IDTWEET)
 )
 comment = "";

# -----------------------------------------------------------------------------
#       TABLE : HASHTAG
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS HASHTAG
 (
   TAGNAME CHAR(32) NOT NULL  ,
   DATEUTIL DATETIME DEFAULT CURRENT_TIMESTAMP
   , PRIMARY KEY (TAGNAME)
 )
 comment = "";

# -----------------------------------------------------------------------------
#       TABLE : SUIVRE
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS SUIVRE
 (
   IDUSERSUIT INTEGER NOT NULL  ,
   IDUSERFAN INTEGER NOT NULL  ,
   DATESUIT DATETIME DEFAULT CURRENT_TIMESTAMP  ,
   LUSUIT DATETIME DEFAULT NULL
   , PRIMARY KEY (IDUSERSUIT,IDUSERFAN)
 )
 comment = "";

# -----------------------------------------------------------------------------
#       TABLE : MENTIONNER
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS MENTIONNER
 (
   IDUSER INTEGER NOT NULL  ,
   IDTWEET INTEGER NOT NULL  ,
   LUMENT DATETIME DEFAULT NULL
   , PRIMARY KEY (IDUSER,IDTWEET)
 )
 comment = "";

# -----------------------------------------------------------------------------
#       TABLE : CONCERNER
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS CONCERNER
 (
   IDTWEET INTEGER NOT NULL  ,
   TAGNAME CHAR(32) NOT NULL
   , PRIMARY KEY (IDTWEET,TAGNAME)
 )
 comment = "";

# -----------------------------------------------------------------------------
#       TABLE : AIMER
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS AIMER
 (
   IDUSER INTEGER NOT NULL  ,
   IDTWEET INTEGER NOT NULL  ,
   DATEAIME DATETIME DEFAULT CURRENT_TIMESTAMP ,
   LUAIME DATETIME DEFAULT NULL
   , PRIMARY KEY (IDUSER,IDTWEET)
 )
 comment = "";


# -----------------------------------------------------------------------------
#       CREATION DES REFERENCES DE TABLE
# -----------------------------------------------------------------------------


ALTER TABLE TWEET
  ADD FOREIGN KEY FK_TWEET_UTILISATEUR (IDUSER)
      REFERENCES UTILISATEUR (IDUSER) ;


ALTER TABLE TWEET
  ADD FOREIGN KEY FK_TWEET_TWEET (IDTWEET_REPONDRE)
      REFERENCES TWEET (IDTWEET) ;


ALTER TABLE SUIVRE
  ADD FOREIGN KEY FK_SUIVRE_UTILISATEUR (IDUSERSUIT)
      REFERENCES UTILISATEUR (IDUSER) ;


ALTER TABLE SUIVRE
  ADD FOREIGN KEY FK_SUIVRE_UTILISATEUR1 (IDUSERFAN)
      REFERENCES UTILISATEUR (IDUSER) ;


ALTER TABLE MENTIONNER
  ADD FOREIGN KEY FK_MENTIONNER_UTILISATEUR (IDUSER)
      REFERENCES UTILISATEUR (IDUSER) ;


ALTER TABLE MENTIONNER
  ADD FOREIGN KEY FK_MENTIONNER_TWEET (IDTWEET)
      REFERENCES TWEET (IDTWEET) ;


ALTER TABLE CONCERNER
  ADD FOREIGN KEY FK_CONCERNER_TWEET (IDTWEET)
      REFERENCES TWEET (IDTWEET) ;


ALTER TABLE CONCERNER
  ADD FOREIGN KEY FK_CONCERNER_HASHTAG (TAGNAME)
      REFERENCES HASHTAG (TAGNAME) ;


ALTER TABLE AIMER
  ADD FOREIGN KEY FK_AIMER_UTILISATEUR (IDUSER)
      REFERENCES UTILISATEUR (IDUSER) ;


ALTER TABLE AIMER
  ADD FOREIGN KEY FK_AIMER_TWEET (IDTWEET)
      REFERENCES TWEET (IDTWEET) ;
