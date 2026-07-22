# 📰 Journalist Portfolio Hub

**Journalist Portfolio Hub** is a premium, feature-rich WordPress plugin designed specifically for investigative journalists, reporters, documentary filmmakers, podcasters, and media professionals. 

It transforms your WordPress site into an editorial-grade portfolio showcase featuring published stories, awards & fellowships, video/podcast multimedia productions, downloadable press assets (CV & Media Kit), and a secure contact system.

---

## ✨ Features at a Glance

- **📖 Stories & Publications Module**: Showcase investigative pieces with kicker headings, publisher tags, external links, custom summaries, and automatic reading time estimation (e.g., `8 min read`).
- **🏆 Awards & Fellowships Module**: Display journalistic honors, grants, and regional fellowships with a 1-line homepage slider and a 6-item paginated grid on `/awards`.
- **🎥 Multimedia Gallery & Lightbox**: Present video documentaries, podcasts, photo essays, and data visualizations in a 4-column grid with custom poster thumbnails and an interactive YouTube video lightbox modal.
- **👤 Brief "About Me" Homepage Section**: Highlight your background, designation, profile picture, and a sleek CTA linking directly to your full biography page (`/about`).
- **📬 Complete Contact Page System**: 2-column layout (`/contact`) with author details, social links, download buttons, AJAX form submission, security nonces, honeypot anti-spam, and `wp_mail()` delivery.
- **📄 Downloadable Press Assets**: Shortcodes and settings for uploading and linking your CV/Resume (PDF) and Media Kit (PDF).
- **⚙️ Portfolio & Footer Settings**: Isolated admin menus for managing profile details, social links (Twitter/X, LinkedIn, YouTube, Facebook, Medium, Instagram), and footer columns.
- **✨ One-Click Demo Content Generator**: Populate or reset your entire portfolio with sample stories, publications, awards, and multimedia productions in a single click.

---

## 🚀 Installation & Automatic Setup

1. **Upload Plugin**: Place the `journalist-portfolio` folder into your WordPress plugins directory (`wp-content/plugins/`).
2. **Activate Plugin**: Navigate to **Plugins -> Installed Plugins** in your WordPress Dashboard and click **Activate**.
3. **Automatic Page Creation**: Upon activation, the plugin automatically creates and configures the essential portfolio pages if missing:
   - `/home` (Static Front Page)
   - `/about` (Biography)
   - `/stories` (Stories & Publications Archive)
   - `/multimedia` (Visual & Audio Gallery)
   - `/awards` (Awards & Fellowships)
   - `/contact` (Contact Page)

---

## 📖 Complete How-to-Use Guide

### 1. ⚙️ General & Profile Settings (`Portfolio Settings -> General Settings`)
Manage your personal branding and hero statement displayed across the portfolio:
- **Full Name**: Enter your display name (e.g., *Jane Doe*).
- **Designation / Title**: Enter your professional role (e.g., *Senior Investigative Reporter & Documentarian*).
- **Contact Details**: Phone number, email address, and geographical location (e.g., *Dhaka, Bangladesh*).
- **Profile Image**: Upload a high-resolution portrait photograph.
- **Hero Statement / Objective**: Enter a pull-quote or mission statement shown in the homepage Hero section.

---

### 2. 📰 Managing Stories & Outlets (`Stories`)
- **Add New Story**: Navigate to **Stories -> Add New**.
  - **Story Heading / Kicker**: e.g., *Exclusive Field Report | Environment & Climate Crisis*.
  - **Publisher / Outlet Name & Link**: Select or type the publishing outlet (e.g., *Reuters*, *The Daily Star*, *Kaler Kantho*).
  - **Reading Time**: Calculated automatically based on body content and custom excerpt length.
  - **Featured Image**: Upload a cover image (recommended size: `400 x 300` or `16:9` ratio).
- **Managing Outlets**: Navigate to **Stories -> Publications** to manage registered news outlets and site links.

---

### 3. 🏆 Managing Awards & Fellowships (`Portfolio Settings -> Awards & Fellowships`)
Add and manage journalistic honors, grants, and fellowships:
- **Award / Fellowship Title**: e.g., *GCCA+ Youth Awards*.
- **Awarded For / Organization**: e.g., *for Climate Storytelling* / *Poynter Institute*.
- **Year & Location**: e.g., *2025* | *International / Geneva, Switzerland*.
- **Icon**: Choose a Dashicon class (e.g., `dashicons-awards`, `dashicons-star-filled`, `dashicons-welcome-learn-more`).
- **Description**: Summary of recognition or reporting impact.

---

### 4. 🎥 Managing Multimedia Productions (`Portfolio Settings -> Multimedia`)
Add video documentaries, podcasts, photo essays, and data visualization projects:
- **Media Title**: e.g., *Voices of the Erosion: Coastal Communities Fight the Rising Tide*.
- **Media Type**: Select dropdown (*Video*, *Podcast*, *Photo Essay*, *Data Visualization*).
- **Tags**: Comma-separated tags (e.g., *Climate, River Erosion, Field Documentary*).
- **YouTube / Embed URL (Required)**: Enter the full YouTube video URL (e.g., `https://www.youtube.com/watch?v=...`).
- **Preview Poster Thumbnail**: Click **Upload** to pick or upload a custom 16:9 cover poster image from the Media Library.

> 💡 **Modal Lightbox**: When visitors click a multimedia card on the Homepage or `/multimedia` page, the video automatically opens in an interactive responsive lightbox popup.

---

### 5. 📬 Contact Page System (`/contact`)
The contact system is automatically routed to `/contact` and features a 2-column editorial split:
- **Left Column**: Author card with avatar, designation, email (`mailto:`), phone (`tel:`), location, social links, and CV/Media Kit download buttons.
- **Right Column**: AJAX-powered contact form with fields for Full Name, Email, Subject, and Message (with live character counter).
- **Security**: Protected by WordPress nonces and a silent honeypot field (`jp_website_url`) to block automated spam bots.

---

### 6. ✨ Re-seeding Demo Content (`Stories -> Re-seed Demo Data`)
If you want to quickly populate your site with sample content:
1. Navigate to **Stories -> Re-seed Demo Data** (or **Portfolio Settings -> Re-seed Demo Data**).
2. Review the summary of sample items (4 Stories, 4 Publications, 6 Awards, 4 Multimedia productions).
3. Click **✨ Re-seed Demo Content & Images** to instantly populate sample data.

---

## 🛠️ Shortcodes & Template Helpers

You can embed portfolio components anywhere on your site using standard WordPress shortcodes or PHP template helpers:

| Component | Shortcode | PHP Helper Function |
|---|---|---|
| **Multimedia Section** | `[jp_multimedia_section]` | `jp_render_multimedia()` |
| **Brief About Me** | `[jp_about_brief_section]` | `jp_render_about_brief()` |
| **Awards Carousel** | `[jp_awards_carousel]` | `echo do_shortcode('[jp_awards_carousel]');` |
| **Contact Page** | `[jp_contact_page]` | `echo do_shortcode('[jp_contact_page]');` |
| **CV Download Button** | `[jp_cv_link]` | `jp_get_cv_url()` |
| **Media Kit Download** | `[jp_media_kit_link]` | `jp_get_media_kit_url()` |
| **Footer Section** | `[portfolio_footer]` | `jp_render_footer()` |

---

## 🔧 Developer Notes & Requirements

- **PHP Version**: Requires PHP 8.0 or higher.
- **WordPress Version**: Compatible with WordPress 6.0+.
- **Coding Standards**: Follows modern WordPress Coding Standards (strict typing, nonces, `sanitize_text_field`, `esc_html`, `esc_url_raw`, `wp_kses_post`).
- **Permalinks**: Ensure your Permalinks are set to **Post name** (**Settings -> Permalinks -> Post name**) so routes like `/multimedia`, `/awards`, and `/contact` resolve smoothly.

---

## 📄 License & Credits

Created for **Journalist Portfolio Hub**. Built with modern WordPress Standards, HSL tailwind-inspired palette, and Google Fonts (Inter & Merriweather).
