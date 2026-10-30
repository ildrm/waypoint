# Authoring, localization and commercial operations

Use /admin after interactive staff bootstrap and MFA. The interface language is session-based; edited translation language is a separate locale filter.

## Flows and releases

Clone an existing documented bank or create a structured draft. Stable question IDs, semantic keys and option IDs must remain unique. Questions need wording/help, stage/purpose, translation keys, input_type, JSON schema and applicability. Supported public presentations are select_one, select_many and record. Record fields support explicit nullable values; no substantive option is preselected.

The editor accepts structured JSON, not executable templates. Graph validation rejects circular/undefined references, unsafe schema references, unreviewed regular expressions, excessive depth/size and unsupported operators/effects. Applicability uses deterministic three-valued predicates. Unknown comparisons remain pending; inactive facts are excluded.

Predicates: always, all, any, not, eq/ne/in/contains, numeric gt/gte/lt/lte, known/unknown/applicability checks, context_eq and conflict references. Rule effects are category-bound: validation findings, recommendations, scope items, declared classifications, pricing eligibility and completion review; derived constants are scalar/null. Stored PHP/SQL/JavaScript, arbitrary templates, remote schemas and eval are not supported.

Simulate with canonical answer records and inspect active questions, conflicts, matched rules and typed effects. Review the exact hash after edits. The latest editor cannot approve their own change. A publisher activates a new immutable release only after structural, settings, operating-policy and14-locale critical-copy checks. Editing a draft retains its previous active release for new public users. Rollback changes the active pointer for future starts; old runs keep their release.

## Language workflow

The configured tags are exactly en,fa,fr,de,ru,hi,zh,ja,es,pt,ar,tr,hy,it. Persian and Arabic use RTL layout. Source English is a proposal. Do not infer market/currency, qualification or legal jurisdiction from a tag.

~~~powershell
rtk proxy php artisan platform:translations
~~~

This synchronizes source keys/hashes and prints coverage. It does not translate or approve content. Edit a target text, preserve ICU {name} and Laravel :name placeholders, then have a separately authorized language reviewer approve it. Source or target edits invalidate approval. Missing/stale critical copy prevents production publication. No unreviewed foreign fallback is presented as ready. Local source preview visibly identifies its English content, with auto-isolated text inside RTL layouts.

UI strings are discovered from controlled application/view copy, framework validation text and approved catalog/rule copy. Question wording/help/options/record labels and metadata have stable source keys. Pinned business translations and report output survive later edits; current interface strings still require current approval. Workers reset translator caches and use the saved report locale/dictionary.

## Settings

General parameters apply across flows. Flow overrides are separate records; absence inherits, while false, zero and allowed null are explicit values. Definitions display type, unit, bounds/enum, nullable/override policy and revision. Financial defaults/overrides require commercial authority. Reset removes an override. Releases snapshot effective values and origins.

The supplied registry is code-controlled: definitions are not operational security settings and cannot grant staff permissions. Changes to authentication/roles are live and are never frozen in a business release.

## Price books and catalog

No real rate or offer is seeded. Work-package IDs must be unique; dependencies acyclic; role hours are integers or exact decimal strings, with an explicit rate for each role. Prices use configured currency/exponent, eligibility, effective dates, disclosed contingency/discount, recurring periods and validity. Missing or overlapping approved context yields review, not a fabricated amount.

A separate commercial reviewer approves a draft; approved or retired money/scope cannot be edited in place. Clone a new version, retire overlapping activation, then approve. Historical reports retain sell-rate snapshots, exponent, calculation trace and amount. Private internal rates/cost traces never appear in public views, DTOs or exports.

C01 synthetic fixtures are opt-in via platform:demo and local/test-only. Their QA gate splits56 pre-release and8 post-release hours; total effort stays320. Scheduling uses explicit40h/person/week capacity in the fixture and yields8 weeks,5 distinct people and peak2 active contributors. It may allocate hours differently from the illustrative hand-written calendar; no person exceeds capacity.

Approved catalog entries declare domain, mandatory eligibility, deliverables, preference metrics and separate declared dimensions. No measured proficiency is inferred from price or project complexity. Equal metrics retain all eligible alternatives. Publish a new flow release to pin changed catalog data.

### Exact quantity and effort configuration

Static labor uses role_hours, keyed by a role with a configured rate. Additional labor can use role_work. A quantity source names a stable question_id, an optional dot-separated record path, and optional aggregation: count for a selected-item list. Numeric quantities must be integers or exact decimal strings. For example:

~~~json
{
  "id": "configured-build",
  "role_hours": {"DEV": 4},
  "role_work": {
    "DEV": {
      "quantity_from": {"question_id": "Q-SW-CONTENT", "path": "pages_launch"},
      "hours_per_unit": "2"
    }
  },
  "fixed_lines": [{
    "id": "content-transfer",
    "unit": "pages",
    "quantity_from": {"question_id": "Q-SW-CONTENT", "path": "pages_launch"},
    "included_quantity": "5",
    "minimum_quantity": "3",
    "minimum_amount": "40",
    "rate": "10"
  }]
}
~~~

These numbers illustrate syntax only. Do not use them as real coefficients without commercial review. Billable quantity is max(0, source quantity minus included quantity). A positive quantity may be raised to minimum_quantity; a positive charge may be raised to minimum_amount. Included quantities of zero cost remain zero even when a minimum exists. Shared fee IDs must be unique across a book.

Contingency and discount eligibility are independent per package/line. Only discount-eligible contingency enters the discount base. Recurring entries declare day/week/month/year and either an exact amount or the same quantity/rate terms; they never enter the one-time total. Put internal unit costs in internal_rates keyed by labor role or fixed fee ID. A missing internal rate leaves private cost incomplete rather than using the selling rate.

Optional role_ranges provide explicit low/high hours per role. They appear only when every included labor role has a range containing its computed base. Unknown drivers, pending package conditions and absent required predecessors yield manual review with null authoritative price/effort/calendar. Work packages must represent agreed scope; a complete package calculation does not prove every customer requirement has a calibrated coefficient.

Hard initial budget caps, unconfigured taxes within a tax-inclusive cap, and mandatory-date/capacity conflicts remain explicit review findings. Calendar comparison assumes planning starts at report generation; confirm actual start dates, approval windows and contractor lead times before a commitment.

## API

See openapi.yaml here for the implemented contract and api-notes.md for adaptations to the proposed original. Use session cookies plus X-CSRF-TOKEN; ETags are quoted aggregate revisions. Canonical PUT answer batches are all-or-nothing and order dependencies before children. Confirmed reuse verifies source ownership, revision, compatible meaning, current schema and confirmation age. Never copy another principal's run ID as evidence.
