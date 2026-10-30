# Platform WBS and backlog

Owner: R28. Reviewers: R01, R18, R27. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: PW01–12, FR-01–29. Open decisions: [register](../00-governance/assumptions-and-open-questions.md). Practice: [estimation-commercial](../00-governance/skills/estimation-commercial.md).

This estimates building the platform, not customer projects. Platform users pay nothing. No real rates/vendor quotations or named team supplied. All base effort is an expert-assumption scenario.

| Package | Deliverable | Role | Base hours | Dependency/assumption | Requirement |
|---|---|---|---:|---|---|
| PW01 | Discovery/product/BA | BA | 240 | Owner decisions/source reconciliation | FR-01–29 |
| PW02 | UX/UI/content architecture | UX | 320 | Initial discovery gate/questions/visual direction | FR-26 |
| PW03 | Flow/rule/session/result engine | BE | 800 | Domain/API contracts | FR-02–17 |
| PW04 | Admin/catalog/settings/publication | BE | 600 | Schema/permissions/PW03 interfaces | FR-18–24 |
| PW05 | Public web/scenarios/report interface | FE | 800 | UX gate/PW03 interfaces | FR-01–17,26,29 |
| PW06 | Localization/export engineering | I18N | 240 | Locale/script/source and renderer feasibility | FR-21,26 |
| PW07 | Quality/security/performance verification | QA | 480 | Integrated features and approved copy | NFR-01–16 |
| PW08 | Operations/release/handover | OPS | 160 | Staging/recovery readiness; quality exit | FR-24,27 |
| PW09 | Source copy | LANG | 40 | Assumed12000 source words frozen | FR-26 |
| PW10 | 13 target translations | LANG | 520 | 12000 words÷300 words/hour×13, expert assumption | FR-26 |
| PW11 | 13 target reviews | LANG | 260 | 12000 words÷600 words/hour×13, expert assumption | FR-26 |
| PW12 | 14-language visual/report QA | LANG | 112 | 8 h/language incl source, expert assumption | AC-06,11 |

BA240+UX320+BE1400+FE800+I18N240+QA480+OPS160=3640 h engineering/product. Language/content932 h, total 4572 h. Word count, translation/review speeds and locale-QA hours are hypothetical, not measured productivity. Source en proposed;13 targets assumes source included among14 named languages.

Backlog order: ownership/version model -> schema/rule/navigation/invalidation -> publication/simulator -> public answer/review -> WBS/catalog/price -> schedule/team/scenarios ->14-language/report -> abuse/recovery/quality -> handover. Attach requirement/question/rule/output/AC IDs to every item. No application or runnable tests created.
