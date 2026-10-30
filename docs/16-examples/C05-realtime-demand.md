# C05 High-demand realtime collaboration

Owner: R04. Reviewers: R17, R28, R29, R27. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: AC-04,05,09,14,16. Open decisions: [register](../00-governance/assumptions-and-open-questions.md). Practice: [estimation-commercial](../00-governance/skills/estimation-commercial.md).

Fictitious complete response set: [C05 JSON](C05-realtime-demand.json). The JSON includes all 75 question dispositions, full applicable typed values, branch trace, exact WBS, outputs and per-person/week allocations. Forecasts, rates and workload are invented; no market price or production performance claim.

Scope: 50000 persistent connections,10000messages/s peak, moderation, regional recovery and quantified burst.

Recommended preliminary architecture: Modular application control plane plus separately capacity-managed realtime channel/gateway or managed realtime service; independent scaling justified by connection workload, not total registrations.

Alternatives: Managed realtime lowers protocol operations if region/cost/retention fit; self-operated gateway requires capacity/recovery proof; Kubernetes not assumed.

Exact report expectation: 2520 base hours; USD140400.00 base +14040.00 contingency −7722.00 discount =146718.00 net excluding unconfigured taxes. Base-scope price only; changes require revision. Plan 23 weeks;11 distinct contributors; peak4. Recurring 12000 USD/month hypothetical budget. Platform use/export cost0.

Sources: Q-SW-SURFACES/WEB-KINDS determine feature families; INTEGRATIONS and applicable domain questions determine external work; REQUESTS/TRANSACTIONS/DATA/RECOVERY constrain capacity; TEAM/AVAILABILITY-TEAM and assumptions constrain schedule. All WBS coefficients are expert assumptions recorded as such, not derived mechanically from traffic.

Acceptance: answers outside branch inactive; unknown API/jurisdiction items visible; shared backend counted once; exact line/adjustment sums; no person exceeds40 h/week; dependencies/lead time fit stated horizon; old results immutable; no platform payment action. 

Sensitivity: DEV+25% adds 400 h and USD25080.00 net under same adjustment policy; delivery effect depends on critical path and developer count. External review2-week delay can extend critical path without changing role hours. Unresolved legal/vendor/content requirements may materially change scope.
