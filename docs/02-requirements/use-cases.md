# Textual use cases

Owner: R02. Reviewers: R05, R27. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: UC-01–11. Open decisions: [register](../00-governance/assumptions-and-open-questions.md). Practice: [workflow-questionnaire](../00-governance/skills/workflow-questionnaire.md).

## UC-01 Start/resume

Actors: User. Trigger: Flow selection or saved run.

Preconditions/permissions: Published supported release; live ownership/token valid; server-side ownership/role checks apply throughout.

Main scenario: Choose locale/purpose; create or load run; pin manifest; show saved revision and next applicable question.

Alternatives/errors: Unsupported locale explains alternatives; expired resume token uses safe recovery; revoked ownership denies.

Postconditions: Pinned authorized run restored. Data changes: Run/ownership/audit. Acceptance: AC-01,03,07.

## UC-02 Answer/edit

Actors: Run owner. Trigger: Save or back edit.

Preconditions/permissions: Active question, typed answer and expected revision; server-side ownership/role checks apply throughout.

Main scenario: Validate; recompute dependency closure; save and invalidate atomically; return new revision and active next step.

Alternatives/errors: 422 invalid fields;412 stale ETag with diff; offline shows unsaved; unknown preserved.

Postconditions: No hidden selections affect new outputs. Data changes: Answer/fact revisions and trace. Acceptance: AC-02,09,15.

## UC-03 Generate/export

Actors: Run owner. Trigger: Confirms review.

Preconditions/permissions: Material conflicts resolved or partial mode acknowledged; live auth; server-side ownership/role checks apply throughout.

Main scenario: Snapshot input/config/price book; calculate structured output; queue report/export; authorize download.

Alternatives/errors: Missing rate incomplete price; duplicate key same job; export failure leaves structured report.

Postconditions: Immutable sourced result revision. Data changes: Result/job/snapshot. Acceptance: AC-03,04,10,15.

## UC-04 Compare/reprice

Actors: Run owner. Trigger: Creates alternative or refresh.

Preconditions/permissions: Accessible baseline result; server-side ownership/role checks apply throughout.

Main scenario: Fork changed answers; re-evaluate applicability; calculate explicit current commercial version; show deltas.

Alternatives/errors: Unsupported integration manual review; different currencies not subtracted without approved conversion.

Postconditions: Old result preserved and new revision labeled. Data changes: Scenario/result links. Acceptance: AC-02,04,05.

## UC-05 Author/publish

Actors: Author/reviewer/publisher. Trigger: Change request.

Preconditions/permissions: Least-privilege staff role; draft based on release; server-side ownership/role checks apply throughout.

Main scenario: Clone draft; edit stable entities; simulate; validate rules/translations/catalog; reviewer inspects exact diff; publish atomically.

Alternatives/errors: Self-approval policy; stale diff invalidates review; failed checks keep prior release.

Postconditions: New manifest active, history immutable. Data changes: Draft/review/release/audit. Acceptance: AC-03,08.

## UC-06 Translate

Actors: Translator/language reviewer. Trigger: Source edit or missing copy.

Preconditions/permissions: Assigned locale; exact source hash; server-side ownership/role checks apply throughout.

Main scenario: Translate keys/ICU placeholders; assess equivalence/formatting; approve exact hash; include coverage.

Alternatives/errors: Source edit makes approval stale; critical missing copy blocks locale; no silent fallback.

Postconditions: Approved content remains traceable. Data changes: Translation/source hash/review. Acceptance: AC-06,08.

## UC-07 Maintain estimate prices

Actors: Price editor/commercial approver. Trigger: Rate/catalog adjustment.

Preconditions/permissions: Currency/market/effective dates and authority defined; server-side ownership/role checks apply throughout.

Main scenario: Edit exact decimals; validate overlaps/limits; preview cases; approve version; activate effective book.

Alternatives/errors: Missing role rate blocks price; conflicting dates block publication; history not repriced.

Postconditions: New estimates snapshot new book; no platform payment. Data changes: Price book/approval/audit. Acceptance: AC-03,04,08,16.

## UC-08 Change settings

Actors: Settings editor/publisher. Trigger: Default/override change.

Preconditions/permissions: Typed allowlisted definition; server-side ownership/role checks apply throughout.

Main scenario: Edit scoped value; resolve effective inheritance; preview affected flows; validate; publish manifest.

Alternatives/errors: Wrong type/disallowed scope/cycle reject; security revocation stays live.

Postconditions: Pinned runs retain business settings. Data changes: Parameter/override/release. Acceptance: AC-03,08.

## UC-09 Support/privacy

Actors: User/support/privacy operator. Trigger: Recovery/export/deletion request.

Preconditions/permissions: Verify requester identity and scoped authority; server-side ownership/role checks apply throughout.

Main scenario: Inspect allowed diagnostics; guide user correction; fulfill audited request; report status.

Alternatives/errors: No staff impersonated answers; failed export retry; policy/legal hold needs escalation.

Postconditions: Fulfilled or explicit pending state with reason. Data changes: Request/audit/export/deletion. Acceptance: AC-07,10.

## UC-10 Startup assessment

Actors: Founder. Trigger: Chooses venture guidance.

Preconditions/permissions: One venture/segment/time context; server-side ownership/role checks apply throughout.

Main scenario: Answer15-stage evidence bank; clarify inconsistencies; identify known blockers; prioritize next evidence/action.

Alternatives/errors: Unknown yields evidence task; one-time buying cycle avoids false churn; claims self-reported.

Postconditions: Action report without probability of success. Data changes: Startup facts/result. Acceptance: AC-09,13.

## UC-11 Service matching

Actors: Service seeker. Trigger: Requests suitable help.

Preconditions/permissions: Published catalog with eligibility and exclusions; server-side ownership/role checks apply throughout.

Main scenario: Collect need/prerequisites/constraints; filter hard constraints; compare eligible options; explain next action.

Alternatives/errors: No-match explicit; incomplete evidence manual review; proficiency placement conditional.

Postconditions: Sourced match/no-match output. Data changes: Matching facts/catalog/trace. Acceptance: AC-09,12.
