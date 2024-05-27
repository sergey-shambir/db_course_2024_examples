# Чтение информации о полях профиля
SELECT
  code,
  type,
  name,
  is_unique
FROM profile_field
WHERE
  tenant_id = 1;

# Чтение экранированных значений
SELECT
  id,
  profile -> '$.first_name' AS first_name,
  profile -> '$.last_name' AS last_name,
  profile -> '$.emails' AS emails,
  profile -> '$.phones' AS phones
FROM employee
WHERE
  tenant_id = 1
;

# Чтение значений без экранирования
SELECT
  id,
  profile ->> '$.first_name' AS first_name,
  profile ->> '$.last_name' AS last_name,
  profile -> '$.emails' AS emails,
  profile -> '$.phones' AS phones
FROM employee
WHERE
  tenant_id = 1
;

# Чтение значений с помощью JSON-функций
SELECT
  id,
  JSON_UNQUOTE(
    JSON_EXTRACT(profile, '$.first_name')
  ) AS first_name,
  JSON_UNQUOTE(
    JSON_EXTRACT(profile, '$.last_name')
  ) AS last_name,
  JSON_EXTRACT(profile, '$.emails') AS emails,
  JSON_EXTRACT(profile, '$.phones') AS phones
FROM employee
WHERE
  tenant_id = 1
;

# Поиск по имени
SELECT *
FROM employee
WHERE
  profile ->> '$.first_name' = 'Пётр'
;

# Поиск номера в массиве phone
SELECT *
FROM employee
WHERE JSON_CONTAINS(
  profile,
  JSON_QUOTE('+7-861-202-03-03'),
  '$.phones'
);


# Поиск номеров в массиве phone
SELECT *
FROM employee
WHERE
  JSON_OVERLAPS(
    profile -> '$.phones',
    '[
      "+7-861-202-02-02",
      "+7-861-202-03-03"
    ]'
  )
;