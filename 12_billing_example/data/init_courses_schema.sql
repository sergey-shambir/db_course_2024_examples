USE lecture_12;

CREATE TABLE course (
  id INT UNSIGNED AUTO_INCREMENT,
  title VARCHAR(200) NOT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE learner (
  id INT UNSIGNED AUTO_INCREMENT,
  name VARCHAR(200) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT NOW(),
  PRIMARY KEY (id)
);

CREATE TABLE course_group (
  id INT UNSIGNED AUTO_INCREMENT,
  course_id INT UNSIGNED,
  title VARCHAR(200) NOT NULL,
  capacity INT UNSIGNED NOT NULL,
  start_date DATETIME NOT NULL,
  end_date DATETIME NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT course_group_course_id_fk
    FOREIGN KEY (course_id)
      REFERENCES course (id)
      ON UPDATE RESTRICT
      ON DELETE RESTRICT,
  CONSTRAINT end_date_check
    CHECK (start_date < end_date)
);

CREATE TABLE course_group_participant (
  learner_id INT UNSIGNED NOT NULL,
  course_group_id INT UNSIGNED NOT NULL,
  registered_at DATETIME NOT NULL DEFAULT NOW(),
  PRIMARY KEY (learner_id, course_group_id),
  CONSTRAINT course_group_participant_learner_id_fk
    FOREIGN KEY (learner_id)
      REFERENCES learner (id)
      ON UPDATE RESTRICT
      ON DELETE RESTRICT,
  CONSTRAINT course_group_participant_course_group_id_fk
    FOREIGN KEY (course_group_id)
      REFERENCES course_group (id)
      ON UPDATE RESTRICT
      ON DELETE RESTRICT
);
