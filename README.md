# Jenga Client Portal

![WordPress Version](https://img.shields.io/badge/WordPress-5.8%2B-blue)
![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-8a8cb9)
![License](https://img.shields.io/badge/License-GPL%20v2.0--or--later-success)
![Version](https://img.shields.io/badge/Version-1.0.0-orange)

> A polished, white-label client dashboard and project management plugin designed specifically for freelance developers and creative agencies. Build stronger client relationships from inside your own WordPress website.

---

## Why Jenga Client Portal?

Stop paying monthly subscriptions for disjointed third-party SaaS client management tools. Jenga Client Portal allows you to bring your clients home to your own domain. By offering a branded, secure, and professional "Sophisticated Dark" dashboard, you can provide an enterprise-grade experience. Clients can track their projects, approve documents, and open support tickets without ever leaving your WordPress site.

---

## 📸 Screenshots

*(Replace placeholders with actual repository images)*
- 🖼️ `[Placeholder: Client Dashboard Overview]`
- 🖼️ `[Placeholder: Project Detail & Progress View]`
- 🖼️ `[Placeholder: Support Ticket Thread]`
- 🖼️ `[Placeholder: Document Library]`
- 🖼️ `[Placeholder: Admin Management Panel]`
- 🖼️ `[Placeholder: Custom Branded Login Page]`

---

## ✨ Features

### 🖥️ Client Portal
- **Front-End Rendered**: Everything lives on the front-end via shortcodes. It looks like a custom web app, not the WordPress admin.
- **Sophisticated Dark Theme**: Out-of-the-box gorgeous dark UI (`#0a0a0f` background, `#1a1a26` cards, `#c9a44a` gold accents).
- **Summary Dashboard**: Welcome header with dynamic cards showing active projects, open tickets, document counts, and the nearest impending deadline.

### 📊 Project Management
- **Status Tracking**: Not Started → In Progress → Under Review → Completed
- **Visual Progress**: Animated completion progress bars.
- **Financial & Scope Outline**: Display project budgets, assigned URLs, start dates, and hard deadlines.

### 🎫 Support Tickets
- **Priority Triage**: Low, Medium, High, Urgent.
- **Status Workflows**: Open, In Progress, Awaiting Reply, Resolved, Closed.
- **Threaded Communication**: Reply to tickets seamlessly via standard WordPress comments functionality (segregated for portal use).
- **AJAX Submission**: Snappy, client-side validated ticket forms without page reloads.

### 📄 Document Library
- **Secure Handling**: Uploads managed directly via WP media, but downloads are securely gated to assigned clients only.
- **Document categorization**: Distinguish between Invoices, Contracts, Deliverables, and Reports.

### ⚙️ Admin Capabilities
- **Hub Overview**: See all portal clients, ticket queues, and active projects in a centralized admin view.
- **Granular Settings**: Select portal pages, manage email notification toggles, tweak branding, and configure ticket auto-close timers.

---

## 🚀 Quick Setup Guide

1. **Install plugin**: Upload the zipped plugin via `Plugins > Add New > Upload` and click **Activate**.
2. **Create Portal Pages**: Create a new WordPress page named "Portal Dashboard" and another named "Portal Login".
3. **Add Shortcodes**: Add the `[jenga_portal_dashboard]` shortcode to the dashboard page, and `[jenga_portal_login]` to the login page.
4. **Configure Settings**: Go to **Client Portal > Settings** in your WordPress admin. Assign your newly created pages in the dropdown menus.
5. **Create a Client**: In standard WP Users, add a new user and assign them the **"Portal Client"** role.
6. **Assign Data**: Create a new Project (via `Client Portal > Projects`) and assign it to your new client in the meta box.

---

## 📜 Shortcodes

Place these on any standard WordPress page/post to render portal components:

| Shortcode | Description |
|-----------|-------------|
| `[jenga_portal_login]` | Renders a custom, branded login form. Redirects to your dashboard on success, or kicks non-portal users to home. |
| `[jenga_portal_dashboard]`| Renders the full flagship client dashboard experience. Automatically denies access to guests. |
| `[jenga_portal_projects]` | Outputs a grid list of projects assigned strictly to the currently logged-in client. |
| `[jenga_portal_tickets]` | Lists open/closed tickets and renders the AJAX ticket submission form. |
| `[jenga_portal_documents]`| Lists securely downloadable documents scoped to the logged-in user. |

---

## 🗂️ Custom Post Types

Jenga creates and manages three unique CPTs hidden from normal website search:

1. **Projects (`jenga_project`)**
   - **Supported:** title, editor, thumbnail
   - **Fields:** Client (User Dropdown), Status, Start Date, Due Date, Progress %, Budget, Project URL.
   - **Taxonomy:** `project_type` (Web Design, Branding, Maintenance, etc.)
2. **Tickets (`jenga_ticket`)**
   - **Supported:** title, editor, comments (replies)
   - **Fields:** Client, Related Project, Priority, Status.
   - **Taxonomy:** `ticket_category` (Bug, Feature Request, Billing, etc.)
3. **Documents (`jenga_document`)**
   - **Supported:** title
   - **Fields:** Client, Related Project, File URL, Document Type.

---

## 🔐 User Roles & Capabilities

The plugin registers a custom `portal_client` role cleanly isolated from your site content:

| Capability | Portal Client | Administrator |
|------------|---------------|---------------|
| `read` (Log into WP) | ✅ | ✅ |
| `portal_view_dashboard` | ✅ | ✅ |
| `portal_view_projects` | ✅ | ✅ |
| `portal_create_tickets` | ✅ | ✅ |
| `portal_view_tickets` | ✅ | ✅ |
| `portal_view_documents` | ✅ | ✅ |
| `portal_upload_documents`| ✅ | ✅ |
| `portal_manage_projects` | ❌ | ✅ |
| `portal_manage_tickets` | ❌ | ✅ |
| `portal_manage_documents`| ❌ | ✅ |
| `portal_manage_clients` | ❌ | ✅ |

---

## 📧 Email Notifications

All emails feature embedded HTML, your brand name, and the gold accent color:
- **To Admin:** New ticket submitted, New ticket reply from client.
- **To Client:** Ticket status changed, New reply from admin, New document uploaded, Project status updated.

*Note: All email notifications can be toggled on/off individually in the Portal Settings.*

---

## 🛡️ Security

Security is critical when handling client data. The following protections are enforced:
- **Capability Checks:** Every shortcode strictly checks `is_user_logged_in()` and `current_user_can('portal_view_dashboard')`.
- **Data Isolation:** All front-end lists (WP_Query/get_posts) hard-filter against the `_jenga_client_id` meta field. A client can **never** see another client's project or ticket.
- **Form Hardening:** All AJAX handlers actively verify nonces before processing data.
- **Data Integrity:** All POST inputs are aggressively sanitized (`sanitize_text_field`, `absint`, `esc_url_raw`). All UI output is escaped (`esc_html`, `esc_attr`).
- **SQL Best Practices:** Any custom queries utilize `$wpdb->prepare()`.
- **File Safety:** Document downloads execute permission validations before streaming file closures.

---

## 🛠️ Customization & Hooks

Designed with developer extension in mind.

### Overriding Templates
You can safely override template files without touching core plugin code!
Copy any PHP file from `/plugins/jenga-client-portal/templates/` into your active theme inside an `/jenga-portal/` directory. The plugin will load your theme's version instead.

### Action Hooks
- `do_action('jenga_portal_after_ticket_submit', $ticket_id, $data)`
- `do_action('jenga_portal_client_created', $user_id)`

### Filter Hooks
- `apply_filters('jenga_portal_project_statuses', $statuses)`
- `apply_filters('jenga_portal_ticket_priorities', $priorities)`

---

## 🏗️ File Structure

```text
jenga-client-portal/
├── jenga-client-portal.php
├── uninstall.php
├── README.md
├── readme.txt
├── includes/
│   ├── class-portal-setup.php
│   ├── class-portal-cpt.php
│   ├── class-portal-shortcodes.php
│   ├── class-portal-ajax.php
│   ├── class-portal-notifications.php
│   ├── class-portal-admin.php
│   └── class-portal-rest-api.php
├── assets/
│   ├── css/
│   │   ├── portal-frontend.css
│   │   └── portal-admin.css
│   └── js/
│       ├── portal-dashboard.js
│       ├── portal-tickets.js
│       └── portal-admin.js
├── templates/
│   ├── portal-login.php
│   ├── portal-dashboard.php
│   ├── portal-projects.php
│   ├── portal-tickets.php
│   ├── portal-documents.php
│   ├── portal-single-project.php
│   └── portal-single-ticket.php
└── languages/
    └── jenga-portal.pot
```

---

## 💻 Tech Stack & Built With

- **Backend:** PHP 7.4+, WordPress Core APIs (CPT, Options, REST API, AJAX, Roles).
- **Frontend:** HTML5, CSS3 (Native Variables, Grid/Flexbox layouts), Vanilla JS/jQuery bindings.
- **Styling:** Bespoke, unopinionated custom CSS—zero heavy frameworks (No Bootstrap/Tailwind dependencies).

### Requirements
- **WordPress:** 5.8 or higher.
- **PHP:** 7.4 or higher.

---

## 🗺️ Roadmap

- [ ] **Stripe Integration:** In-portal invoice payment flows.
- [ ] **Client Onboarding Wizard:** Guide new portal clients through questionnaires.
- [ ] **Slack & Discord Hooks:** Push ticket submissions directly into agency chat rooms.
- [ ] **Document Versioning:** Maintain history for deliverable revisions.

---

## 📝 License & Authors

**Author:** Loic Hazoume
**Web:** [https://wxza.net](https://wxza.net)
**Email:** jerryhazoume@gmail.com

This project is licensed under the **GPL-2.0-or-later** License.

---

## 🤝 Contributing

Contributions, issues, and feature requests are welcome!
Feel free to open an issue or submit a pull request if you want to extend Jenga Client Portal to be even better. When submitting PRs, please ensure inputs are sanitized and you follow the WordPress Coding Standards.
