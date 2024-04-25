USE catboard;

CREATE TABLE image (
  id INT UNSIGNED AUTO_INCREMENT,
  path VARCHAR(300) NOT NULL,
  width INT UNSIGNED NOT NULL,
  height INT UNSIGNED NOT NULL,
  mime_type VARCHAR(50) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE unique_path (path)
);

CREATE TABLE post (
  id INT UNSIGNED AUTO_INCREMENT,
  image_id INT UNSIGNED NOT NULL,
  description VARCHAR(300) NOT NULL,
  author_name VARCHAR(100) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT NOW(),
  PRIMARY KEY (id),
  CONSTRAINT post_image_id_fk
    FOREIGN KEY (image_id)
      REFERENCES image (id)
      ON DELETE RESTRICT
      ON UPDATE RESTRICT
);
