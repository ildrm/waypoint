# Screen inventory and state coverage

Owner: R08. Reviewers: R09, R13, R19. Status: proposed, 2026-10-29. Sources: prompt §§13,15; [visual profile](../00-governance/skills/visual-system-motion.md). Related: UI-01–UI-15, AC-06, AC-11. Open decisions: brand and source/region/script choices.

| ID | Screen / primary action | Required states and content | Artifact |
|---|---|---|---|
| UI-01 | Flow choice / Start | Available, unavailable locale, empty, resume, loading/failure | [Desktop LTR](mockups/01-flow-choice.svg) |
| UI-02 | Question / Next | Unselected, selected, focus, invalid, unknown, saving, saved, offline | [Question + clarification](mockups/02-question-clarification.svg) |
| UI-03 | Clarification / Save detail | Optional help, required unit, uncertainty, rejection | Included in UI-02 |
| UI-04 | Review / Generate result | Complete, unknown, conflict, dependent-answer impact | [Mobile LTR](mockups/03-answer-review.svg) |
| UI-05 | Result / Next action | Complete, partial, missing prices, review, stale, expired, export failure | [Desktop LTR](mockups/04-result-estimate.svg) |
| UI-06 | Scenarios / Adopt revision | No alternative, comparable, incompatible currency/config, changed input | Explicit scope differences; no commercial-margin winner |
| UI-07 | Saved sessions / Resume | Empty, active, expired, revoked, concurrent edit | Project/run/version and last acknowledged save |
| UI-08 | Admin editor / Simulate | Draft, invalid rule, conflict, dirty, saved, published read-only | [Desktop LTR](mockups/05-admin-flow-editor.svg) |
| UI-09 | Translation matrix / Submit review | Missing, draft, reviewed, approved, stale, blocked | [Desktop RTL](mockups/06-translation-pricing.svg) |
| UI-10 | Price editor / Validate | Inherited, override, missing rate, overlap, rejected adjustment | Pricing panel in UI-09 artifact |
| UI-11 | Simulator / Run fixture | Invalid input, trace, unreachable branch, price gap, conflict | Clearly simulated; no session mutation |
| UI-12 | Publication / Publish | Pending review, blockers, scheduled, published, retired | Diff and translation/pricing/permission checks |
| UI-13 | Sign in / Continue | Invalid/expired token, recovery, MFA, claim conflict | Paste/autofill allowed; accessible recovery |
| UI-14 | Support / Request recovery | Redacted, consent scope, denied, escalated, resolved | No unauthorized answer changes |
| UI-15 | Export/privacy / Submit request | Queued, available, expired link, failed, legal hold | Verified identity; no secret URLs in analytics |

Artifacts are static design illustrations with annotations, title and description, not interactive pages. Desktop compositions use 1280 ×880 artboards; mobile review uses a 390 CSS px content frame in an annotation artboard. UI-09 uses illustrative Persian text for RTL inspection; Persian is owner-confirmed, but sample text is not a reviewed translation.

Hover supplements focus; selection uses shape plus color; disabled controls have reasons; loading preserves labels; empty states offer relevant actions; errors retain inputs; saved requires acknowledgement; stale names the changed revision; conflict offers compare/reload; interrupted mode states save risk; partial reports list missing sections. See [components](component-specifications.md) for behavior.
