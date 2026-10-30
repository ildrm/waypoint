# Implementation handover

Status date:2026-10-30 (Asia/Tehran). The latest user request authorized implementation of implementation-super-mega-prompt.md. Historical documentation-only instructions are preserved as source material. No production site was deployed and no external account, communication, real transaction, or customer dataset was used.

## Delivered behavior

| Capability | Local implementation | Source / modules |
|---|---|---|
| Public assessments | All123 documented questions; one question/record at a time; no substantive default; known/unknown/not-decided; conditional branches; active progress; validation; back edits; review | docs/03-workflows; FlowEngine, Predicate, AnswerValidator, BatchAnswers; Blade views |
| Recovery and versions | Private recovery codes; server ownership; expiry; optimistic revisions; atomic/idempotent starts, answers, reports, forks and repricing | RunService, RunAccess, Idempotent, ProjectAccess |
| Startup | Blockers before evidence experiments; scoped strengths and15-stage evidence; action owner/measure/decision/review fields; no universal score or success probability | ReportBuilder; full startup bank |
| Software discovery | Web/mobile combinations; feature matrix; constraints, candidate architecture/rejected alternatives; quantified workload records; unknowns; configured WBS/rates; feasible capacity scheduling | SoftwareDiscovery, ProjectEstimator, ExactMoney, CapacityScheduler |
| Matching | Approved pinned catalog; mandatory eligibility; insufficient evidence/no-match/manual-review; stable ties; declared context separate from measured proficiency | Predicate, ReportBuilder, CatalogItem |
| Reports and scenarios | Immutable inputs/output; old book/release continuity; linked forks and explicit repricing; structured JSON and printable HTML | ReportService, AssessmentReport, RenderExport |
| Administration | Flow creation/edit/clone/retire; structured question/rule editing; simulation and trace; independent review; local preview; publication/history/rollback; catalog and price lifecycle | Filament Workspace; AdminWorkspace; PublicationService |
| Parameters | Separate general defaults and flow overrides; strict types, units/bounds/null rules; effective origin; explicit zero/false/null; reset; release snapshots | SettingsResolver; parameter registry; admin forms |
| Language operations | Exactly14 locale tags; en/fa/fr/de/ru/hi/zh/ja/es/pt/ar/tr/hy/it; RTL/LTR; ICU and Laravel placeholders; stale source hashes; independent review; all14 critical-copy gate | TranslationManager, DictionaryLoader, LocaleFormat, SourceCopy |
| Support / privacy | Recorded help/export/deletion requests, explicit consent, authorized review, scoped erasure, live revocation on requests and queued exports | PrivacyService, SupportRequest, RenderExport |
| Operational tooling | Health, queue, scheduler, backup/restore exercise, retention dry run/apply, safe staff bootstrap/password reset, audit and answer-free events | routes/console.php; BackupService; Audit |
| API | Cookie/CSRF auth; canonical camelCase requests; atomic PUT answer batches; strong ETags; idempotency; operation polling; confirmed fact reuse; MFA-protected publication | ApiWireController; runtime OpenAPI; ApiWireTest |

## Ready locally versus production gates

| Item | Exact state / safe behavior | Required next action |
|---|---|---|
| Source language | English is a reversible development default; not an owner-approved publication source | Confirm source language and source copy |
|14 languages | Infrastructure and review workflow exist; no native-reviewed target translations were supplied. Even English source is draft. Source preview is clearly labeled | Supply/review critical UI, validation, question, option, field, report and catalog copy; clear all14 coverage gates |
| Locale variants / markets | zh script/region, pt region, operating markets and pricing currency remain undecided | Confirm these independently of language |
| Real estimates | No approved real effort/rate book exists. Missing coefficients/rates produce incomplete/manual-review results and null authoritative totals | Enter scoped work packages, rates/currency/exponent, periods, eligibility, calendars and commercial approval |
| Real catalog | Only two explicitly fictional local demonstration options are present | Curate offerings, eligibility, alternatives and declared dimensions; independently review; publish a new release |
| Pricing publication | Synthetic C01 is development-only. It reconciles320h,17600 base,1760 contingency,968 discount,18392 USD net; internal10280 remains private | Do not treat this fixture as a quote or production price |
| Retention / legal policy |7-day anonymous expiry is a local proposal. Automatic personal-data erasure is blocked without RETENTION_POLICY_APPROVED. Verified requested erasure is implemented | Approve retention/deletion/audit/backups, operational responsibility and jurisdiction-specific policy |
| Platform organization | One operator with user/anonymous ownership; project contexts do not infer customer tenancy from questionnaire answers | Confirm whether the platform itself requires separate customer organizations before production |
| PDF | JSON and printable HTML work. A durable multiscript PDF renderer/font review has not been supplied | Choose/review renderer and licensed font coverage, render representative14-script content; enable only after verification |
| Authentication | Local staff bootstrap, MFA, role checks and password/session revocation implemented; public account linking works for existing accounts | Decide any public registration, email verification/delivery and recovery policy; no outbound mail service was connected |
| Runtime / infrastructure | SQLite/local PHP verified. Production PostgreSQL, hosting, TLS, mail, monitoring and restore under production conditions remain unvalidated | Choose supported infrastructure; upgrade PHP patch/runtime, run upstream advisory checks and full deployment qualification |
| Accessibility / performance | Automated and representative browser checks recorded; no native screen-reader audit, production load test, field CWV result, or compliance certification | Complete representative assistive-technology, script/font and production-device/load qualification |

## Important contracts

- Answers, release manifests, report snapshots and public sell rates are pinned where required. A new rate or changed scope produces a new linked report. Live ownership, disabled users, suspension, expiry and erasure still apply to old IDs and queued/downloaded exports.
- Money uses decimal strings and BCMath; per-line half-up rounding precedes eligible contingency and discount. Tax excluded/unconfigured differs from tax-free. Recurring charges are separate. Margin is not added twice or used to rank matches.
- Schedules allocate declared combined person capacity across dependency work. Contributors may cover several roles; they are counted once and never overbooked. Weekly peak means contributors active in the same calendar week. Planning ranges and uniform weekly allocation are explicit expert assumptions, not calibrated confidence bounds.
- Freeform legacy capacity notes remain evidence, not invented staffing. Structured contributor rows supply aliases, role codes, hours and calendar limits; uncertain staffing yields manual review.
- Startup source enum values such as A/B are interpreted only with a stable Q-ST-* ID and its documented meaning. They are not positional display identifiers, an ordinal score, or a universal strength ranking.
- Business release mutations serialize on the business-lock row. Security revocation is live rather than pinned into business settings. Approved and retired financial data remain immutable; clone a new version.

## Evidence and limitations

See verification.md for actual executed checks and links. In-memory translation approvals used by tests are explicitly test doubles. They are not linguistic approval. Frontend review used a fresh same-provider evaluator because a different provider was unavailable; it is not an external human review.

Read-only parameter definitions follow the supplied registry; staff edit typed values/overrides rather than turn configuration into executable code. Advanced customer-project integrations are assessed as requirements; no real payment/AI/vendor integration was connected.
