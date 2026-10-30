# Acceptance scenario catalog

Owner: R27. Reviewers: R05, R23, R28. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: AC-01–16. Open decisions: [register](../00-governance/assumptions-and-open-questions.md). Practice: [quality-documentation](../00-governance/skills/quality-documentation.md).

These specify later implementation acceptance. Current execution validates documentation fixtures only, not product behavior.

## AC-01 Session save/resume

Given: Owned run BRv1, acknowledged revision3. When: Pause/resume; new BRv2 exists. Then: Restore revision3/BRv1; live ownership; expired token safe recovery. Trace: FR-02, NEED-04. Verification: controlled fixture/manual task or integration/fault/concurrency exercise during implementation; record actual evidence and qualified reviewer.

## AC-02 Back-edit invalidation

Given: Web+mobile with offline package. When: Remove mobile or alter upstream option. Then: Dependent mobile answers/facts inactive and new charges removed; historical result unchanged. Trace: FR-05, RULE-CORE-03. Verification: controlled fixture/manual task or integration/fault/concurrency exercise during implementation; record actual evidence and qualified reviewer.

## AC-03 Version/history

Given: Old result/bookv1; new content/bookv2. When: Read old; explicit revise/reprice. Then: Old exact output unchanged; new result linked and visibly versioned; live revoke still denies. Trace: FR-14,17,27. Verification: controlled fixture/manual task or integration/fault/concurrency exercise during implementation; record actual evidence and qualified reviewer.

## AC-04 Price arithmetic

Given: C01 fixture PB-EXAMPLE-v1. When: Calculate role lines and adjustment order. Then: 320 h, B17600, C1760, D968, N18392; tax excluded; missing rate price=null;0.005 line rounding explicit. Trace: FR-12,13. Verification: controlled fixture/manual task or integration/fault/concurrency exercise during implementation; record actual evidence and qualified reviewer.

## AC-05 Schedule/capacity

Given: C01 or platform allocation fixture. When: Sum role/person/week and dependency dates. Then: Integer distinct people; no person>availability; C01 5 people, peak2,8 weeks; not effort/headcount. Trace: FR-12. Verification: controlled fixture/manual task or integration/fault/concurrency exercise during implementation; record actual evidence and qualified reviewer.

## AC-06 Locale parity

Given: Same canonical answers and context in 14 named language entries. When: Switch en/fa and source hash edit. Then: Same facts/money; correct bidi/formatting; stale critical copy blocks; no linguistic approval inferred. Trace: FR-21,26. Verification: controlled fixture/manual task or integration/fault/concurrency exercise during implementation; record actual evidence and qualified reviewer.

## AC-07 Ownership/security

Given: Foreign ID or revoked principal; queued job exists. When: Read/edit/export/download/reuse/support. Then: Deny scoped access and queued delivery; no raw data leakage; sanitize audit. Trace: FR-23,27. Verification: controlled fixture/manual task or integration/fault/concurrency exercise during implementation; record actual evidence and qualified reviewer.

## AC-08 Publication

Given: Draft with undefined option/cycle/missing critical text or rate. When: Validate/review/publish. Then: Block invalid manifest; old release active; changed hash invalidates approval; valid atomic activation. Trace: FR-18–22. Verification: controlled fixture/manual task or integration/fault/concurrency exercise during implementation; record actual evidence and qualified reviewer.

## AC-09 Unknown/conflict

Given: Unknown API/rate or incompatible hard budget/date/scope. When: Review/generate. Then: Explicit unresolved/manual-review/partial result; unknown not zero/no; no guarantee. Trace: FR-04,08,11. Verification: controlled fixture/manual task or integration/fault/concurrency exercise during implementation; record actual evidence and qualified reviewer.

## AC-10 Export failure

Given: Structured report exists, renderer fails. When: Retry same export key; request unauthorized URL. Then: Structured report accessible; bounded retry, no duplicate calculation; foreign download denied. Trace: FR-15,24. Verification: controlled fixture/manual task or integration/fault/concurrency exercise during implementation; record actual evidence and qualified reviewer.

## AC-11 Accessibility

Given: Public/admin full task in target devices/scripts. When: Keyboard, screen reader, zoom, reduced-motion checks. Then: Labels/focus/errors/status/reflow/contrast and target criteria verified later; static colors alone insufficient. Trace: FR-26, NFR-01. Verification: controlled fixture/manual task or integration/fault/concurrency exercise during implementation; record actual evidence and qualified reviewer.

## AC-12 Matching

Given: Mandatory prerequisite unmet or all candidates excluded. When: Run matching/tier preference. Then: No ineligible winner; no_match/manual_review; skill distinct from package tier. Trace: FR-10. Verification: controlled fixture/manual task or integration/fault/concurrency exercise during implementation; record actual evidence and qualified reviewer.

## AC-13 Startup evidence

Given: Once-only model or self-report conflict/known blocker. When: Evaluate guidance. Then: No universal success score; C01 buying cycle disables23; known blocker first; clarify conflict. Trace: FR-07. Verification: controlled fixture/manual task or integration/fault/concurrency exercise during implementation; record actual evidence and qualified reviewer.

## AC-14 Workload/architecture

Given: Quantified registrations, DAU, concurrency, RPS, messages, data/recovery. When: Compare standard/custom/mobile/realtime alternatives. Then: All units/horizon/basis explicit; no microservices solely from account count; prove candidate constraints. Trace: FR-08,09. Verification: controlled fixture/manual task or integration/fault/concurrency exercise during implementation; record actual evidence and qualified reviewer.

## AC-15 Concurrency/idempotency

Given: Expected revision3; two writers; duplicate operation key. When: Concurrent save/generation. Then: One save revision4, other412; same key/payload same operation; changed payload409; transaction atomic. Trace: FR-06,15. Verification: controlled fixture/manual task or integration/fault/concurrency exercise during implementation; record actual evidence and qualified reviewer.

## AC-16 Free platform

Given: Any supported questionnaire/report/export. When: Complete/revise/export. Then: No checkout, billing, payment capture, paid entitlement or purchase authorization; project price informational. Trace: FR-29. Verification: controlled fixture/manual task or integration/fault/concurrency exercise during implementation; record actual evidence and qualified reviewer.
