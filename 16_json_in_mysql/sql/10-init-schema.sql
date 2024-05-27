USE json_demo;

CREATE TABLE tenant (
  id INT UNSIGNED AUTO_INCREMENT NOT NULL,
  name VARCHAR(100) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
);

CREATE TABLE employee (
  id INT UNSIGNED AUTO_INCREMENT NOT NULL,
  tenant_id INT UNSIGNED NOT NULL,
  profile JSON NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT employee_tenant_id_fk
    FOREIGN KEY (tenant_id)
      REFERENCES tenant (id)
      ON DELETE CASCADE
      ON UPDATE CASCADE
);

CREATE TABLE profile_field (
  tenant_id INT UNSIGNED NOT NULL,
  code VARCHAR(100) NOT NULL,
  type ENUM ('text', 'email', 'phone', 'date') NOT NULL,
  name VARCHAR(100) NOT NULL,
  is_unique BOOLEAN NOT NULL,
  PRIMARY KEY (tenant_id, code),
  CONSTRAINT profile_field_tenant_id_fk
    FOREIGN KEY (tenant_id)
      REFERENCES tenant (id)
      ON DELETE CASCADE
      ON UPDATE CASCADE
);

