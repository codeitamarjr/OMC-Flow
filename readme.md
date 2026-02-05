# OMC Flow — Compliance and CRO Deadline Tracker for Irish Businesses

OMC Flow is a Laravel + Livewire application designed to streamline the management of Irish companies, their compliance obligations, CRO filings, team roles, and document submission tracking. The platform helps business owners stay compliant with CRO regulations and avoid costly fines by proactively tracking key deadlines like Annual Returns (AR01).

---


## 🚀 Features

### ✅ Company & Business Management
- Manage multiple businesses per user
- Assign companies to businesses
- Add, update, and tag companies
- Filter companies by tags or deadlines

### ✅ Tag Filtering and Session Persistence
- Tags stored per business and filter state is remembered per session
- Tag updates handled with Livewire model binding and `sync()`

### ✅ CRO Integration
- Uses official CRO API to:
  - Retrieve company details
  - Fetch and update company submission documents
- Auto-sync officer snapshots (when available in CRO payload)
- Track obligation statuses in `company_cro_document`:
  - `completed`
  - `due_soon`
  - `missing`
  - `overdue`
  - `risky`
- Auto-create deadline metadata:
  - `B1` (ARD + 28 days)
  - `B10` officer changes (event-based when officer dates are available)
  - `B2` registered office changes (14-day filing window after detected address change)
  - `AGM` annual governance check (annual cycle from `last_agm`, fallback `registration_date`)
- Handles invalid/null CRO responses with fallback logic
- Uses corrected company submissions endpoint: `company/{company_num}/c/submissions`

### ✅ Submission Tracking
- Retrieve and cache submissions with a dedicated job
- Deadline calculation logic for AR01, C1, B2, B10, B5, etc.
- View historical submissions in a responsive modal with Alpine.js + Livewire

### ✅ Compliance Dashboard
- Dashboard widget now shows:
  - Active companies tracked
  - Overdue, risky, due soon, and missing totals
  - Last synced at timestamp
- Dashboard company table now summarizes risk per company:
  - Next core CRO deadline (`B1` / `B10` / `B2` / `AGM`)
  - Risk level (`High`, `Elevated`, `Medium`, `Low`, `Compliant`)
  - Issue badges (`Overdue`, `Risky`, `Missing`)
  - Sort by nearest deadline and highest risk

### ✅ Team Management
- Invite users by email and auto-create accounts if needed
- Assign `member` or `admin` roles per business
- Admins can manage team membership
- Users can belong to multiple businesses, each with different roles

### ✅ Job + Command Architecture
- `CompanyFetchCroSubmissions` Job fetches & syncs company submission data
- `RefreshCroSubmissions` Artisan command updates all companies in batches
- `RefreshCompaniesFromCro` dispatches `RefreshSingleCompanyFromCro` for all valid companies
- `RefreshSingleCompanyFromCro` updates:
  - company profile fields
  - filing history
  - officer snapshot
  - obligation status/risk/deadline metadata
- `compliance:send-reminders` sends smart reminders:
  - Countdown reminders at `60 / 30 / 7` days
  - Overdue escalation reminders at `1 / 7 / 30` overdue days
  - Delivery channels: email + in-app notifications
- Handles duplicate updates and throttles requests between batches

---

## 🛠️ Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-org/omc-flow.git
   cd omc-flow
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install && npm run build
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Then add your [CRO API credentials](https://services.cro.ie/cws/documentation) to `.env`:
   ```env
   CRO_EMAIL=your@email.com
   CRO_API_KEY=your_cro_api_key
   CRO_QUEUE=default
   ```

4. **Run migrations**
   ```bash
   php artisan migrate
   ```

5. **Start the development server**
   ```bash
   php artisan serve
   ```

---

## ⚙️ Artisan Commands

### Update CRO Submissions for All Companies
```bash
php artisan cro:refresh-submissions
```

### Run Full CRO Company Refresh (All Companies)
```bash
php artisan queue:restart
php artisan migrate --force
php artisan tinker --execute="\App\Jobs\RefreshCompaniesFromCro::dispatch();"
php artisan queue:work database --queue=default --tries=3 --timeout=180 --stop-when-empty
```

### Daily Automation (Current Status)
- Daily CRO sync is scheduled at `00:00`:
  - `RefreshCompaniesFromCro`
- Daily reminders are scheduled at `08:00`:
  - `compliance:send-reminders`
- Scheduler and queue worker must be running for automation:
  - `php artisan schedule:run` via cron
  - `php artisan queue:work`

---

## 📌 CRO Auto-Sync Update (2026-02-05)

### What changed
- Extended `RefreshSingleCompanyFromCro` to sync:
  - Company details
  - Filing history (`company_submission_documents`)
  - Officer snapshot (`companies.cro_officers_snapshot`, `companies.cro_officers_synced_at`)
  - Obligation statuses and deadlines on `company_cro_document`
- Added core obligation focus in UI and sorting:
  - Company list and dashboard now summarize core CRO obligations (`B1`, `B10`, `B2`, `AGM`)
  - Company list supports filter reset, obligation filters, nearest deadline sort, and highest risk sort
- Updated dashboard table:
  - Removed actions column
  - Added risk-summary-oriented columns and badges
- Added automatic deadline rules:
  - `B1`: ARD + 28 days
  - `B10`: 14-day filing window
  - `B2`: 14-day registered office change filing window
  - `AGM`: annual governance check deadline
- Added migration:
  - `2026_02_05_160000_add_cro_sync_columns`
- Added resilient fallback handling for CRO submissions:
  - If `getCompanySubmissions()` fails or returns invalid payload, fallback logic keeps job running

### Database fields added
- `companies`
  - `cro_officers_snapshot` (JSON)
  - `cro_officers_synced_at` (timestamp)
- `company_cro_document`
  - `due_date`
  - `last_filed_at`
  - `status`
  - `risk_level`
  - `notes`

### Commands executed and result
- Ran full refresh flow for all companies
- Queue processing finished with no pending jobs:
  - `pending_jobs=0`
- Current obligation status totals:
  - `completed: 35`
  - `missing: 123`
  - `overdue: 3`
  - `risky: 7`
- Current risk totals:
  - `high: 3`
  - `medium: 130`
  - `low: 35`

---

## 🧩 Project Structure

| Directory        | Purpose                                                                 |
|------------------|-------------------------------------------------------------------------|
| `app/Models`     | Eloquent models for User, Business, Company, Tag, SubmissionDocument    |
| `app/Livewire`   | Livewire components (TeamManager, CompanyTable, CompanyForm)            |
| `app/Jobs`       | Asynchronous job to fetch submission data via CRO API                  |
| `app/Services`   | Core services like `CroSearchService` to integrate with the CRO API     |
| `resources/views/livewire` | Blade templates for each Livewire component                    |
| `resources/views/components/ui` | Modal, flash messages, and UI building blocks             |

---

## 🛡️ Security & Rate Limiting

- API requests to CRO are rate-limited.
- `CroSearchService` handles Cloudflare `1015` rate limits by:
  - Sleeping for 10 seconds on hit
  - Throttling between batch requests
- Requests are retried using `sleep()` + pagination logic to avoid repeated requests

---

## 📬 Email Invitations

When an admin invites a new user:
- A new user is created if they don't exist
- A reset password token is emailed
- Markdown-based invite email includes:
  - Who invited them
  - Which business they were invited to
  - A secure reset link

---

## 💡 Future Roadmap

- [ ] Automatic email notifications before AR01 deadlines
- [ ] Support for Financial Statement (B1 + Accounts) due dates
- [ ] Auto-download and store official CRO PDFs
- [ ] Multi-tenant audit trail
- [x] Permissions per business (Granular access control)
- [x] Update the application from the app

---

## 🧠 Credits

Built with ❤️ by [Itamar Junior](https://github.com/codeitamarjr)

---

## 📜 License

[MIT](LICENSE)
---
