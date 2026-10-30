# Software discovery workflow

Owner: R04. Reviewer: R02, R27. Status: proposed specification; no human approval.
Sources: supplied documentation prompt; startup source where stated. Related IDs: WF-SOFTWARE; AC-02, AC-04, AC-05, AC-09, AC-14.
Open decisions: production locales, markets/currency, organization model; see [owner decisions](../../00-governance/assumptions-and-open-questions.md) from flow documents.
Practice profiles: [workflow and assessment](../../00-governance/skill-registry.md) (relative to flow folders; shared documents use ../00-governance/skill-registry.md).

WF-SOFTWARE is a proposed user-facing questionnaire that converts a scoped business need into an explainable preliminary solution, WBS, price, schedule and team. The platform itself must use PHP; evaluated customer projects have no blanket PHP constraint.

The [question bank](question-bank.yaml) contains 75 questions. Records are labeled numeric/detail refinements of one logical question; they do not imply a single unlabelled form. No field is prefilled with a substantive answer. A brochure site follows common requirements plus content questions and bypasses commerce, booking, marketplace and mobile internals. Uncertainty remains visible.

| Stage | Scope | Questions |
|---|---|---|
| A | Business objective and constraints | Q-SW-OBJECTIVE, Q-SW-USERS, Q-SW-OUTCOME, Q-SW-CHANGE, Q-SW-EXISTING, Q-SW-PRIORITY, Q-SW-DEADLINE, Q-SW-BUDGET, Q-SW-PROCUREMENT, Q-SW-OWNERSHIP |
| B | Delivery surfaces | Q-SW-SURFACES, Q-SW-WEB-KINDS, Q-SW-MOBILE-PLATFORMS, Q-SW-DEVICES, Q-SW-DISTRIBUTION, Q-SW-OFFLINE, Q-SW-DEVICE-FEATURES, Q-SW-DEVICE-DETAILS, Q-SW-SHARED |
| C | Users and identity | Q-SW-IDENTITY, Q-SW-ROLES, Q-SW-ORGANIZATIONS, Q-SW-AUTH, Q-SW-ACCESS |
| D | Domain features | Q-SW-CONTENT, Q-SW-SEARCH, Q-SW-LOCALES, Q-SW-SEO, Q-SW-LEADS, Q-SW-COMMERCE-GOODS, Q-SW-COMMERCE-CATALOG, Q-SW-COMMERCE-PRICING, Q-SW-COMMERCE-CHECKOUT, Q-SW-COMMERCE-FULFILLMENT, Q-SW-BOOKING-RESOURCE, Q-SW-BOOKING-POLICY, Q-SW-MARKET-PARTIES, Q-SW-MARKET-MONEY, Q-SW-PORTAL-FEATURES, Q-SW-PORTAL-DETAILS, Q-SW-MEDIA-FEATURES, Q-SW-MEDIA-DETAILS, Q-SW-AI-NEED, Q-SW-AI-TASK |
| E | Integrations/migration | Q-SW-INTEGRATIONS, Q-SW-MIGRATION |
| F | Quantitative workload | Q-SW-REGISTERED, Q-SW-ACTIVE, Q-SW-CONCURRENCY, Q-SW-REQUESTS, Q-SW-TRANSACTIONS, Q-SW-DATA, Q-SW-BURSTS, Q-SW-REGIONS |
| G | Quality/security/data constraints | Q-SW-AVAILABILITY, Q-SW-RECOVERY, Q-SW-CONSISTENCY, Q-SW-DEGRADATION, Q-SW-OBSERVABILITY, Q-SW-SUPPORT, Q-SW-ACCESSIBILITY, Q-SW-SENSITIVITY, Q-SW-GEOGRAPHY, Q-SW-RETENTION, Q-SW-REGULATION |
| H | Visual/content scope | Q-SW-BRAND, Q-SW-ASSETS, Q-SW-MOTION, Q-SW-CONTENT-READINESS |
| I | Team/delivery constraints | Q-SW-TEAM, Q-SW-AVAILABILITY-TEAM, Q-SW-DECISIONS, Q-SW-DEPENDENCIES, Q-SW-MAINTENANCE |
| J | Results and confirmation | Q-SW-CONFIRM |

The [coverage matrix](coverage-matrix.csv) maps every mandatory prompt category to actual inputs and consequences. Each question must support scope, a constraint, clarification or report explanation. Domain-specific records include quantities/state rules, not merely a yes/no feature checklist. Read [shared semantics](../shared-question-model.md), [decisions](decision-tables.csv), [output](output-contract.md) and [acceptance](scenarios-and-acceptance.md).

Entry: a user selects Software needs or confirms scoped fact reuse. Exit: complete preliminary report, partial report with named gaps, manual-review result or saved run. All exits preserve a reviewable answer record. No result authorizes a purchase or establishes legal/technical certainty.

