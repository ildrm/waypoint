# C02 E-commerce sales burst

Owner: R04. Reviewers: R17, R28, R29, R27. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: AC-04,05,09,14,16. Open decisions: [register](../00-governance/assumptions-and-open-questions.md). Practice: [estimation-commercial](../00-governance/skills/estimation-commercial.md).

Fictitious complete response set: [C02 JSON](C02-commerce-burst.json). The JSON includes all 75 question dispositions, full applicable typed values, branch trace, exact WBS, outputs and per-person/week allocations. Forecasts, rates and workload are invented; no market price or production performance claim.

Scope: 2000 products,8000 variants,2 warehouses, hosted payments, ERP inventory, refund/reconciliation.

Recommended preliminary architecture: Compare established commerce product with validated ERP/payment extensions against custom modular commerce; retain transactional inventory/reservation and queued idempotent webhooks.

Alternatives: Hosted commerce preferred only after API/ownership/rate-limit proof; custom application if extension limits violate reservation/reconciliation requirements.

Exact report expectation: 840 base hours; USD46600.00 base +4660.00 contingency −2563.00 discount =48697.00 net excluding unconfigured taxes. Base-scope price only; changes require revision. Plan 16 weeks;6 distinct contributors; peak2. Recurring 1500 USD/month hypothetical budget. Platform use/export cost0.

Sources: Q-SW-SURFACES/WEB-KINDS determine feature families; INTEGRATIONS and applicable domain questions determine external work; REQUESTS/TRANSACTIONS/DATA/RECOVERY constrain capacity; TEAM/AVAILABILITY-TEAM and assumptions constrain schedule. All WBS coefficients are expert assumptions recorded as such, not derived mechanically from traffic.

Acceptance: answers outside branch inactive; unknown API/jurisdiction items visible; shared backend counted once; exact line/adjustment sums; no person exceeds40 h/week; dependencies/lead time fit stated horizon; old results immutable; no platform payment action. 

Sensitivity: DEV+25% adds 120 h and USD7524.00 net under same adjustment policy; delivery effect depends on critical path and developer count. External review2-week delay can extend critical path without changing role hours. Unresolved legal/vendor/content requirements may materially change scope.
