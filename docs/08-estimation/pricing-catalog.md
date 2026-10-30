# Estimate pricing catalog

Owner: R29. Reviewers: R28, R18. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: FR-13, ITEM-*, PARAM-*, AC-04, AC-16. Open decisions: [register](../00-governance/assumptions-and-open-questions.md). Practice: [estimation-commercial](../00-governance/skills/estimation-commercial.md).

The platform is free. This administered catalog calculates informational prices of customer projects; it does not bill users or authorize purchases. No production rates were supplied. Missing prices keep outputs incomplete/manual review.

| Entity | Required fields/constraints |
|---|---|
| Price book | stable ID, version, status, currency, market, effective_from/to UTC, approved_by, validity_days, rounding_policy |
| Role rate | role_id, unit=hour, sell_rate exact decimal, internal_cost_rate separately permissioned, currency, effective interval |
| Fixed item | ITEM ID, deliverables, quantity unit, fixed amount, included effort, exclusions; forbid also charging included role effort |
| Unit item | ITEM ID, unit, quantity bounds, unit amount, rounding, source factor |
| Adjustment | ID, type, percentage/fixed, eligible line IDs, order, exclusions, stackability, min/max, approval_limit |
| Flow override | flow_id, allowed item/rate reference, override value, reason, approver, effective book version |

Currency has explicit minor-unit exponent and decimal arithmetic; do not assume2 for all currencies. No silent FX. Region, currency, language and tax jurisdiction separate. Reject overlapping effective rates per scope/currency; select one fully approved version at calculation start. Drafts do not affect users.

Hypothetical PB-EXAMPLE-v1 USD sell rates/hour: BA60.00, UX50.00, DEV60.00, QA45.00, OPS45.00. Internal cost/hour respectively35,30,35,25,30. These are invented fixtures, not market rates. PARAM-CONTINGENCY=0.10, PARAM-DISCOUNT=0.05 and PARAM-ESTIMATE-VALIDITY=30 are example-only. Actual approval limits must be configured before production publication.


[Example feature catalog](example-feature-catalog.json) defines stable ITEM IDs, units and inclusion policies used by fictitious cases. Feature quantities trace to confirmed answer scope; prices come from the role-hour WBS, not an additional feature fee. Catalog sample is not approved production commercial data.
