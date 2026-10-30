# Software derivation and recommendations

Owner: R17. Reviewer: R02, R27. Status: proposed specification; no human approval.
Sources: supplied documentation prompt; startup source where stated. Related IDs: WF-SOFTWARE; OUT-SCOPE, OUT-ARCH, OUT-EFFORT, OUT-PRICE, OUT-SCHEDULE, OUT-TEAM.
Open decisions: production locales, markets/currency, organization model; see [owner decisions](../../00-governance/assumptions-and-open-questions.md) from flow documents.
Practice profiles: [workflow and assessment](../../00-governance/skill-registry.md) (relative to flow folders; shared documents use ../00-governance/skill-registry.md).

The [decision table](decision-tables.csv) is a declarative design specification. Category rules normalize and propagate facts; architecture rules compare alternatives. No single traffic band selects a stack. Every recommendation records question IDs, answer revisions, disclosed assumptions, rule/release ID, satisfied constraints, rejected candidates and missing validations.

## Ordered method

First validate scope/provenance, then evaluate hard legal/contract/device/geography and budget/date constraints. Unknown constraints remain unknown. Build atomic feature IDs, e.g. ITEM-EDITORIAL-ROLES, ITEM-COMMERCE-CHECKOUT, ITEM-ANDROID-APP, with quantity/acceptance and surface. Foundation, account system, shared backend and one integration ID are counted once. Platform-specific UI, device support and release QA remain incremental.

Compare standard CMS/commerce or hosted product configuration before custom delivery. Compare SSR/progressive enhancement and SPA by interaction/offline needs rather than fashion. For mobile use the distribution/device/offline matrix, current capability verification and maintenance team skills. Independent services require an explicit independent deployment/scaling/team boundary; user count alone is insufficient.

Produce low/base/high role effort as explicit expert assumptions until calibration exists. Feed atomic WBS to the administered rate book; missing active rates return an incomplete commercial state. Build the dependency DAG, allocate capacity, calculate role hours/FTE, and disclose review/store/integration lead times. See [estimation contracts](../../08-estimation/calculation-contract.md).

## Input-to-output source contract

| Output | Primary sources | Required explanation |
|---|---|---|
| OUT-SCOPE | OBJECTIVE, SURFACES, WEB-KINDS, domain details, CONFIRM | Feature matrix; mandatory/optional/exclusions; one foundation |
| OUT-ARCH | B/C/D/E/F/G, OWNERSHIP, TEAM | Constraint matrix; alternatives; fit/reject reasons and validation |
| OUT-EFFORT | Feature quantities, H/I, integrations/migration, quality | WBS line, role, quantity, coefficient provenance, range |
| OUT-PRICE | OUT-EFFORT, budget context, selected commercial catalog | Rate/book version, adjustment order, tax/recurring separation |
| OUT-SCHEDULE | DEADLINE, dependencies, review and available capacity | Critical path, role availability, calendar and planning range |
| OUT-TEAM | TEAM, AVAILABILITY-TEAM and role WBS | Distinct contributors, peak staff, FTE by phase, review specialists |
| OUT-ACTIONS | Unknown/conflicting facts and unvalidated alternatives | Owner role, evidence needed, impact and next decision |

A report is preliminary until critical integrations, workload, regulatory applicability and ownership decisions are validated. It must say which unknown could change scope/price/date and present phases or specialist review instead of false precision.

