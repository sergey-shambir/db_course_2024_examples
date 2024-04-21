USE lecture_12;

CREATE TABLE account (
  phone VARCHAR(20) NOT NULL,
  balance DECIMAL(20, 2) UNSIGNED,
  sms_count INT UNSIGNED NOT NULL,
  minutes_count INT UNSIGNED NOT NULL,
  PRIMARY KEY (phone)
);

CREATE TABLE billing_plan (
  id INT UNSIGNED,
  title VARCHAR(200) NOT NULL,
  sms_count INT UNSIGNED NOT NULL,
  minutes_count INT UNSIGNED NOT NULL,
  base_price DECIMAL(20, 2) NOT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE account_billing_period (
  phone VARCHAR(20) NOT NULL,
  start_date DATETIME NOT NULL,
  end_date DATETIME,
  billing_plan_id INT UNSIGNED NOT NULL,
  price DECIMAL(20, 2) NOT NULL,
  PRIMARY KEY (phone, start_date),
  CONSTRAINT account_billing_period_phone_fk
    FOREIGN KEY (phone)
      REFERENCES account(phone)
      ON UPDATE RESTRICT
      ON DELETE RESTRICT,
  CONSTRAINT account_billing_period_billing_plan_id
    FOREIGN KEY (billing_plan_id)
      REFERENCES billing_plan(id)
      ON UPDATE RESTRICT
      ON DELETE RESTRICT
);

CREATE TABLE account_balance_transfer (
  id INT UNSIGNED AUTO_INCREMENT,
  from_phone VARCHAR(20) NOT NULL,
  to_phone VARCHAR(20) NOT NULL,
  amount DECIMAL(20, 2) UNSIGNED NOT NULL,
  datetime DATETIME NOT NULL DEFAULT NOW(),
  PRIMARY KEY (id),
  CONSTRAINT account_balance_transfer_from_phone_fk
    FOREIGN KEY (from_phone)
      REFERENCES account(phone)
      ON UPDATE RESTRICT
      ON DELETE RESTRICT,
  CONSTRAINT account_balance_transfer_to_phone_fk
    FOREIGN KEY (to_phone)
      REFERENCES account(phone)
      ON UPDATE RESTRICT
      ON DELETE RESTRICT
);
