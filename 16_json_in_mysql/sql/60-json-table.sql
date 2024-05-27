SELECT
  e.id,
  e.profile->>'$.first_name' AS first_name,
  t.phone
FROM employee e
  INNER JOIN JSON_TABLE(
  profile -> '$.phones',
  '$[*]'
  COLUMNS (
    phone VARCHAR(50) PATH '$' ERROR ON ERROR
    )
             ) AS t
;
