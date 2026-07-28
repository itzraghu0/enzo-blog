---
name: ktui-admin-ui
description: Laravel admin UI work for this project using the existing letters views as the visual baseline. Use when creating or changing Blade admin pages, forms, tables, nav items, breadcrumbs, or layout components, especially before introducing any HTML component that should be checked against KTUI docs first.
---

# KTUI Admin UI

Use this skill for Blade-based admin screens in this project.

## UI Source of Truth

- Use `resources/views/letters` as the primary visual reference.
- Reuse the existing layout patterns:
  - `layouts.admin.app`
  - breadcrumb header blocks
  - `kt-card` sections
  - `kt-btn` actions
  - `select2` inputs
  - `flatpickr` date inputs
  - table-driven list views

## Required Workflow

Before introducing any new HTML component:

1. Search `https://ktui.io/docs` for the component or closest KTUI equivalent.
2. Prefer the KTUI pattern over a custom one when it exists.
3. Match the existing letters view spacing, card structure, and typography.

## Build Rules

- Keep admin views localized with `__()`.
- Keep markup consistent with the existing project layout.
- Prefer Blade sections and includes over inline duplication.
- Preserve responsive behavior from the current admin templates.
- Use reusable partials for repeated table rows, filters, and form groups.

## Practical Patterns

- For list pages, use a header card, search/filter card, and table card.
- For create/edit pages, use grouped cards with clear section titles.
- For destructive actions, show a confirmation step.
- For multi-select relations, use `select2`.
- For date fields, use `flatpickr`.

## Avoid

- Do not introduce a new component style without checking KTUI first.
- Do not drift away from the letters UI baseline unless the project explicitly changes direction.
