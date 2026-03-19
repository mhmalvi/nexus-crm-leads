# Nexus CRM Leads

The lead management microservice for the **Nexus CRM** platform. This Laravel-based API handles the complete lead lifecycle — from capture and assignment through qualification, communication, and conversion tracking.

## Features

- **Lead CRUD Operations** — Create, read, update, and delete leads with rich metadata
- **Lead Assignment** — Assign and reassign leads to sales representatives with bulk operations
- **Pipeline Management** — Track lead status transitions with full audit logging
- **Lead Scraping** — Automated lead data extraction and import capabilities
- **Excel Import** — Bulk lead upload via Excel/CSV file parsing
- **Campaign Attribution** — Associate leads with marketing campaigns and track conversion rates
- **Quality Scoring** — Rate and filter leads by quality metrics
- **Communication Tracking** — Log call history, comments, and email interactions per lead
- **Email Templates** — Manage reusable email templates with mail merge support
- **Checklist Management** — Configurable checklists for lead processing workflows
- **Document Management** — Attach and manage student documents per lead
- **Course Management** — Link leads to courses with accountant-level course administration
- **Sales Performance** — Per-salesperson lead lists and assignment tracking
- **Form Integration** — Public API endpoint for capturing leads from external web forms
- **PDF Export** — Generate and serve checklist PDFs
- **Analytics** — Campaign-wise lead percentages, status counts, and conversion metrics

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Framework | Laravel 10 |
| Language | PHP 8.1+ |
| Authentication | Laravel Sanctum |
| HTTP Client | Guzzle |
| Database | MySQL |
| Testing | PHPUnit 10 |
| Code Style | StyleCI |

## Prerequisites

- PHP >= 8.1
- Composer
- MySQL 5.7+ or MariaDB 10.3+

## Getting Started

1. **Clone the repository**

   ```bash
   git clone https://github.com/mhmalvi/nexus-crm-leads.git
   cd nexus-crm-leads
   ```

2. **Install dependencies**

   ```bash
   composer install
   ```

3. **Configure environment**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Update `.env` with your database credentials and service URLs.

4. **Run database migrations**

   ```bash
   php artisan migrate
   ```

   Alternatively, import the provided `crm_lead.sql` schema file.

5. **Start the development server**

   ```bash
   php artisan serve
   ```

   The API will be available at `http://localhost:8000`.

## API Overview

| Endpoint Group | Description |
|---------------|-------------|
| `POST /api/lead/list` | List and filter leads |
| `POST /api/create-lead` | Create a new lead |
| `POST /api/lead/assign` | Assign leads to sales reps |
| `PUT /api/lead/status` | Update lead status |
| `PUT /api/lead/quality/update` | Update lead quality score |
| `POST /api/lead/mail` | Send emails to leads |
| `POST /api/excel-read` | Import leads from Excel |
| `POST /api/lead/scrap` | Scrape lead data |
| `POST /api/campaign/list` | List campaigns |
| `GET /api/counts` | Dashboard count metrics |
| `POST /api/create-lead-from-form` | Public form submission endpoint |

## Microservices Integration

| Service | Interaction |
|---------|------------|
| nexus-crm-users | Validates authentication tokens and fetches user/sales data |
| nexus-crm-orgs | Retrieves company context for multi-tenant operations |
| nexus-crm-payments | Links leads to payment records |
| nexus-crm-alerts | Triggers notification events on lead status changes |

## License

This project is proprietary software. All rights reserved.
