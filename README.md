# disease-monitoring

Disease monitoring **recording system** for muga and eri. It sits beside `cmerti-site` and does not change anything in it.

**Open:** http://localhost/disease-monitoring/

## How it works

1. **Sign in** with your **work diary username and password**. It uses the same accounts table (`diary_users`), password hashes, 5-try lockout and sign-in log as cmerti-site. Anyone still using the first password the admin gave them has to change it in the work diary before they can sign in here.
2. **New Record** has six parts:
   - **Visit:** date, crop, season, site type, stage, host plant, DFLs, sample size
   - **Location:** state, district, block, village, farm, farmer, phone, GPS
   - **Findings:** incidence % for each disease or pest, plus mother moth examination (moths examined / positive)
   - **Weather:** temperature, RH and rainfall
   - **Action and remarks:** what was done, remarks and a photo
   - **E-mail addresses** to send the report to
3. **Save** does four things:
   - grades each finding and the record (Low / Moderate / High)
   - raises alerts for pebrine-positive moths, cold-and-humid muscardine weather, heavy rain and high incidence
   - makes a **PDF report** that includes the recommended management for each finding
   - **e-mails the PDF** to every address entered. A copy also goes to the person who recorded it and to any `always_notify` addresses.
4. **Records:** filter by date, crop, district, disease or severity; open a record or its PDF; **Resend** the e-mail or send it to more addresses; download a CSV. Staff see only their own records. Admins see everyone's.
5. **Dashboard:** counts, diseases and districts over the last 90 days, and e-mails that were not delivered.

## E-mail setup (one time)

XAMPP cannot send mail by itself. Edit `config.php` → `mail`:

```php
'from_email' => 'yourname@gmail.com',
'smtp' => ['host' => 'smtp.gmail.com', 'port' => 465, 'secure' => 'ssl',
           'username' => 'yourname@gmail.com', 'password' => '16-letter app password'],
```

Then go to **E-mail Check** (admins only) and send a test. Until this is filled in, records still save and the PDF can still be downloaded. The e-mail shows as "Failed" with a Resend button.

## Database

- It uses the same MySQL database as cmerti-site, with settings read from `cmerti-site/includes/db-config.php`.
- On first sign-in it creates its own tables: `dm_records`, `dm_findings` and `dm_notifications`.
- No `diary_` table is created or changed. Sign-ins and record views are written to the existing `diary_login_log` and `diary_audit_log`.

## Files

| File | Purpose |
|---|---|
| config.php | the only settings file: e-mail, severity thresholds |
| login.php | sign in / sign out |
| index.php | dashboard |
| record-new.php | entry form |
| record.php | view one record, resend e-mail, add addresses |
| records.php | list, filters, CSV |
| pdf.php, photo.php | PDF / photo, signed-in users only |
| mail-test.php | e-mail check (admins) |
| reference.php, pages/ | disease reference pages copied from cmerti-site |
| includes/app.php | settings, database, sign-in, CSRF |
| includes/dm-data.php | disease list, management advice, grading, saving |
| includes/report.php | PDF content and sending |
| includes/pdf.php | PDF writer (no library needed) |
| includes/mailer.php | SMTP sender with attachment (no library needed) |
| storage/ | PDFs and photos; created automatically and closed to the web |

## Points to confirm with Pathology

- The **severity thresholds** (below 5 % low, 5–15 % moderate, above 15 % high; any pebrine is high) are working defaults set in `config.php`. They do not come from the institute's pages.
- The muscardine weather alert is based on the Lahdoigarh comparison on the Silkworm Diseases page: minimum below 10 °C with RH at or above 72 %.

## GitHub

The code goes to the private repository **Sericulture-AI/disease-monitoring**. Records, PDFs and photos (`storage/`) and passwords (`config.local.php`) are never uploaded.

**First time**
1. Create an **empty private** repo at https://github.com/new. Set Owner to `Sericulture-AI` and Name to `disease-monitoring`, and do not add a README.
2. Install Git for Windows if it isn't already installed: https://git-scm.com/download/win
3. Double-click `push-to-github.bat`. Sign in to GitHub in the browser window the first time it asks.

**After every change:** double-click `push-to-github.bat` again.

**Passwords:** copy `config.local.example.php` to `config.local.php` and put the SMTP details there, not in `config.php`.

**Installing on another server:**
1. Run `git clone https://github.com/Sericulture-AI/disease-monitoring.git` inside `htdocs`, beside `cmerti-site`.
2. Create `config.local.php` there.
