# Functional requirements

Owner: R02. Reviewers: R05, R17, R27. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: FR-01–29. Open decisions: [register](../00-governance/assumptions-and-open-questions.md). Practice: [product-requirements](../00-governance/skills/product-requirements.md).

Each row is an atomic requirement for its applicable scope. Verification uses the named acceptance scenario and later implementation evidence. FR-28 is conditional/deferred.

| ID | Required behavior | Acceptance |
|---|---|---|
| FR-01 | Select a published flow and supported locale with a purpose/privacy notice | AC-01,06 |
| FR-02 | Start/resume a run with pinned business release and live scoped ownership checks | AC-01,03,07 |
| FR-03 | Render typed stable-ID questions with bounded multiselect and quantified units | AC-09,14 |
| FR-04 | Keep unknown, zero, no, not-decided, inactive and unanswered distinct | AC-09 |
| FR-05 | Invalidate dependent answers/facts on back edits before new scope or pricing | AC-02 |
| FR-06 | Validate applicability/schema and expected answer revision on server; resolve concurrent conflicts | AC-02,15 |
| FR-07 | Preserve15-stage startup evidence assessment and blocker precedence without a universal success score | AC-13 |
| FR-08 | Elicit all applicable software A–J categories, including measurable workload and device/integration details | AC-09,14 |
| FR-09 | Explain architecture alternatives using answer IDs, constraints and assumptions; PHP restriction only for platform | AC-14 |
| FR-10 | Match eligibility/exclusions before tier; no-match/manual-review remains valid | AC-12 |
| FR-11 | Review mandatory/optional per-surface scope, assumptions and material conflicts before results | AC-09 |
| FR-12 | Derive WBS effort, customer-project price, dependency schedule and staffing separately | AC-04,05 |
| FR-13 | Use admin role/fixed/unit rates and approved adjustments; missing rates yield incomplete price | AC-04 |
| FR-14 | Snapshot active inputs, versions, exact lines and assumptions in immutable result revisions | AC-03,04 |
| FR-15 | Generate/export idempotently under live authorization; retain structured report if export fails | AC-10,15 |
| FR-16 | Fork scenarios and show changed scope, price, recurring cost, schedule and staffing | AC-02,05 |
| FR-17 | Refresh expired prices only as explicit new revisions preserving original results | AC-03,04 |
| FR-18 | Manage flows/stages/questions/options/shared library references through reviewed drafts | AC-08 |
| FR-19 | Administer restricted navigation/classification/recommendation/pricing rules with dry-run traces | AC-08 |
| FR-20 | Resolve typed shared defaults and permitted flow overrides with provenance and impact previews | AC-08 |
| FR-21 | Manage translations by source hash and review state; critical missing/stale text blocks publication | AC-06,08 |
| FR-22 | Publish atomic validated releases; retire/roll back without rewriting historical results | AC-03,08 |
| FR-23 | Separate author/reviewer/publisher/commercial/support permissions and audit changes | AC-07,08 |
| FR-24 | Perform scoped support recovery/import/export/privacy requests with explicit failure reports | AC-07,10 |
| FR-25 | Measure purpose-limited analytics without raw answers or tokens | AC-07 |
| FR-26 | Support exactly 14 named languages with responsive accessible RTL/LTR screens and localized reports | AC-06,11 |
| FR-27 | Recheck live revocations on old sessions, jobs and downloads | AC-07 |
| FR-28 | Optional AI is deferred; any later narrative must use approved facts and cannot set prices | AC-09 |
| FR-29 | Provide platform use free of charge: no checkout, billing, payment capture, subscription entitlement or paywall | AC-16 |

FR-29 is owner-confirmed. Customer-project prices and commerce requirements remain informational outputs about evaluated projects; the platform never collects those payments. Changes must trace to questions/rules/outputs and affected acceptance records.
