
SHOW INDEXES FROM employee;

ALTER TABLE employee
  DROP INDEX profile_first_name_idx
;

ALTER TABLE employee
  DROP INDEX profile_phones_idx
;
