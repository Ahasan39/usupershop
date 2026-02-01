# SEO Improvement Plan (Target: 100% Score)

This plan outlines the steps to optimize the USuperShop project for search engines.

## 1. Technical SEO Foundation 

### 1.1 Sitemap Generation
- **Task**: Create an automated system to generate `sitemap.xml`.
- **Action**: Install `spatie/laravel-sitemap` and create an Artisan command `php artisan sitemap:generate` to index all Products, Categories, and Static Pages.
- **Schedule**: Set up a scheduler to run this daily.

### 1.2 Robots.txt Optimization
- **Task**: Update `robots.txt` to guide crawlers.
- **Action**: Add Sitemap location and allow all major bots.

### 1.3 Canonical URLs
- **Task**: Ensure every page has a self-referencing canonical tag to prevent duplicate content issues.
- **Action**: Update `master.blade.php` to include `<link rel="canonical" href="{{ url()->current() }}" />`.

## 2. On-Page SEO (Dynamic Content)

### 2.1 Dynamic Meta Tags in Controllers
- **Task**: Pass specific SEO data from Controllers to Views.
- **Action**: Update `FrontendController` methods (`index`, `productDetails`, `categoryWiseProduct`, etc.) to pass:
    - `meta_title`
    - `meta_description`
    - `meta_keywords`
    - `og_image` (for social sharing)

### 2.2 View Layer Implementation
- **Task**: Ensure views correctly `yield` the meta data.
- **Action**: Verify `master.blade.php` yields are working and update child views (`home.blade.php`, `product-details.blade.php`) to `section` these values.

### 2.3 Heading Hierarchy (H1 Tags)
- **Task**: Ensure every page has exactly one `<h1>`.
- **Action**:
    - **Home**: Set the main logo text or hero heading as `<h1>`.
    - **Product Details**: Ensure Product Name is `<h1>`.
    - **Category Page**: Ensure Category Name is `<h1>`.

## 3. Schema Markup (Rich Snippets)

### 3.1 JSON-LD Implementation
- **Task**: Add structured data for Google Rich Results.
- **Action**: Implement:
    - **Product Schema**: On product details pages (Name, Image, Price, Availability, Review).
    - **BreadcrumbList**: On all deep pages.
    - **Organization**: On the homepage.

## 4. Performance & Experience

### 4.1 Image Optimization
- **Task**: Ensure images have `alt` attributes.
- **Action**: Audit `product-details.blade.php` and `home.blade.php` to ensure dynamic images have `alt="{{ $product->name }}"`.

### 4.2 404 Page Customization
- **Task**: Ensure a user-friendly 404 page exists.
- **Action**: Design a helpful 404 page with links back to Home and Shop.

---

## Execution Steps

1.  **Install Sitemap Package**: `composer require spatie/laravel-sitemap`
2.  **Create Sitemap Command**: `app/Console/Commands/GenerateSitemap.php`
3.  **Update Robots.txt**: Add sitemap URL.
4.  **Refactor FrontendController**: Inject SEO metadata.
5.  **Update Master Layout**: Add Canonical link and verify meta tags.
6.  **Update Product Views**: Add Schema.org JSON-LD and verify H1.
