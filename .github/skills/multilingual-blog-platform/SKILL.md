---
name: multilingual-blog-platform
description: Multilingual Laravel blog architecture with role-based authors, category trees, reusable media uploads, Tiptap content, SEO, geo metadata, default-locale fallback, and service-layer write flows shared by web and API. Use when creating or changing blog models, migrations, services, validation, controllers, translation behavior, media handling, or blog API endpoints.
---

# Multilingual Blog Platform

Use this skill for the blog domain in this project.

## Core Rules

- Keep all write operations in services.
- Keep controllers thin.
- Reuse the same services from web controllers and API controllers.
- Treat the default locale as the source of truth.
- When a non-default locale has missing data, seed or copy only the empty fields from the default locale.
- Never overwrite existing translated content during fallback sync.

## Domain Shape

- `Post` is the canonical record.
- `PostTranslation` stores locale-specific fields such as title, slug, excerpt, body, SEO, and open graph data.
- `Category` is hierarchical and can have a parent category.
- `CategoryTranslation` stores locale-specific category fields.
- Use a pivot table for many-to-many post-to-category assignment.
- Use a reusable media model for images and attachments that can be linked to any entity.

## Service Layer

Prefer these services:

- `PostService`
- `CategoryService`
- `TranslationService`
- `MediaService`
- `SeoService`
- `GeoService`
- `SlugService`

Typical responsibilities:

- create, update, delete, and publish posts
- sync category assignments
- copy default-language values into empty translation fields
- generate locale-aware slugs
- normalize SEO and geo payloads
- upload and attach media files

## Content Editing

- Use Tiptap for rich content.
- Store the canonical editor payload as JSON.
- Optionally cache rendered HTML or plain text for search.
- Sanitize rendered output.

## SEO and Geo

- Keep SEO fields locale-aware.
- Support preview image metadata and alt text.
- Store geo data in dedicated columns or JSON when the shape is flexible.

## API Parity

- Use the same service methods for both admin web and API endpoints.
- Validate with request classes.
- Return locale-aware payloads.
- Apply the same fallback rules in both surfaces.

## UI Coordination

- When building admin screens for this domain, follow the letters views as the UI baseline.
- If new HTML components are needed, check `ktui-admin-ui` first.
