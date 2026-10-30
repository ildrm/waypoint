# Assumptions and owner questions

Owner: R01. Reviewers: R15, R24, R28, R29. Status: proposed specification, 2026-10-29; no human approval.
Sources: [authorized brief](sources/documentation-super-mega-prompt.md), [inventory](source-inventory.md). Related IDs: DEC-01–10, RISK-01–08. Open decisions: [decision log](assumptions-and-open-questions.md). Practice: [product-requirements](skills/product-requirements.md).

Questions were bundled for the owner during execution. Pending answers permit alternative design, not invented approvals.

| Decision | Current proposal / alternatives | Impact if unresolved | Accountable role / next evidence |
|---|---|---|---|
| DEC-01 language variants + source | Owner confirmed en/fa/fr/de/ru/hi/zh/ja/es/pt/ar/tr/hy/it; English engineering docs; source/variants pending | Cannot approve language coverage, fonts, reviewer staffing or release | R01 with R16: Confirm source locale and Chinese script/Portuguese regional variants |
| DEC-02 markets/currency/tax | No production base currency, region or tax rules; USD examples hypothetical | Production price outputs must remain incomplete | R29/R24: approved price book and jurisdiction review |
| DEC-03 operating model | Single operator with user/project ownership; organizational SaaS alternative | Tenant model changes data, auth, prices and timeline | R01: business operating model |
| DEC-04 anonymous retention | Proposed7-day anonymous expiry, 90-day dormant authenticated notice; retention policy pending | Data deletion/support policy cannot be published as legal conclusion | R24: purpose/retention and legal basis |
| DEC-05 launch scope | Startup+software+service matching; educational placement excluded pending domain | Program matching needs actual catalog and eligibility | R01: catalog sponsor |
| DEC-06 support/SLO geography | Business-hours support and one region proposed; explicit SLO design in operations | Capacity, incident coverage, hosting costs provisional | R26: support hours and regions |
| DEC-07 commercial authority | Price editors and publishers separate; caps to approve | Publication blocked without authority mapping | R29: rates, effective dates, approvals |
| DEC-08 identity and accounts | Email identity, MFA for staff; SSO optional later | Authentication procurement/integration effort uncertain | R23: identity risk review |
| DEC-09 visual direction | Calm editorial assessment interface, static artifacts proposed | Brand/font/translation design approval pending | R09: owner review and user research |
| DEC-10 deadline/team | No committed platform launch date or personnel | Delivery model is scenario, not commitment | R28: named availability and dependencies |

| Risk | Consequence | Mitigation / owner |
|---|---|---|
| RISK-01 missing blueprint | Earlier commitments may be missed | Reconcile if provided; R02 |
| RISK-02 unsupported estimation coefficients | Misleading precision | Low/base/high expert assumptions, catalog snapshots, later calibration; R28 |
| RISK-03 untranslated critical copy | Wrong meaning or unsafe publication | Block per-locale publication and release until required 14 covered; R16 |
| RISK-04 scope/date conflict | Impossible promise | Phasing/manual review and dependency schedule; R28 |
| RISK-05 sensitive narrative | Privacy exposure | Minimize, redact logs, purpose-bound retention; R24 |
| RISK-06 contradictory rules | Inconsistent or repeated charges | Restricted rules, explicit conflict policy, dry run and unique charge keys; R05 |
| RISK-07 unverified runtime | Source compatibility differs from install | Package resolution and product verification later; R18 |
| RISK-08 unreviewed jurisdiction | Incorrect compliance promises | Specialist review trigger; no automatic compliance certification; R24 |

Example-only calendar: 40 available hours/person/week, five working days, no holidays. Each worked schedule discloses individual allocations. These assumptions never become production defaults silently.

