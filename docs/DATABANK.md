# DRAMS Databank — operator + developer guide

The "DRAMS Databank" sidebar menu has one page, **Search All Sources**
(`/databank/all_sources`). It searches one value — mobile number, CNIC,
name or FIR number — in every external source the Databank lets DRAMS
read, **through the Databank integration API** (`databank` project,
`docs/INTEGRATION_API.md` there). DRAMS does not connect to those
databases for this page: the Databank decides which sources and field
categories the DRAMS API user may use, and audits every request.

It is separate from the per-person dashboard panels (`/persons/ext_db_*`),
which still read the external databases directly (see "Remaining direct
connections" below).

## How a search works

- The type is detected from the value (CNIC, mobile number, name, FIR
  number) unless the user picks it. Values are checked with the API's own
  rules before any request is made.
- One request (`POST integration/search`) answers for every permitted
  source, at most 25 records per source (the API user's
  `recordsPerSource`). Each source is shown as its own table; the columns
  come from the fields its records carry.
- "Follow linked CNICs / numbers" (mobile number or CNIC only) also
  searches the CNICs and numbers found on the records; records found that
  way carry a "Found through" column.
- Errors are shown as errors (never as "no matching records"), with the
  Databank reference to quote to the Databank administrators.

## File map

| File | Purpose |
|------|---------|
| `application/config/databank.php` | Base URL, token, timeouts, records per search. The token comes from `databank.local.php` (git-ignored, see `databank.local.example.php`) or `DATABANK_API_TOKEN`. |
| `application/classes/Helpers/DatabankApi.php` | The HTTP client: `me()`, `search_all()`, `normalize_identifier()`. Never logs the token or searched values, never retries, never follows redirects; throws `Helpers_DatabankApi_Exception` (user-safe message, `reason`, Databank `request_id`, and an admin-only `detail` such as the cURL error). |
| `application/classes/Helpers/Databank.php` | Turns the API answer into one table per source (`search_all_sources`, `records_table`). Also keeps `dlms_pdo()` for the person dashboard's DLMS panel. |
| `application/classes/Controller/Databank.php` | The page and its AJAX results. |
| `application/views/templates/user/databank_advanced_form.php` | The search form (text, select, checkbox, hint line). |
| `application/views/templates/user/databank_advanced_results.php` | One result table (escaped values, validated images). |
| `cli/databank_api_check.php` | Set-up check: config, host resolution, `GET integration/me`, the underlying cause of a failure. Never prints the token. |
| `cli/tests/databank_api_test.php` | Automated tests against a mock API (`cli/tests/databank_api_mock.php`). |

## Configuration

Set the token with `DATABANK_API_TOKEN` or in
`application/config/databank.local.php`. The default base URL is
`http://databank.ctdkpk.gov.pk/api/v1`; that name is **not in public
DNS**, so a server that cannot resolve it needs a hosts entry
(`192.168.0.158 databank.ctdkpk.gov.pk`) or the IP as base URL
(`http://192.168.0.158/api/v1`). Run `php cli/databank_api_check.php`
on the server after any change; it prints `OK` when the page can work.

Each search is written to the Kohana log with the DRAMS user id and the
Databank reference (no searched values), because every DRAMS user shares
one API identity on the Databank side.

## Access control

Menu and every action: `manu_management` menu 34 or 35 for the user's
role, or user 170 / 171 (`Controller_Databank::_require_databank_access()`).

## Remaining direct connections

`config/database.php` keeps `mobile`, `ecp`, `ctd_kpk`, `dlms_sqlsrv` and
`govt_emp_data`. The Databank page does not use them, but these still do:
the person dashboard panels (`Controller_Persons::action_ext_db_*` →
`Helpers_Person`), the ECP OCR daemon and its cron jobs
(`Helpers_EcpOcrDaemon`, `Cronjob`), and `Model_User` (`mobile`).

## Reports & Uploads section

The "Nadra Databank", "Nadra Reports", "Nadra Breakup Reports", "Old
Databank (MSISDN)", "MSISDN Reports" and "MSISDN Breakup Reports" items
(`Controller_Admindatabank`) work on DRAMS's own database and stay hidden
in the menu (`if (false)` in `sidebar_user.php`).
