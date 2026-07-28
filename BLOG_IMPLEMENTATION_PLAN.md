# Blog Implementation Plan

## Goal

Convert the current Laravel app into a multilingual, role-based blog platform with:

- language-first content
- author-based user roles
- multi-category posts
- hierarchical categories
- shared service layer for web and API
- reusable image/media uploads
- preview images, SEO, and geo support
- Tiptap editor for content
- fallback behavior from the default language into missing locales

## UI Reference

Use the existing admin UI style from `resources/views/letters` as the visual and layout reference for new blog screens.

Keep the same patterns:

- `layouts.admin.app`
- `kt-card` sections
- breadcrumb header blocks
- `kt-btn` action buttons
- `select2` for multi-select fields
- `flatpickr` for dates
- table-driven index pages
- edit forms with grouped cards

## Core Rules

1. Every write action must go through a service class.
2. Web controllers and API controllers must reuse the same services.
3. All content is language-based.
4. Default language is the source of truth.
5. Missing translation data must fall back to the default language.
6. If a non-default language has no content yet, copy the default content into it as the starting draft.
7. Existing translations must never be overwritten by fallback sync.
8. Media uploads must be reusable anywhere in the system.

## Roles

Use role-based access with `User` acting as the author account.

Recommended roles:

- `admin`
- `editor`
- `author`
- `viewer`

Permissions:

- admin manages everything
- editor manages and publishes content
- author creates and edits own content
- viewer has read-only access

## Content Model

### Post

Canonical post record with shared data:

- author
- status
- publish state
- featured flag
- preview image
- geo metadata
- timestamps

### PostTranslation

Per-language post data:

- locale
- title
- slug
- excerpt
- body HTML from Tiptap
- SEO title
- meta description
- open graph fields
- alt text for image, if needed

### Category

Canonical category node:

- parent category
- sort order
- status

### CategoryTranslation

Per-language category data:

- locale
- name
- slug
- description
- SEO fields

### Pivot Tables

- `post_category` for post to category many-to-many assignment
- optional `post_tag` later if tags are needed

### Media

Add a reusable media table so uploaded files can be linked to posts, categories, or future entities.

Recommended fields:

- `id`
- `disk`
- `path`
- `filename`
- `mime_type`
- `size`
- `alt_text`
- `title`
- `caption`
- `entity_type`
- `entity_id`
- `collection`
- `locale`
- `created_by`
- timestamps

## Language Strategy

### Default Language

Choose one locale as default, for example `en`.

### Read Behavior

When a requested locale is missing:

- use the translated row for that locale if it exists
- otherwise fall back to the default locale row

### Write Behavior

When creating content in a non-default locale:

- copy the default locale values into the new translation record
- let the editor replace them gradually

When updating the default locale:

- sync only empty translation fields in other locales
- do not overwrite translated content that already exists

This keeps the content usable even when only one language has been entered.

## Service Layer

### Required Services

- `PostService`
- `CategoryService`
- `TranslationService`
- `SeoService`
- `GeoService`
- `MediaService`
- `SlugService`

### Service Responsibilities

#### PostService

- create post records
- update post records
- delete posts
- attach categories
- save translations
- coordinate preview image and media links
- handle publish state

#### CategoryService

- create categories
- update categories
- manage parent-child hierarchy
- save translations
- ensure locale fallback

#### TranslationService

- normalize translation payloads
- copy default language content when a locale is empty
- merge fallback fields safely
- never overwrite existing translated values

#### SeoService

- generate and validate slugs
- normalize SEO title and description
- manage OG and Twitter metadata
- support locale-specific canonical logic

#### GeoService

- validate geo payloads
- store country/region/city data
- support optional coordinates

#### MediaService

- store uploaded images
- update metadata
- link uploaded media to any model
- return public URLs or signed URLs depending on disk
- support preview image and inline content image use cases

## Media Controller

Create a dedicated media controller for uploads and reuse.

Suggested responsibilities:

- accept image upload requests
- validate file type, dimensions, and size
- call `MediaService`
- return JSON for API usage
- return redirect or flash response for admin usage
- expose image lookup and delete endpoints if needed

Recommended actions:

- `store`
- `destroy`
- `show`
- `index` for browsing uploaded media

This controller should not contain storage logic directly.

## Tiptap Editor

Use Tiptap for post content editing.

Recommended storage:

- `content` longtext for canonical editor HTML
- optional `content` for cached rendering
- optional `body_text` for search indexing

Editor support:

- headings
- paragraphs
- lists
- links
- images
- code blocks
- quotes
- embeds

## SEO Support

Per locale, store:

- SEO title
- meta description
- canonical URL
- OG title
- OG description
- OG image
- Twitter card data
- robots flags

Also support:

- locale-aware slugs
- `hreflang`
- fallback canonical behavior

## Geo Support

Keep geo support flexible enough for future targeting.

Suggested fields:

- country
- region
- city
- latitude
- longitude
- timezone

If the geo data is more complex later, store the extra payload in JSON.

## Public Blog Screens

Build public pages for:

- homepage
- locale-based post list
- single post page
- category archive
- category tree navigation
- search results

These pages should resolve content by locale first, then fall back to the default locale.
Keep public blog Blade files under `resources/views/frontend/blog`.
Always render JSON-LD / knowledge graph data on every frontend blog page.

Future frontend user features:

- frontend users can sign up
- users can log in only after email or account verification
- verified logged-in users can add comments on blog posts
- comments must support threading
- replies must support unlimited reply depth, or a clearly defined nesting limit if needed later

## Admin Screens

Build admin screens using the existing letters UI style:

- blog dashboard
- post index
- post create
- post edit
- category index
- category create
- category edit
- media library
- SEO settings panel
- locale manager if needed

Form layout pattern:

- breadcrumb header
- summary card
- tabbed locale form sections
- image upload card
- SEO card
- publish card
- category multi-select card

## API Surface

Expose the same content through API endpoints using the same services.

Suggested endpoints:

- `GET /api/blog/posts`
- `GET /api/blog/posts/{slug}`
- `POST /api/blog/posts`
- `PUT /api/blog/posts/{id}`
- `DELETE /api/blog/posts/{id}`
- `GET /api/blog/categories`
- `POST /api/media`

API behavior:

- accept locale in request
- return localized payload
- apply fallback to default language
- validate via request classes
- enforce role-based authorization

## Validation

Use form requests and API request validation for:

- locale
- title
- slug
- category IDs
- image upload
- SEO fields
- geo fields
- body HTML

## Migrations

Create migrations for:

- posts
- post translations
- categories
- category translations
- post_category pivot
- media

If needed later:

- tags
- comments
- revisions

## Implementation Phases

### Phase 1

- define default locale
- define roles
- create blog migrations
- create core models and relationships

### Phase 2

- implement service layer
- implement translation fallback
- implement slug and SEO helpers
- implement media upload support

### Phase 3

- build admin post/category UI
- build media library UI
- keep the letters UI structure for cards, tables, and forms

### Phase 4

- build public blog pages
- add locale switching
- add category trees
- add search

### Phase 5

- expose API endpoints
- ensure API uses same services as web
- add authorization rules and tests

### Phase 6

- test fallback behavior
- test media reuse
- test multilingual post/category flows
- test role access

## Notes

- Do not put write logic in controllers.
- Do not duplicate blog logic between web and API.
- Keep the default language usable as the fallback source at all times.
- Use the existing `letters` UI as the design pattern for new admin screens.
- Search `https://ktui.io/docs` for any HTML component before introducing it into the admin UI.
