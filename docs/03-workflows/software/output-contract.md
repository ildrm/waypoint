# Software result contract

Owner: R04. Reviewer: R02, R27. Status: proposed specification; no human approval.
Sources: supplied documentation prompt; startup source where stated. Related IDs: WF-SOFTWARE; WF-RESULT; AC-03, AC-04, AC-10, AC-15.
Open decisions: production locales, markets/currency, organization model; see [owner decisions](../../00-governance/assumptions-and-open-questions.md) from flow documents.
Practice profiles: [workflow and assessment](../../00-governance/skill-registry.md) (relative to flow folders; shared documents use ../00-governance/skill-registry.md).

The result envelope contains run_id, flow_release_id, input_revision, report_revision, locale_pack_version, calculated_at, status and evidence_limits. It includes the exact presented question/answer record, inactive/skipped reasons, derived facts with provenance, and deterministic structured outputs. API field names may be camelCase; documentation-bank names are snake_case and map one-to-one without changing semantics.

| Field | Type / content | Missing-state behavior |
|---|---|---|
| OUT-SCOPE | Included/optional/excluded feature rows per web/Android/iOS/admin/API; phase, quantity and acceptance | Unspecified items named as gaps |
| OUT-ARCH | Candidate comparisons, proposed topology/stack, constraints satisfied/rejected, validation actions | Preliminary/manual-review; never unsupported definitive solution |
| OUT-EFFORT | WBS role hours low/base/high, reusable/incremental tags and coefficient source | Unknown lines retained with null hours if not defensibly bounded |
| OUT-PRICE | Currency, price_book_version, rate/line/adjustment snapshots, exact amounts, tax status, validity | null total and incomplete state for missing rates; no zero substitute |
| OUT-SCHEDULE | DAG dependencies, earliest feasible and planning range, calendar, critical path and lead times | Provisional range/manual-review where external dependency unknown |
| OUT-TEAM | Integer unique/peak people, roles, seniority, hours/week, FTE by phase and person capacity | Unfilled capability remains a gap |
| recurring_costs | Infrastructure/provider/maintenance/content/store items, quantity, rate/source/version | TBD and exclusions visible; never buried in build total |
| OUT-ACTIONS | Evidence/action/owner/decision trigger for each material gap | At least one next action if result incomplete |

Each material output line includes source question IDs/answer revisions, rule IDs and assumption IDs. Scope edits produce a new input revision and new estimate; price-only refresh produces a visibly new estimate with unchanged input revision and new commercial snapshot. Old output is read-only historical and access checked live. JSON/CSV/PDF exports carry the same identifiers, units, currency and evidence limits. Estimate is distinct from quotation/contract/invoice/payment authorization.

