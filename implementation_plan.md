# Hyper Adz Website — Implementation Plan

## Background

The `Data_for_Website.docx` file contains real business data and content requirements for the Hyper Adz website. I've compared this data against the current site and identified the following gaps and improvements required.

---

## Current State vs. Required State

| Area | Current State | Required (from Document) |
|------|--------------|--------------------------|
| Brand description | Generic/vague | Specific: "connects local brands to high-intent audiences through premium indoor screens across trusted partner venues" |
| Business address | `Ganapathy, Coimbatore - 641006` | `10, KK Nagar, 8th Street, Police Quarters Road, Ganapathy post, Coimbatore Dist, Tamilnadu - 641006` |
| Email | `connect.hyperadz@gmail.com` | Primary: `support@hyperadz.in` (business email), Secondary: `connect.hyperadz@gmail.com` |
| Phone | ✅ `+91 99620 99110` | ✅ Same, also WhatsApp: `9962099110` |
| GST | Missing | `33AIWPM0841K1ZX` |
| Mission/Vision | Vague on About page | Specific Vision and Mission provided |
| CTAs | Only "Plan Campaign" | Need: "Become a Location Partner", "Book a Demo", "Get a Quote", "Advertise with Hyper Adz", "Contact Us" |
| Partner page | Missing | New "Become a Partner" page needed |
| Target categories | Not shown | 6 categories: Food & Beverage, Health & Fitness, Beauty & Wellness, Auto & Mobility, Retail & Lifestyle, Learning & Recreation |
| Positioning tagline | Not explicitly stated | "Hyper Adz connects local brands to high-intent audiences through premium indoor screens across trusted partner venues." |
| Legal/Policy pages | Stub content | Links to full policy docs available (Google Docs) |
| Social media | Footer missing social links | Available on existing site — need to add |
| WhatsApp widget | Missing | Add floating WhatsApp chat button (9962099110) |
| Color palette | Blue (#0A66FF) | ✅ Good fit — refine for marketing/advertising feel |
| Tax rate info | Not on site | 18% GST — relevant for service/quote pages |
| SEO keywords | Generic | Specific target keywords provided |

---

## Proposed Changes

---

### 1. Brand Positioning & Copy Updates

#### [MODIFY] [home.blade.php](file:///d:/UI%20design/resources/views/home.blade.php)
- Update hero `<h1>` and lead paragraph to reflect real positioning:
  > *"Hyper Adz connects local brands to high-intent audiences through premium indoor screens across trusted partner venues."*
- Update hero tagline / eyebrow to "Multitude Solutions Presents" (already exists — keep it)
- Update stats section numbers/labels to match actual claims

#### [MODIFY] [about.blade.php](file:///d:/UI%20design/resources/views/about.blade.php)
- Replace generic mission/vision with exact content from document:
  - **Vision**: Build a most trusted and impactful Hyper Local advertising ecosystem in Tamil Nadu. Connect brands with high-intent audiences through premium partner locations and intelligent digital visibility.
  - **Mission**: To help businesses grow by delivering location-based indoor advertising solutions, while creating value for advertisers, venue partners, and consumers through measurable reach and mutually beneficial collaborations.
- Update address to full: `10, KK Nagar, 8th Street, Police Quarters Road, Ganapathy post, Coimbatore Dist, Tamilnadu - 641006`

---

### 2. Contact Information Updates

#### [MODIFY] [footer.blade.php](file:///d:/UI%20design/resources/views/partials/footer.blade.php)
- Update address to full address from document
- Add `support@hyperadz.in` as primary business email (replace `connect.hyperadz@gmail.com` or show both)
- Add social media icon links (Instagram, LinkedIn, etc. — visible on existing hyperadz.in site)
- Add a "Partner with Us" link to the footer

#### [MODIFY] [contact.blade.php](file:///d:/UI%20design/resources/views/contact.blade.php)
- Update full address
- Add WhatsApp link: `wa.me/919962099110`
- Show both email addresses
- Add GST number in a business details section

---

### 3. CTAs — Add "Become a Partner" Entry Point

> [!IMPORTANT]
> The document lists **"Become a Location Partner"** as a key CTA. The existing live site even has a "Become a Partner" menu item. This page is completely missing from the current build.

#### [NEW] [partner.blade.php](file:///d:/UI%20design/resources/views/partner.blade.php)
New page explaining the location partner programme, covering:
- 6 target partner categories (Food & Beverage, Health & Fitness, Beauty & Wellness, Auto & Mobility, Retail & Lifestyle, Learning & Recreation)
- Benefits for venue partners (earning from ad screens)
- Simple enquiry form

#### [MODIFY] [web.php](file:///d:/UI%20design/routes/web.php)
- Add route: `/become-a-partner` → `partner` → `partner`

#### [MODIFY] [navbar.blade.php](file:///d:/UI%20design/resources/views/partials/navbar.blade.php)
- Add "Partner" link to navbar

---

### 4. Navigation Improvements

#### [MODIFY] [navbar.blade.php](file:///d:/UI%20design/resources/views/partials/navbar.blade.php)
- Rename current nav items slightly to match document's CTAs
- Keep: About, Services, Network, Why Hyper Adz, Contact
- Add: **Partner** page link
- Update primary CTA button to "Advertise with Us" (stronger advertising-focused CTA)

---

### 5. Legal Policy Pages — Content Improvement

> [!NOTE]
> The document provides Google Docs links for each policy. I'll update the policy pages with more substantial content matching the linked documents.

#### [MODIFY] [privacy.blade.php](file:///d:/UI%20design/resources/views/policies/privacy.blade.php)
- Expand with richer privacy policy content matching the spirit of the Google Docs version

#### [MODIFY] [terms.blade.php](file:///d:/UI%20design/resources/views/policies/terms.blade.php)
- Expand with richer terms content

#### [MODIFY] [refund.blade.php](file:///d:/UI%20design/resources/views/policies/refund.blade.php)
- Expand with richer refund/cancellation content

#### [NEW] Cookie Policy page
- Add `/cookie-policy` route and page (doc lists a cookie policy link)

---

### 6. Color Palette — Refine for Advertising/Marketing Confidence

> [!NOTE]
> Current palette is already blue/white. The recommended refinement is subtle — shifting the primary blue slightly warmer/more confident and adding a premium electric blue for marketing boldness.

#### [MODIFY] [hyperadz.css](file:///d:/UI%20design/public/css/hyperadz.css)
Proposed palette refinements (advertising-industry optimized):

| Token | Current | Proposed |
|-------|---------|----------|
| `--ha-blue` | `#0A66FF` | `#1155CC` (deeper, more trustworthy) |
| `--ha-blue-2` | `#3B82F6` | `#2563EB` |
| `--ha-accent` | `#60A5FA` | `#3B82F6` |
| Footer bg | `#0B132B` | `#0A1628` (richer deep navy) |

> [!IMPORTANT]
> User should confirm if they want the palette tweaked or if the current blue (#0A66FF) is preferred to keep as-is.

---

### 7. WhatsApp Floating Button

#### [MODIFY] [app.blade.php](file:///d:/UI%20design/resources/views/layouts/app.blade.php)
- Add a floating WhatsApp button (bottom-right) linking to `wa.me/919962099110`
- Style it with WhatsApp green (`#25D366`) as an exception to the blue/white palette (standard industry practice for WhatsApp CTAs)

---

### 8. SEO Improvements

#### [MODIFY] All page blade files
- Update `<title>` and `<meta description>` using the target keywords from the document:
  - Home: "Indoor Advertising Coimbatore | Digital Signage Network | Hyper Adz"
  - Services: "DOOH Advertising Coimbatore | Indoor Digital Signage Services | Hyper Adz"
  - Network: "Advertising Screens Coimbatore | Indoor Ad Locations | Hyper Adz"
  - About: "About Hyper Adz | Hyperlocal Ad Network Coimbatore | Multitude Solutions"
  - Partner: "Become a Location Partner | Indoor Ad Screen Partner Coimbatore | Hyper Adz"

---

### 9. Home Page — Add Target Audience / Advertiser Types Section

#### [MODIFY] [home.blade.php](file:///d:/UI%20design/resources/views/home.blade.php)
Add a new section showing who Hyper Adz serves (from document's target audience):
- **Advertisers**: Car/Bike showrooms, Jewellery brands, Education institutions, Apparel brands, SMBs, Agencies
- **Venue Partners**: Restaurants, Cafes, Gyms, Salons, Clinics, etc.

---

## Open Questions

> [!IMPORTANT]
> **1. Color Palette**: Should I keep the current electric blue (`#0A66FF`) as-is or refine it to the deeper navy-blue (`#1155CC`) for a more premium marketing feel? Current blue is vibrant/energetic; deeper blue reads as more established/trustworthy.

> [!IMPORTANT]
> **2. Cookie Policy Page**: Should I add a separate `/cookie-policy` page (the document lists a cookie policy link), or just reference it in the Privacy Policy?

> [!IMPORTANT]
> **3. Pricing / GST**: The document says 18% GST applies. Should this be shown anywhere on the site (e.g., on a services/pricing page), or left out as pricing is "contact for quote"?

> [!IMPORTANT]
> **4. Partner Page**: Should the "Become a Partner" page include a form submission (data goes to database), or just collect enquiries via WhatsApp/email like the current Contact page?

---

## Verification Plan

### Manual Verification
- Check all updated pages in browser at `http://127.0.0.1:8890`
- Confirm all existing routes still work
- Verify new Partner page renders correctly
- Test WhatsApp button and all contact links
- Verify SEO meta tags update with `view-source`
