# Software cross-field decision invariants

Owner: R05. Reviewers: R04, R27. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: RULE-SW-READ-WRITE, RULE-SW-ACTIVE-COUNTS, RULE-SW-PEAK, RULE-SW-BUDGET-RANGE, RULE-SW-NONE-EXCLUSIVE, RULE-SW-DATE-FIT, RULE-SW-RATE-COMPLETE, RULE-SW-OUTPUT-SOURCE. Open decisions: [register](../../00-governance/assumptions-and-open-questions.md). Practice: [workflow-questionnaire](../../00-governance/skills/workflow-questionnaire.md).

These supplement schema typing and categorical decision tables; evaluate only known active facts in the same context. Unknown inputs preserve unknown, not false proof.

| Rule | Category | Exact trigger | Effect | Acceptance |
|---|---|---|---|---|
| RULE-SW-READ-WRITE | validation | known REQUESTS.read_percent + write_percent !=100 | Reject or clarify endpoint workload partition; no normalization guess | AC-14 |
| RULE-SW-ACTIVE-COUNTS | clarification | same planning period/scope and DAU > MAU | Clarify periods/definitions or correct; registrations not automatically compared because anonymous users possible | AC-14 |
| RULE-SW-PEAK | validation | known peak_rps < typical_rps for same endpoint/window | Clarify/correct exact demand definitions | AC-14 |
| RULE-SW-BUDGET-RANGE | validation | initial_min > initial_max in same currency | Reject invalid range | AC-09 |
| RULE-SW-NONE-EXCLUSIVE | validation | none selected with another AUTH or DEVICE-FEATURES option | Reject mutually contradictory selection; compatible substantive options remain multiselect | AC-09 |
| RULE-SW-DATE-FIT | clarification | mandatory date earlier than resource-leveled earliest completion | Return phased/revised constraint alternatives or manual review, never promise all | AC-05,09 |
| RULE-SW-RATE-COMPLETE | pricing | required approved rate missing, wrong currency or expired | Price incomplete/null; preserve useful non-price results | AC-04 |
| RULE-SW-OUTPUT-SOURCE | validation | material output lacks answer/fact/assumption/config source | Block complete report or mark affected section unresolved | AC-09 |

Question-bank applicability is normative JSON predicate structure. Downstream derivations compare constraints rather than selecting stacks from a single label. CMS/commerce/native/realtime/tenancy alternatives require capability validation and explicit rejected reasons. Assessed-project payment needs do not add platform payment features.
