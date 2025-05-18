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
- Form-specific logic to determine expected submission deadlines
- Handles Cloudflare `1015` rate limiting gracefully

### ✅ Submission Tracking
- Retrieve and cache submissions with a dedicated job
- Deadline calculation logic for AR01, C1, B2, B10, B5, etc.
- View historical submissions in a responsive modal with Alpine.js + Livewire

### ✅ Compliance Dashboard
- Display compliance status:
  - `Overdue`: past due date
  - `Due Soon`: within 15 days
  - `Compliant`: future-dated
- Badges shown in real time on the company table

### ✅ Team Management
- Invite users by email and auto-create accounts if needed
- Assign `member` or `admin` roles per business
- Admins can manage team membership
- Users can belong to multiple businesses, each with different roles

### ✅ Job + Command Architecture
- `CompanyFetchCroSubmissions` Job fetches & syncs company submission data
- `RefreshCroSubmissions` Artisan command updates all companies in batches
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
   SERVICES_CRO_EMAIL=your@email.com
   SERVICES_CRO_KEY=your_cro_api_key
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
- [ ] Permissions per business (Granular access control)
- [x] Update application from the app

---

## 🧠 Credits

Built with ❤️ by [Itamar Junior](https://github.com/codeitamarjr)

---

## 📜 License

[MIT](LICENSE)
---
