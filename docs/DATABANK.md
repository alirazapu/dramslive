# DRAMS Databank — operator + developer guide

The "DRAMS Databank" sidebar menu searches the external databases
(mobile subscribers, ECP, CTD KPK FIR records, driving licences,
government employees, vehicles) **through the Databank integration API**
(`databank` project, `docs/INTEGRATION_API.md` there). DRAMS no longer
connects to those databases for these pages: the Databank decides which
sources, features and field categories the DRAMS API user may use, and
audits every request.

It is separate from the per-person dashboard panels (`/persons/ext_db_*`),
which still read the external databases directly (see "Remaining direct
connections" below).

## What's where

```
/databank/advanced_search         → Advanced Search (investigation session)
/databank/all_sources             → one value in every permitted source
/databank/subscriber_advanced     → Mobile Subscription (local + foreign)
/databank/ecp_advanced            → ECP electoral roll (+ /databank/ecp_family)
/databank/ctd_kpk_advanced        → CTD KPK FIR records
/databank/dlms_advanced           → driving licences
/databank/govt_employee_advanced  → government employees
/databank/vehicle_advanced        → vehicle / excise registrations
```

## File map

| File | Purpose |
|------|---------|
| `application/config/databank.php` | Base URL, token, timeouts, records per search. The token comes from `databank.local.php` (git-ignored, see `databank.local.example.php`) or `DATABANK_API_TOKEN`. |
| `application/classes/Helpers/DatabankApi.php` | The only HTTP client: `me()`, `search()`, `search_all()`, `ecp_family()`, `advanced()`, `export()`. Validates identifiers with the API's rules, never logs the token or searched values, never retries, throws `Helpers_DatabankApi_Exception` (user-safe message + `reason` + Databank `request_id`). |
| `application/classes/Helpers/Databank.php` | Maps API records onto the row keys the result table uses (`search_*`), generic tables for varying fields (`records_table`), and `refine_rows()`. Also keeps `dlms_pdo()` for the person dashboard's DLMS panel. |
| `application/classes/Controller/Databank.php` | Pages, AJAX results, the Advanced Search JSON proxy (`advanced_api`) and CSV export. Page/field/column definitions are in `_page()`. |
| `application/views/templates/user/databank_advanced_form.php` | Generic filter form (text, select, checkbox, disabled "not available" fields, hint line). |
| `application/views/templates/user/databank_advanced_results.php` | Generic result table (escaped values, validated images, optional notes). |
| `application/views/templates/user/databank_ecp_family.php` | ECP household page. |
| `application/views/templates/user/databank_advanced_search.php` | Advanced Search page and its JS (tabs: Summary, Records, Findings, Graph, Timeline, Profile, CDR, Location, Geo, Export). |
| `cli/databank_api_check.php` | Deployment check: config + `GET integration/me` (prints grants, never the token). |
| `cli/tests/databank_api_test.php` | Automated tests against a mock API (`cli/tests/databank_api_mock.php`). |

## How a per-source search works

The API's normal search takes **one** identifier per request — mobile
number, CNIC, name or FIR number — and returns at most 25 records per
source (the API user's `recordsPerSource`). So each form field has a role
(`_page()`):

- `search` — the API can search by it. When several are filled, one is
  searched, in the order FIR → mobile → CNIC → name.
- `refine` — narrows the returned records (`Helpers_Databank::refine_rows`),
  so all filled fields still AND together. A note says when this happened
  and when the 25-record cap was reached.
- `unsupported` — shown disabled; the API can neither search nor return it.

Errors are shown as errors (never as "no matching records"), with the
Databank reference to quote to the Databank administrators.

## Field mapping (API record field label → table column)

| Page | Mapped labels | Not available through the API |
|------|---------------|-------------------------------|
| Subscriber (`MOBILE`) | Subscriber category, Mobile number (or record title), CNIC, Name, Address, District, Tehsil, Mobile operator, Status, Biometric verification (BVS) | IMSI and father (not returned; searching foreign accounts needs a 13-digit CNIC) |
| ECP (`ECP`) | CNIC, Name, Father's name, Address (+ address image), Age, Gender, Electoral block code, Phone numbers; ECP ID from the record reference | — |
| CTD FIR (`FIR`) | CNIC, Name, Father's name, Alias, Tribe, Place of birth, Latest FIR number, FIR date | Permanent / current district and tehsil |
| DLMS (`DRIVING_LICENCE`) | CNIC, Name, Father's name, Date of birth, Gender, Licence number, Issued on, Expires on | First / middle / last name parts (returned joined); licence-number-only search |
| Govt employee (`GOVT_EMPLOYEE`) | CNIC, Name, Father's name, Personnel number, Job title, Organisation unit, Personnel area | First / last name parts; numeric org-unit code; personnel-number-only search |
| Vehicle (`VEHICLE`) | every returned field (columns follow the records) | registration-number search (use Advanced Search) |

## Advanced Search

The browser never calls the Databank: its JS posts `op` + `payload` to
`/databank/advanced_api`, which forwards only whitelisted calls
(`Helpers_DatabankApi::$advanced_ops`) with whitelisted body keys. Every
DRAMS user shares one API identity, so the controller remembers the
searchIds each DRAMS user started (session) and refuses the others.
Each call is written to the Kohana log with the DRAMS user id and the
Databank reference (no searched values).

## ECP family

Uses `POST integration/ecp/family`, added to the Databank together with
this integration. Until that Databank change is deployed, the page says
the family view is not available yet.

## Access control

Menu and every action: `manu_management` menu 34 or 35 for the user's
role, or user 170 / 171 (`Controller_Databank::_require_databank_access()`).

## Remaining direct connections

`config/database.php` keeps `mobile`, `ecp`, `ctd_kpk`, `dlms_sqlsrv` and
`govt_emp_data`. The Databank pages do not use them any more, but these
still do: the person dashboard panels (`Controller_Persons::action_ext_db_*`
→ `Helpers_Person`), the ECP OCR daemon and its cron jobs
(`Helpers_EcpOcrDaemon`, `Cronjob`), and `Model_User` (`mobile`).

## Reports & Uploads section

The "Nadra Databank", "Nadra Reports", "Nadra Breakup Reports", "Old
Databank (MSISDN)", "MSISDN Reports" and "MSISDN Breakup Reports" items
(`Controller_Admindatabank`) work on DRAMS's own database and stay hidden
in the menu (`if (false)` in `sidebar_user.php`).
