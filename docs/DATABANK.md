# DRAMS Databank — operator + developer guide

The "DRAMS Databank" sidebar menu is a self-contained search facility that
lets users query each of the external databases this app reads. It is
intentionally separate from the per-person dashboard tabs (`/persons/ext_db_*`)
which are only useful once a person record exists in DRAMS — Databank lets
you go directly from a free-text filter to whatever the source database
holds, without first creating a person.

## What's where

```
/databank/subscriber_advanced     → unified Mobile + Foreigner search
/databank/ecp_advanced            → ECP electoral roll
/databank/ctd_kpk_advanced        → KPK CTD person profile
/databank/dlms_advanced           → DLMS driving licences
/databank/govt_employee_advanced  → government employee data
```

Each page has the same shape: a multi-field filter form on top, an empty
results panel below, and inline JS that POSTs the filled fields to a
sibling AJAX endpoint (`<page>_results`) and dumps the returned HTML
fragment into the panel.

## File map

| File | Purpose |
|------|---------|
| `application/classes/Controller/Databank.php`            | One host action + one AJAX action per database. Thin: forms come from `_form_view()`, queries from `Helpers_Databank`, results from the generic `databank_advanced_results` view. |
| `application/classes/Helpers/Databank.php`               | All SQL. One static method per database. Each takes an associative `$filters` array, drops empty values, AND-joins the rest into a single WHERE clause. |
| `application/views/templates/user/databank_advanced_form.php`    | Generic multi-field filter form. Reusable across all five searches. |
| `application/views/templates/user/databank_advanced_results.php` | Generic result table. Renders any column list passed by the action; supports a `formatter: 'image_jpeg'` for inline base64 image previews. |
| `application/views/templates/layout/sidebar_user.php`    | The "DRAMS Databank" treeview lives here. Five search items at the top, then a `Reports & Uploads` sub-header with the existing Admindatabank items. |

## Access control

Visibility — both the menu items and the controller actions — is gated by
the same role check used in the sidebar:

```
role_id 34 OR 35  OR  user_id 170 OR 171
```

`Controller_Databank::_require_databank_access()` re-runs this check on
every action so the URLs cannot be hit directly by unauthorised users.

## Adding a new search

1. Add a column-aware static method to `Helpers_Databank` that takes
   `(array $filters, $limit)` and returns rows. Drop empty filters,
   escape every value with `$DB->escape()`, build the WHERE clause from
   the supplied filters only.
2. Add a `action_<name>_advanced()` host action in
   `Controller_Databank.php`. Use `_form_view([...])` with the title,
   subtitle, breadcrumb, ajax_url, and `fields` array (each field:
   `{name, label, placeholder}`).
3. Add a sibling `action_<name>_advanced_results()` action that calls
   `_collect_filters([allowed names...])`, then your helper, then renders
   `templates/user/databank_advanced_results` with `rows` + `columns`.
4. Add a menu item to the DRAMS Databank `<ul class="treeview-menu">` in
   `sidebar_user.php` and add the menu_name to the `$databank_active`
   match list above so the parent treeview highlights correctly.

## Notes on the unified Subscriber search

The form has seven fields but only some apply to each underlying table.
The backend (`Helpers_Databank::search_subscriber_unified`) decides:

| Filter   | Hits subscribers_main (mobile)? | Hits afghan_accounts (foreigner)? |
|----------|---------------------------------|------------------------------------|
| msisdn   | yes (with PK number variants)   | yes (with same variants)           |
| cnic     | yes (`cnic`)                    | yes (`foreign_cnic` OR `master_acc_number`) |
| imsi     | yes                             | no (column does not exist)         |
| name     | no (not in current allow-list)  | yes (`master_name LIKE`)           |
| father   | no                              | yes (`father_name LIKE`)           |
| address  | no                              | yes (`site_address LIKE`)          |
| district | no                              | yes (`master_pak_district LIKE`)   |

Each row in the merged result is tagged with `source = 'mobile'` or
`source = 'foreigner'` so the result table can render the row's "true"
columns (e.g. CNIC for mobile, foreign_cnic for foreigner) via the
`fallback_keys` mechanism in `databank_advanced_results.php`.

The user does NOT have to know whether the CNIC they typed is Pakistani
or Afghan — the search hits both tables and shows whichever side
matched.

## Caveats

- **subscribers_main columns**: `imei` and `subscriber_name` are not
  exposed as filters. The legacy `Helpers_Subscriber::search()` does not
  whitelist them, and we have not verified they exist on every shard.
  When schema is confirmed, add them to `search_subscriber_unified` and
  to the form.
- **DLMS** runs against SQL Server (`dlms_sqlsrv`). It uses
  `SELECT TOP (n)` instead of `LIMIT n`. Do not copy the MySQL pattern
  when adding new DLMS queries.
- **ECP address column** depends on the `address_text` field being
  populated. For rows that only have `address_image_base64`, run the
  cron OCR backfill first (`/cronjob/ecp_address_diagnostic` to see
  fill rate; `/cronjob/ecp_address_ocr_backfill` to fill).

## Reports & Uploads section

The lower half of the DRAMS Databank menu — "Nadra Databank", "Nadra
Reports", "Nadra Breakup Reports", "Old Databank (MSISDN)", "MSISDN
Reports", "MSISDN Breakup Reports" — is unchanged from the original
implementation. Those actions live in `Controller_Admindatabank`. They
are pre-existing features and the structural code review (controller
methods exist, view files referenced exist) found no obvious problems,
so they were left in place. If any of them break in production, hide
the corresponding `<li>` in `sidebar_user.php` rather than deleting the
underlying controller action.
