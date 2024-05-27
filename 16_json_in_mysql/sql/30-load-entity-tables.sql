# Сотрудник ООО «Розничные продажи»
INSERT INTO employee
  (id, tenant_id, profile)
VALUES (1, 1, '{
  "first_name": "Пётр",
  "last_name": "Мелехов",
  "emails": [
    "p.melehov@trade.local",
    "petr.melehov@trade.local"
  ],
  "phones": [
    "+7-861-202-02-02",
    "+7-861-202-03-03"
  ],
  "manager_name": "Мирон Коршунов"
}')
;

# Сотрудник ООО «Застройщик»
INSERT INTO employee
  (id, tenant_id, profile)
VALUES (2, 2, '{
  "first_name": "Пётр",
  "last_name": "Мелехов",
  "phone": "+7-861-202-02-02",
  "job_title": "Слесарь",
  "hired_at": "2021-01-07"
}')
;

SELECT * FROM employee;
