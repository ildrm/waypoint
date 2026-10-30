# Implementation acceptance traceability

Status date:2026-10-30, Asia/Tehran. This is runtime implementation evidence, separate from the historical documentation validation under docs/17-audit. The original [acceptance catalog](../13-quality/acceptance-scenarios.md) is preserved. Verification below means the named local evidence, not full production acceptance.

| Acceptance / requirements | Implementation | Executed evidence and limits |
|---|---|---|
| AC-01; FR-01–03: start/save/resume | RunService, RunAccess, ProjectAccess, public recovery forms | WorkflowTest public journey; EstimateAssuranceTest recovery ownership/revocation; browser recovery/review. Seven-day anonymous expiry is a proposal. |
| AC-02; FR-04–06,16: back edits/unknowns | FlowEngine, Predicate, AnswerValidator, BatchAnswers | Hidden mobile invalidation/history; all123 banks; atomic dependency-ordered batches; false/zero/null distinctions. |
| AC-03; FR-14,16,17,22,27: versions | FlowRelease, AssessmentReport, ReportService, PublicationService | Old-price preservation, linked repricing, immutable release/report, pinned settings and live revocation; API revision tests. |
| AC-04; FR-12,13: estimates | ExactMoney, ProjectEstimator, PriceBook | C01/rounding/exponents/eligible bases; dynamic work, inclusions/minimums, recurring separation/private costs. Real business coefficients absent. |
| AC-05; FR-12,16: schedule/people | CapacityInput, CapacityScheduler | C01 eight weeks/five people/peak two; combined multi-role person capacity, dependencies/holidays and missing-role null schedule; disclosed uniform-week assumptions. |
| AC-06; FR-21,26:14 locales | TranslationManager, SourceCopy, DictionaryLoader, LocaleFormat, RTL views | Hash/placeholder/publication checks; large exact decimals/Persian digits; locale switch; queued saved-locale test; browser RTL. Native target-language approval remains gated. |
| AC-07; FR-23–25,27: ownership/privacy | RunAccess, capabilities, session checks, PrivacyService, Audit, export worker | Foreign read/export/reuse denial; MFA/password revocation; erasure/history/artifacts; generic API404; actual staff password+TOTP browser task; CSRF-free POST419. No penetration-test claim. |
| AC-08; FR-18–23: authoring | Structured bank/rule editor, simulator, SettingsResolver, AdminWorkspace, PublicationService, BusinessLock | Schema/operator/cycle rejection, independent price/review authority,14-locale gate, pinned settings, rollback/history; authorized admin sections and staff DOM contracts. Shared content is materialized into immutable bank snapshots, never live-mutated across releases. |
| AC-09; FR-04,08,11: uncertainty | FlowEngine, RuleEffects, ConstraintReview, ReportBuilder | Missing rates/capacity/quantity null; hard budget/tax/date findings; exact C07 source-question/selected-label HTTP test. |
| AC-10; FR-15,24: exports | RenderExport, ExportJob, private authorized storage | Retry/revocation/structured-report checks; worker locale isolation; JSON/HTML implemented. PDF blocked pending renderer/fonts. |
| AC-11; FR-26 / NFR-01: UI | Semantic Blade, original CSS/JS, Filament | Public/admin DOM contracts; desktop/tablet/mobile screenshots, keyboard tasks, reduced-motion RTL and representative axe checks; fresh evaluator. Native screen-reader/all14-language task evaluations unperformed. |
| AC-12; FR-10: matching | Pinned CatalogItem, Predicate, ReportBuilder | Eligibility/empty result, ties and missing metrics unranked/manual review; fictional browser match. No measured proficiency claim. |
| AC-13; FR-07: startup | Complete15-stage bank, blockers, action plan, clarifications | Once-only purchase disables retention; blocker precedence; unknown evidence produces actions without score/probability; exact C07 source labels. |
| AC-14; FR-08,09: architecture | Full75-question software bank, SoftwareDiscovery, ReportBuilder | Known values across C01–C06 pass real schemas; per-surface matrix/constraints/alternatives retain sources/units. Customer architecture remains preliminary with validation needs. |
| AC-15; FR-06,15: atomicity | Idempotent, transactions, revision predicates, BusinessLock, SerializingSQLiteConnection | Stale412, payload409, exact replay, atomic batch rollback and operation tests. ConcurrencyTest uses two real processes and an overlap handshake to verify local SQLite waits and commits both serialized writes. Production-database contention/load remains unqualified. |
| AC-16; FR-29: free platform | No billing/payment credentials/entitlements; free UI/export | Route/schema/source inspection and uncharged browser journeys. Informational customer-project quotes never authorize payment. |
| FR-28: optional AI | Deferred by specification | No AI account or model-controlled price. |

Test approvals and synthetic rates/catalog data are fixtures, not linguistic, commercial, legal or human acceptance. See [verification](verification.md) for commands/artifacts and [handover](handover.md) for production gates.
