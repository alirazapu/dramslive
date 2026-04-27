/*VERY VERY Important */

CREATE TABLE person_firstname_backup_2026_full AS
SELECT person_id, first_name
FROM person;


UPDATE person
SET first_name = ucwords(
    TRIM(
        REGEXP_REPLACE(
            TRIM(first_name),
            '^(?i)person[[:space:]]+',
            ''
        )
    )
)
WHERE first_name REGEXP '^(?i)person[[:space:]]';

UPDATE person
SET last_name = ucwords(
    TRIM(
        REGEXP_REPLACE(
            TRIM(last_name),
            '^(?i)person[[:space:]]+',
            ''
        )
    )
)
WHERE last_name REGEXP '^(?i)person[[:space:]]';


UPDATE person
SET last_name = ucwords(
        TRIM(
            REGEXP_REPLACE(
                TRIM(last_name),
                '(?i)\\b(prepaid|postpaid)\\b',
                ''
            )
        )
    )
WHERE last_name REGEXP '(?i)\\b(prepaid|postpaid)\\b';


	
UPDATE person
SET 
    first_name = ucwords(TRIM(SUBSTRING_INDEX(first_name, ' ', 1))),
    
    last_name = ucwords(
        TRIM(
            CONCAT(
                COALESCE(TRIM(last_name), ''),
                CASE WHEN TRIM(last_name) != '' THEN ' ' ELSE '' END,
                TRIM(SUBSTRING_INDEX(first_name, ' ', -1))
            )
        )
    )
WHERE 
    first_name REGEXP '^[^[:space:]]+[[:space:]]+[^[:space:]]+$'
    AND NOT first_name REGEXP '([[:space:]][^[:space:]]+){2,}'
    
    -- Skip initial/title patterns
    AND first_name NOT REGEXP '^(?i)(M\\.?|Md\\.?|Mohd\\.?|Muhammad|Mohammed|Mohammad|Muhammed)\\s+[^[:space:]]+$'
    
    AND (
        last_name IS NULL 
        OR TRIM(last_name) = '' 
    );

/* -----------------------------------------------------------
   Strip Mobilink parser trailer from address.
   Some addresses were persisted as
       "<real address> <ISO-DOB><email-timestamp><sender-email>
        <originating MSISDN>fir <id>"
   because the new-format Mobilink response had no comma between
   the address and the trailer. Cut from the first datetime marker.
   Backup first so this can be reversed if something looks wrong.
   ----------------------------------------------------------- */
CREATE TABLE IF NOT EXISTS person_address_backup_2026_full AS
SELECT person_id, address
FROM person
WHERE address REGEXP
      '[[:space:]]+([0-9]{4}-[0-9]{1,2}-[0-9]{1,2}[Tt]|[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}[[:space:]]+[0-9]{1,2}:[0-9]{2})';

UPDATE person
SET address = NULLIF(
    TRIM(
        REGEXP_REPLACE(
            address,
            '[[:space:]]+([0-9]{4}-[0-9]{1,2}-[0-9]{1,2}[Tt]|[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}[[:space:]]+[0-9]{1,2}:[0-9]{2}).*$',
            ''
        )
    ),
    ''
)
WHERE address REGEXP
      '[[:space:]]+([0-9]{4}-[0-9]{1,2}-[0-9]{1,2}[Tt]|[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}[[:space:]]+[0-9]{1,2}:[0-9]{2})';

UPDATE person
SET address = ucwords(address)
WHERE address!=ucwords(address);

UPDATE person
SET last_name = ucwords(last_name);

UPDATE person
SET first_name = ucwords(first_name);
