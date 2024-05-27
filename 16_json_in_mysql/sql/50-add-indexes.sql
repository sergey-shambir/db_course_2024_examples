# Функциональный индекс по однозначному атрибуту
ALTER TABLE employee
  ADD INDEX profile_first_name_idx
    ((CAST(profile ->> '$.first_name' AS CHAR(100))))
;

# Функциональный индекс по многозначному атрибуту
ALTER TABLE employee
  ADD INDEX profile_phones_idx
    ((CAST(profile -> '$.phones' AS CHAR(50) array)))
;

# Генерируемая колонка + индекс
# Решение для MySQL 5.7, где нет функциональных индексов
ALTER TABLE employee
  ADD COLUMN first_name VARCHAR(100)
    GENERATED ALWAYS AS (profile ->> '$.first_name'),
  ADD INDEX first_name_idx (first_name)
;
