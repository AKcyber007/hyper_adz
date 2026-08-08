# Hyper Adz Phase 1 Marketing Website Brief

## Objective

Recreate the Hyper Adz website as a real Laravel 12 Blade application, not a standalone prototype. This is Phase 1 of a larger digital advertising platform, so implement only the premium marketing website. Do not build booking, payments, authentication, customer dashboards, admin screens, live inventory, or checkout flows.

## Audience

Business owners, marketing managers, retail brands, agencies, and local advertisers evaluating indoor digital advertising opportunities in Coimbatore and nearby markets.

## Source Material

- UI reference: `D:\UI design\uireference.html`
- User Phase 1 brief: `C:\Users\LENOVO\.codex\attachments\eccbaecd-d84f-4b40-b837-c442dec72610\pasted-text.txt`
- Brand logo assets already copied to:
  - `D:\UI design\public\images\hyperadz-banner-logo.svg`
  - `D:\UI design\public\images\hyperadz-logo-square.svg`
  - `D:\UI design\public\images\favicon.svg`
- Policy documents are available at:
  - `C:\Users\LENOVO\Downloads\Privacy_Policy.docx`
  - `C:\Users\LENOVO\Downloads\Terms_Conditions.docx`
  - `C:\Users\LENOVO\Downloads\Refund_Policy.docx`
  - `C:\Users\LENOVO\Downloads\Cookie_Policy.docx`
  - `C:\Users\LENOVO\Downloads\HA_Policies.docx`

## Brand Facts To Use

Use the official Hyper Adz content direction: indoor digital advertising, digital signage, screen rentals/sales, brand promotion, campaign analytics, cloud connected displays, and premium location-based media. Contact details from the live Hyper Adz site:

- Email: `connect.hyperadz@gmail.com`
- Phone: `+91 99620 99110`
- Address: Ganapathy, Coimbatore - 641006

## Aesthetic Direction

Follow the supplied `uireference.html` as closely as possible. Keep its premium SaaS feel: blue and white palette, clean white sections, glass-like cards, soft shadows, modern typography, strong whitespace, rounded cards, polished buttons, subtle hover states, and smooth motion.

The result should feel like Stripe, Linear, Apple, Webflow, or Framer, but visually grounded in the existing UI reference rather than redesigned from scratch.

Avoid text overlap. Ensure long labels wrap cleanly on mobile and desktop. Do not create card-in-card layouts. Use full-width section bands with constrained inner content.

## Typography

Use a modern sans-serif pairing similar to the reference: Inter for body text and Poppins or a comparable display font for headings. Keep letter spacing at `0` unless the reference specifically requires an all-caps micro-label.

## Color Direction

Use the reference's clean Hyper Adz blue system:

- Primary blue: `#0A66FF`
- Secondary blue: `#2E8BFF`
- Accent blue: `#5EA8FF`
- Text: near-black/slate
- Backgrounds: white and pale blue-gray

Include enough neutral contrast so the site does not become a one-note blue page.

## Required Laravel Output

Use Laravel Blade and project-native folders. Output to the current Laravel project at `D:\UI design`.

Create or update:

- `routes/web.php`
- `resources/views/layouts/app.blade.php`
- `resources/views/partials/navbar.blade.php`
- `resources/views/partials/footer.blade.php`
- `resources/views/components/section-header.blade.php`
- `resources/views/components/cta.blade.php`
- `resources/views/components/service-card.blade.php`
- `resources/views/home.blade.php`
- `resources/views/about.blade.php`
- `resources/views/services.blade.php`
- `resources/views/network.blade.php`
- `resources/views/why.blade.php`
- `resources/views/contact.blade.php`
- `resources/views/policies/privacy.blade.php`
- `resources/views/policies/terms.blade.php`
- `resources/views/policies/refund.blade.php`
- `resources/views/errors/404.blade.php`
- `public/css/hyperadz.css`
- `public/js/hyperadz.js`
- `public/robots.txt`
- `public/sitemap.xml`

If needed, create supporting folders such as `resources/views/partials` and `resources/views/policies`.

## Pages And Sections

Home:
- Premium hero with brand logo, animated background, strong CTA, and a first-viewport visual signal of indoor digital screens.
- Company statistics.
- Client logos placeholder row.
- Services preview.
- Why Hyper Adz preview.
- Interactive network preview.
- Testimonials placeholder.
- Contact CTA.

About:
- Company story.
- Mission and vision.
- Business overview.
- Why Hyper Adz.
- Timeline.
- Core values.

Services:
- Indoor Digital Advertising.
- Digital Signage Sales.
- Digital Signage Rental.
- Campaign Analytics.
- Cloud Connected Displays.
- Brand Promotion.
- Each service needs an icon, image/visual treatment, description, and CTA.

Network:
- Leaflet.js + OpenStreetMap.
- Dummy markers only.
- Marker popup fields: location name, category, address, and a Coming Soon button.
- Design the page so a future booking/details module can sit beside or below the map, but do not implement booking.

Why Hyper Adz:
- Largest indoor digital network.
- 300+ screens.
- Geo tagged reports.
- Premium locations.
- Transparent pricing.
- Ethical business.
- Complete client satisfaction.

Contact:
- Company information.
- Contact form UI only.
- Map/location placeholder.
- Phone, email, address, business hours, social icons.

Policy Pages:
- Privacy Policy, Terms & Conditions, Refund Policy.
- Use supplied `.docx` content where practical. If extraction is not practical, create polished structured placeholder policy content for Hyper Adz and make it easy to replace later.

404:
- Branded not-found page with navigation back home and contact CTA.

## Libraries

Use CDN imports in the layout:

- Bootstrap 5.3
- Bootstrap Icons
- AOS
- Leaflet.js on the Network page
- GSAP only for subtle hero/micro interactions

Use custom CSS in `public/css/hyperadz.css` for the real design. Do not rely only on Bootstrap.

## SEO

Each page should support:

- Meta title.
- Meta description.
- Canonical URL.
- Open Graph tags.
- Twitter card tags.
- Schema.org Organization JSON-LD in the layout.

Add `robots.txt` and `sitemap.xml`.

## Future Platform Readiness

Keep route names, component names, section naming, and CSS organization clean so Phase 2 map filtering and Phase 3 booking can be added without redesign. Add future-facing CTAs as "Explore Network" or "Plan a Campaign", but any booking-specific action should say "Coming Soon" or lead to contact.

## Verification Expectations

After implementation, inspect the generated files for obvious syntax issues and confirm the layout avoids overlapping text in responsive CSS. If PHP execution is blocked by the environment, report that clearly.
