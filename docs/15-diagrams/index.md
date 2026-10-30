# Diagram source index

Owner: R17. Reviewers: R05, R21, R22, R26, R31. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: FR-01–29, AC-01–16. Open decisions: [register](../00-governance/assumptions-and-open-questions.md). Practice: [architecture-data-api](../00-governance/skills/architecture-data-api.md).

These sources describe proposed responsibilities/interactions. C4 views use Mermaid flowcharts with explicit scope, not a claim of a certified notation/tool. PlantUML use-case/activity sources use genuine actor/usecase/start/decision/action constructs. Mermaid supports ERD, sequence, state, class and Gantt forms; no implementation code generated.

| Source | Scope/caption | Related requirements |
|---|---|---|
| [c4-context.mmd](c4-context.mmd) | c4 context | FR-02–27; AC-01–15 |
| [c4-containers.mmd](c4-containers.mmd) | c4 containers | FR-02–27; AC-01–15 |
| [rule-estimate-components.mmd](rule-estimate-components.mmd) | rule estimate components | FR-02–27; AC-01–15 |
| [domain-erd.mmd](domain-erd.mmd) | domain erd | FR-02–27; AC-01–15 |
| [use-cases.puml](use-cases.puml) | use cases | FR-02–27; AC-01–15 |
| [activity-answer.puml](activity-answer.puml) | activity answer | FR-02–27; AC-01–15 |
| [activity-estimate.puml](activity-estimate.puml) | activity estimate | FR-02–27; AC-01–15 |
| [sequence-start-resume.mmd](sequence-start-resume.mmd) | sequence start resume | FR-02–27; AC-01–15 |
| [sequence-answer-edit.mmd](sequence-answer-edit.mmd) | sequence answer edit | FR-02–27; AC-01–15 |
| [sequence-result-generation.mmd](sequence-result-generation.mmd) | sequence result generation | FR-02–27; AC-01–15 |
| [sequence-repricing.mmd](sequence-repricing.mmd) | sequence repricing | FR-02–27; AC-01–15 |
| [sequence-publication.mmd](sequence-publication.mmd) | sequence publication | FR-02–27; AC-01–15 |
| [state-release.mmd](state-release.mmd) | state release | FR-02–27; AC-01–15 |
| [state-run.mmd](state-run.mmd) | state run | FR-02–27; AC-01–15 |
| [state-translation.mmd](state-translation.mmd) | state translation | FR-02–27; AC-01–15 |
| [state-estimate.mmd](state-estimate.mmd) | state estimate | FR-02–27; AC-01–15 |
| [domain-classes.mmd](domain-classes.mmd) | domain classes | FR-02–27; AC-01–15 |
| [deployment-trust-boundaries.mmd](deployment-trust-boundaries.mmd) | deployment trust boundaries | FR-02–27; AC-01–15 |
| [business-process-conceptual.mmd](business-process-conceptual.mmd) | business process conceptual | FR-02–27; AC-01–15 |
| [platform-dependency-schedule.mmd](platform-dependency-schedule.mmd) | platform dependency schedule | FR-02–27; AC-01–15 |

Business process is explicitly conceptual swimlanes, not BPMN-conformant XML. DMN-style decision tables live in 03-workflows/*/decision-tables.csv; not executable DMN conformance claims. All state edges are design transitions; live security revocation constrains every state. Gantt anchor dates are fictional visualization only;14-delivery relative week/person allocation tables authoritative.

Validation: delimiter/type/reference inspection only unless actual renderer results recorded in 17-audit. PlantUML/Java/Mermaid CLI unavailable; no package installed. Static C4 context preview provides readable relationships; full diagram renders remain unverified. Do not claim parser or semantic certification from text inspection.


Follow-up: [C4 context PNG](c4-context-preview.png) was rendered and inspected from the [static SVG](c4-context-preview.svg); it now includes directional arrows and the email boundary. This does not validate or render the Mermaid/PlantUML sources. [Verification details](../17-audit/follow-up-verification.md).
