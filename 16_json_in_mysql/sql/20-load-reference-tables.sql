INSERT INTO tenant
  (id, name)
VALUES (1, 'ООО «Розничные продажи»'),
       (2, 'ООО «Застройщик»')
;

SELECT *
FROM tenant
;

# Поля профиля для ООО «Розничные продажи»
INSERT INTO profile_field
  (tenant_id, code, type, name, is_unique)
VALUES (1, 'first_name', 'text', 'Имя', TRUE),
       (1, 'last_name', 'text', 'Фамилия', TRUE),
       (1, 'emails', 'email', 'Email', FALSE),
       (1, 'phones', 'phone', 'Телефон', FALSE),
       (1, 'manager_name', 'text', 'Менеджер', TRUE)
;

# Поля профиля для ООО «Застройщик»
INSERT INTO profile_field
  (tenant_id, code, type, name, is_unique)
VALUES (2, 'first_name', 'text', 'Имя', TRUE),
       (2, 'last_name', 'text', 'Фамилия', TRUE),
       (2, 'phones', 'phone', 'Телефон', TRUE),
       (2, 'job_title', 'text', 'Должность', TRUE),
       (2, 'hired_at', 'date', 'Дата найма', TRUE)
;

SELECT *
FROM profile_field;
