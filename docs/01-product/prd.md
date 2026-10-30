# Product requirements document

Owner: R01. Reviewers: R02, R17, R27. Status: proposed specification, 2026-10-29; no human approval.
Sources: [brief](../00-governance/sources/documentation-super-mega-prompt.md), [source inventory](../00-governance/source-inventory.md). Assumptions/open decisions: [DEC-01–10](../00-governance/assumptions-and-open-questions.md). Related IDs: NEED-01–08, FR-01–28. Practice: [product-requirements](../00-governance/skills/product-requirements.md).

A user selects a flow, supplies scoped answers, resolves material uncertainty, reviews the derived scope and receives a revisioned report. Software results distinguish recommendation from commitment; startup results prioritize blockers and evidence-building actions; matching results explain eligibility or no match.

Release1 includes three questionnaire flows and eight supporting workflows in the workflow catalog. Shared services handle sessions, provenance, deterministic rule evaluation, typed parameters, report generation, commercial snapshots and publication. Public UI and staff admin are separate permission surfaces.

| Product capability | Acceptance condition | Priority |
|---|---|---|
| Adaptive questionnaire | Relevant branches only; no dependent hidden charges after edit | Must |
| Software discovery | All mandatory A–J categories covered or disclosed unknown/inactive; quantified demand | Must |
| Startup guidance |15-stage source mapped, contradictions handled, no universal score | Must |
| Matching | Eligibility, exclusions, no-match and separate proficiency/tier concepts | Must |
| Explainable estimate | WBS/role hours/rates/adjustments/schedule/team sources visible | Must |
|14-language experience | Required locale list approved, critical copy complete, RTL and exports verified | Must |
| Safe management | Author/reviewer/publication gates; immutable release/results; live auth | Must |
| Scenario comparison | Explicit changed answers, shared foundations once, alternative outcomes | Must |
| Optional AI narrative | Approved inputs and deterministic fallback; no authoritative pricing | Deferred |
| Native platform app | Not required for assessment platform first release; customer-project mobile still elicited | Conditional |

Non-goals: executing customer projects, automatic procurement/payment, binding quotes, regulated advice, ranking startup success probability, unverified proficiency certification. The release gate requires unresolved production decisions to be assigned and resolved before launch, although a proposed design can be documented now.

