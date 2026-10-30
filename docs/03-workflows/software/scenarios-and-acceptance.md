# Software path acceptance scenarios

Owner: R27. Reviewer: R02, R27. Status: proposed specification; no human approval.
Sources: supplied documentation prompt; startup source where stated. Related IDs: WF-SOFTWARE; AC-02, AC-04, AC-05, AC-06, AC-07, AC-09, AC-14, AC-15.
Open decisions: production locales, markets/currency, organization model; see [owner decisions](../../00-governance/assumptions-and-open-questions.md) from flow documents.
Practice profiles: [workflow and assessment](../../00-governance/skill-registry.md) (relative to flow folders; shared documents use ../00-governance/skill-registry.md).

| Scenario | Inputs / path | Expected result | Acceptance |
|---|---|---|---|
| Brochure site | web + content; known modest demand | No commerce/mobile question exposure; CMS alternatives with editorial scope | AC-14 |
| Mobile-only commerce | mobile + commerce service family; both OS | Commerce questions remain active; per-OS UI plus backend once | AC-02 |
| Both surfaces offline | web+mobile, queued edits, camera | Shared matrix and conflict policy required; compare PWA against exact capability evidence | AC-02 |
| Organization portal | isolated orgs + portal | Customer tenancy scope/auth tests; platform operator decision not overwritten | AC-07 |
| Burst payments | commerce, orders/payment peak and burst duration | Stock/payment idempotency, worker/reconciliation design and later load plan | AC-14, AC-15 |
| Conflicting budget/date | fixed constraints with mandatory scope exceeding capacity | Clarification or phased alternatives; no fabricated guarantee | AC-09, AC-05 |
| Back edit | Remove mobile after paid device scope | Inactive details excluded from facts/WBS/price; history retained | AC-02 |
| Unknown integration | Mandatory API without documentation | Feasibility discovery/manual review and explicit lead-time gap | AC-09 |
| Missing role rate | WBS role has no effective rate | Incomplete commercial state, null total, missing rate ID | AC-04 |
| Locale switch | Same canonical answers and release | Same scope/numbers and logical branch; new localized labels only | AC-06 |
| Duplicate generate | Same input revision and idempotency key | One result/estimate; retries return same job/result | AC-15 |

These are documentation acceptance specifications. No application, load, linguistic or accessibility test was executed. Complete fictitious customer cases live under [examples](../../16-examples/).

