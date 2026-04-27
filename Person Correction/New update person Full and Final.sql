/* =========================================================
   PERSON NAME CLEANUP SCRIPT (PRODUCTION GRADE)
   Safe + Counter-based + Backup + Review
   ========================================================= */

START TRANSACTION;

/* =========================================================
   1) CREATE DYNAMIC BACKUP TABLE
   ========================================================= */
SET @backup_table = CONCAT('person_backup_', DATE_FORMAT(NOW(), '%Y%m%d_%H%i%s'));

SET @sql = CONCAT('
    CREATE TABLE ', @backup_table, ' AS
    SELECT person_id, first_name, last_name, address
    FROM person
');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

/* =========================================================
   2) SHOW BACKUP TABLE NAME
   ========================================================= */
SELECT @backup_table AS backup_table_created;

/* =========================================================
   3) PRE-CHECK COUNTS
   ========================================================= */
SELECT 'PRECHECK - first_name starts with Person' AS step,
       COUNT(*) AS affected_rows
FROM person
WHERE TRIM(COALESCE(first_name, '')) REGEXP '^[Pp][Ee][Rr][Ss][Oo][Nn]([[:space:]]+.*)?$';

SELECT 'PRECHECK - last_name starts with Person' AS step,
       COUNT(*) AS affected_rows
FROM person
WHERE TRIM(COALESCE(last_name, '')) REGEXP '^[Pp][Ee][Rr][Ss][Oo][Nn]([[:space:]]+.*)?$';

SELECT 'PRECHECK - last_name contains prepaid/postpaid' AS step,
       COUNT(*) AS affected_rows
FROM person
WHERE TRIM(COALESCE(last_name, '')) REGEXP '(^|[[:space:]])([Pp][Rr][Ee][Pp][Aa][Ii][Dd]|[Pp][Oo][Ss][Tt][Pp][Aa][Ii][Dd])([[:space:]]|$)';

SELECT 'PRECHECK - first_name = Person and last_name single word' AS step,
       COUNT(*) AS affected_rows
FROM person
WHERE TRIM(COALESCE(first_name, '')) REGEXP '^[Pp][Ee][Rr][Ss][Oo][Nn]$'
  AND TRIM(COALESCE(last_name, '')) REGEXP '^[^[:space:]]+$';

SELECT 'PRECHECK - first_name empty and last_name single word' AS step,
       COUNT(*) AS affected_rows
FROM person
WHERE (first_name IS NULL OR TRIM(first_name) = '')
  AND TRIM(COALESCE(last_name, '')) REGEXP '^[^[:space:]]+$';

/* =========================================================
   4) PREVIEW TARGET RECORDS BEFORE UPDATE
   ========================================================= */
SELECT 
    person_id,
    first_name,
    last_name,
    address
FROM person
WHERE 
    TRIM(COALESCE(first_name, '')) REGEXP '^[Pp][Ee][Rr][Ss][Oo][Nn]([[:space:]]+.*)?$'
    OR TRIM(COALESCE(last_name, '')) REGEXP '^[Pp][Ee][Rr][Ss][Oo][Nn]([[:space:]]+.*)?$'
    OR TRIM(COALESCE(last_name, '')) REGEXP '(^|[[:space:]])([Pp][Rr][Ee][Pp][Aa][Ii][Dd]|[Pp][Oo][Ss][Tt][Pp][Aa][Ii][Dd])([[:space:]]|$)'
ORDER BY person_id;

/* =========================================================
   5) NORMALIZE SPACES IN first_name
   ========================================================= */
UPDATE person
SET first_name = TRIM(
    REGEXP_REPLACE(
        REPLACE(REPLACE(REPLACE(COALESCE(first_name, ''), CHAR(9), ' '), CHAR(10), ' '), CHAR(13), ' '),
        '[[:space:]]+',
        ' '
    )
)
WHERE first_name IS NOT NULL
  AND first_name != TRIM(
        REGEXP_REPLACE(
            REPLACE(REPLACE(REPLACE(COALESCE(first_name, ''), CHAR(9), ' '), CHAR(10), ' '), CHAR(13), ' '),
            '[[:space:]]+',
            ' '
        )
    );

SELECT 'STEP 5 - Normalize first_name spaces' AS step, ROW_COUNT() AS affected_rows;

/* =========================================================
   6) NORMALIZE SPACES IN last_name
   ========================================================= */
UPDATE person
SET last_name = TRIM(
    REGEXP_REPLACE(
        REPLACE(REPLACE(REPLACE(COALESCE(last_name, ''), CHAR(9), ' '), CHAR(10), ' '), CHAR(13), ' '),
        '[[:space:]]+',
        ' '
    )
)
WHERE last_name IS NOT NULL
  AND last_name != TRIM(
        REGEXP_REPLACE(
            REPLACE(REPLACE(REPLACE(COALESCE(last_name, ''), CHAR(9), ' '), CHAR(10), ' '), CHAR(13), ' '),
            '[[:space:]]+',
            ' '
        )
    );

SELECT 'STEP 6 - Normalize last_name spaces' AS step, ROW_COUNT() AS affected_rows;

/* =========================================================
   7) MOVE last_name -> first_name
      WHEN first_name = Person AND last_name = single safe word
   Example:
      Person | Ghazi  -> Ghazi | NULL
   ========================================================= */
UPDATE person
SET 
    first_name = UCWORDS(TRIM(last_name)),
    last_name  = NULL
WHERE TRIM(COALESCE(first_name, '')) REGEXP '^[Pp][Ee][Rr][Ss][Oo][Nn]$'
  AND TRIM(COALESCE(last_name, '')) REGEXP '^[^[:space:]]+$'
  AND TRIM(COALESCE(last_name, '')) NOT REGEXP '^[Pp][Ee][Rr][Ss][Oo][Nn]$'
  AND TRIM(COALESCE(last_name, '')) NOT REGEXP '^[Pp][Rr][Ee][Pp][Aa][Ii][Dd]$'
  AND TRIM(COALESCE(last_name, '')) NOT REGEXP '^[Pp][Oo][Ss][Tt][Pp][Aa][Ii][Dd]$';

SELECT 'STEP 7 - Move last_name into first_name where first_name=Person' AS step, ROW_COUNT() AS affected_rows;

/* =========================================================
   8) REMOVE "Person " PREFIX FROM first_name
   Example:
      Person Ahmed -> Ahmed
   ========================================================= */
UPDATE person
SET first_name = NULLIF(
    UCWORDS(
        TRIM(
            REGEXP_REPLACE(first_name, '^[Pp][Ee][Rr][Ss][Oo][Nn][[:space:]]+', '')
        )
    ),
    ''
)
WHERE TRIM(COALESCE(first_name, '')) REGEXP '^[Pp][Ee][Rr][Ss][Oo][Nn][[:space:]]+';

SELECT 'STEP 8 - Remove Person prefix from first_name' AS step, ROW_COUNT() AS affected_rows;

/* =========================================================
   9) REMOVE "Person " PREFIX FROM last_name
   Example:
      Person Khan -> Khan
   ========================================================= */
UPDATE person
SET last_name = NULLIF(
    UCWORDS(
        TRIM(
            REGEXP_REPLACE(last_name, '^[Pp][Ee][Rr][Ss][Oo][Nn][[:space:]]+', '')
        )
    ),
    ''
)
WHERE TRIM(COALESCE(last_name, '')) REGEXP '^[Pp][Ee][Rr][Ss][Oo][Nn][[:space:]]+';

SELECT 'STEP 9 - Remove Person prefix from last_name' AS step, ROW_COUNT() AS affected_rows;

/* =========================================================
   10) REMOVE exact leftover "Person"
   ========================================================= */
UPDATE person
SET first_name = NULL
WHERE TRIM(COALESCE(first_name, '')) REGEXP '^[Pp][Ee][Rr][Ss][Oo][Nn]$';

SELECT 'STEP 10A - Remove exact Person from first_name' AS step, ROW_COUNT() AS affected_rows;

UPDATE person
SET last_name = NULL
WHERE TRIM(COALESCE(last_name, '')) REGEXP '^[Pp][Ee][Rr][Ss][Oo][Nn]$';

SELECT 'STEP 10B - Remove exact Person from last_name' AS step, ROW_COUNT() AS affected_rows;

/* =========================================================
   11) REMOVE prepaid/postpaid FROM last_name (whole word only)
   Example:
      Ali prepaid -> Ali
      prepaid     -> NULL
   ========================================================= */
UPDATE person
SET last_name = NULLIF(
    UCWORDS(
        TRIM(
            REGEXP_REPLACE(
                last_name,
                '(^|[[:space:]])([Pp][Rr][Ee][Pp][Aa][Ii][Dd]|[Pp][Oo][Ss][Tt][Pp][Aa][Ii][Dd])([[:space:]]|$)',
                ' '
            )
        )
    ),
    ''
)
WHERE last_name IS NOT NULL
  AND TRIM(last_name) REGEXP '(^|[[:space:]])([Pp][Rr][Ee][Pp][Aa][Ii][Dd]|[Pp][Oo][Ss][Tt][Pp][Aa][Ii][Dd])([[:space:]]|$)';

SELECT 'STEP 11 - Remove prepaid/postpaid from last_name' AS step, ROW_COUNT() AS affected_rows;

/* =========================================================
   12) IF first_name IS EMPTY AND last_name HAS SAFE SINGLE WORD
   THEN move last_name -> first_name
   Example:
      NULL | Aslam -> Aslam | NULL
   ========================================================= */
UPDATE person
SET 
    first_name = UCWORDS(TRIM(last_name)),
    last_name  = NULL
WHERE (first_name IS NULL OR TRIM(first_name) = '')
  AND TRIM(COALESCE(last_name, '')) REGEXP '^[^[:space:]]+$'
  AND TRIM(COALESCE(last_name, '')) NOT REGEXP '^[Pp][Ee][Rr][Ss][Oo][Nn]$'
  AND TRIM(COALESCE(last_name, '')) NOT REGEXP '^[Pp][Rr][Ee][Pp][Aa][Ii][Dd]$'
  AND TRIM(COALESCE(last_name, '')) NOT REGEXP '^[Pp][Oo][Ss][Tt][Pp][Aa][Ii][Dd]$';

SELECT 'STEP 12 - Move last_name into empty first_name' AS step, ROW_COUNT() AS affected_rows;

/* =========================================================
   12B) STRIP MOBILINK PARSER TRAILER FROM address

   The Mobilink "new format" subscriber response concatenates the
   address with no comma separator before the trailer:
       <real address> <ISO-DOB> <email send timestamp>
       <sender email> <originating MSISDN> fir <id>

   Example bad row:
       "... Khyber Pakhtunkhwa Pn Khyber Agency Pakistan
        2014-05-11t00:00:00000+05:0004/24/2026 2:14:16
        Amdcs@ctkpmailcom923045242005fir 1039508"

   Cleanup: cut everything from the first datetime marker onwards.
   Markers we anchor on:
     - ISO timestamp:    YYYY-M[M]-D[D][Tt]
     - US date+time:     MM/DD/YYYY HH:MM[:SS]
   Pakistani addresses don't legitimately contain either pattern,
   so the risk of false positives is low.

   Note: MySQL POSIX classes (no '\d', '\s'), '+'/'?' work in
   REGEXP_REPLACE on MySQL 8.0+.
   ========================================================= */
SELECT 'PRECHECK 12B - addresses with parser trailer' AS step,
       COUNT(*) AS affected_rows
FROM person
WHERE address REGEXP
      '[[:space:]]+([0-9]{4}-[0-9]{1,2}-[0-9]{1,2}[Tt]|[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}[[:space:]]+[0-9]{1,2}:[0-9]{2})';

/* Preview a sample of polluted rows BEFORE cleanup so the operator
   can spot-check the cut point. */
SELECT person_id, address
FROM person
WHERE address REGEXP
      '[[:space:]]+([0-9]{4}-[0-9]{1,2}-[0-9]{1,2}[Tt]|[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}[[:space:]]+[0-9]{1,2}:[0-9]{2})'
ORDER BY person_id
LIMIT 20;

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

SELECT 'STEP 12B - Strip parser trailer from address' AS step, ROW_COUNT() AS affected_rows;

/* =========================================================
   13) FINAL CASING
   ========================================================= */
UPDATE person
SET first_name = NULLIF(UCWORDS(TRIM(first_name)), '')
WHERE first_name IS NOT NULL
  AND first_name != NULLIF(UCWORDS(TRIM(first_name)), '');

SELECT 'STEP 13A - Final casing first_name' AS step, ROW_COUNT() AS affected_rows;

UPDATE person
SET last_name = NULLIF(UCWORDS(TRIM(last_name)), '')
WHERE last_name IS NOT NULL
  AND last_name != NULLIF(UCWORDS(TRIM(last_name)), '');

SELECT 'STEP 13B - Final casing last_name' AS step, ROW_COUNT() AS affected_rows;

UPDATE person
SET address = UCWORDS(TRIM(address))
WHERE address IS NOT NULL
  AND address != UCWORDS(TRIM(address));

SELECT 'STEP 13C - Final casing address' AS step, ROW_COUNT() AS affected_rows;

/* =========================================================
   14) FINAL REVIEW - ONLY SUSPICIOUS LEFTOVERS
   ========================================================= */
SELECT
    person_id,
    first_name,
    last_name,
    address
FROM person
WHERE
    TRIM(COALESCE(first_name, '')) REGEXP '^[Pp][Ee][Rr][Ss][Oo][Nn]([[:space:]]+.*)?$'
    OR TRIM(COALESCE(last_name, '')) REGEXP '^[Pp][Ee][Rr][Ss][Oo][Nn]([[:space:]]+.*)?$'
    OR TRIM(COALESCE(last_name, '')) REGEXP '(^|[[:space:]])([Pp][Rr][Ee][Pp][Aa][Ii][Dd]|[Pp][Oo][Ss][Tt][Pp][Aa][Ii][Dd])([[:space:]]|$)'
    OR COALESCE(address, '') REGEXP
       '[[:space:]]+([0-9]{4}-[0-9]{1,2}-[0-9]{1,2}[Tt]|[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}[[:space:]]+[0-9]{1,2}:[0-9]{2})'
ORDER BY person_id;

/* =========================================================
   15) FINAL SUMMARY COUNTS
   ========================================================= */
SELECT 'FINAL CHECK - first_name starts with Person' AS step,
       COUNT(*) AS remaining_rows
FROM person
WHERE TRIM(COALESCE(first_name, '')) REGEXP '^[Pp][Ee][Rr][Ss][Oo][Nn]([[:space:]]+.*)?$';

SELECT 'FINAL CHECK - last_name starts with Person' AS step,
       COUNT(*) AS remaining_rows
FROM person
WHERE TRIM(COALESCE(last_name, '')) REGEXP '^[Pp][Ee][Rr][Ss][Oo][Nn]([[:space:]]+.*)?$';

SELECT 'FINAL CHECK - last_name contains prepaid/postpaid' AS step,
       COUNT(*) AS remaining_rows
FROM person
WHERE TRIM(COALESCE(last_name, '')) REGEXP '(^|[[:space:]])([Pp][Rr][Ee][Pp][Aa][Ii][Dd]|[Pp][Oo][Ss][Tt][Pp][Aa][Ii][Dd])([[:space:]]|$)';

SELECT 'FINAL CHECK - address still contains parser trailer' AS step,
       COUNT(*) AS remaining_rows
FROM person
WHERE COALESCE(address, '') REGEXP
      '[[:space:]]+([0-9]{4}-[0-9]{1,2}-[0-9]{1,2}[Tt]|[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}[[:space:]]+[0-9]{1,2}:[0-9]{2})';

/* =========================================================
   16) COMMIT
   ========================================================= */
COMMIT;

/* =========================================================
   IF ANYTHING LOOKS WRONG, USE:
   ROLLBACK;
   ========================================================= */
