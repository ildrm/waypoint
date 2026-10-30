# Domain model

Owner: R17. Reviewers: R05, R21. Status: proposed. Sources: prompt, startup baseline. Related: AC-02, AC-03, AC-04. Open: organization model.

| Concept | Responsibility |
|---|---|
| Project | Ownership boundary for one venture or evaluated project |
| Flow | Stable definition identity with independent content/rules/output contract |
| BusinessRelease | Immutable compatible content/rule/parameter/library/locale manifest |
| Question/Option | Stable semantic IDs; labels and order never drive logic |
| Run | Traversal of one release for one project; optimistic revision |
| AnswerRevision | Append-only assertion, state, provenance, applicability, actor |
| Fact | Typed value with source answer IDs and derivation rule |
| Rule | Restricted condition plus a typed category-specific effect |
| CatalogItem | Feature/service/level identity and scope unit |
| PriceBook | Independently approved effective-dated commercial version |
| Estimate | Immutable calculation, WBS/calendar, assumptions and expiry |
| Report | Structured explanation referencing input/estimate/template revisions |
| Scenario | Explicit fork with changed answers; never invisible overwrite |
| TranslationUnit | Source key/hash, locale, meaning context and review evidence |

User proficiency, target proficiency, complexity and package tier are different types. Unknown is a user assertion; missing is no current input; inactive is excluded by branching; not applicable needs a valid branch reason. Reused facts carry source project/run/release, timestamp, consent and freshness. Cross-project reuse requires explicit selection and scope validation. Narrative cannot create a fact or charge. See [classes](../15-diagrams/domain-classes.mmd) and [ERD](../15-diagrams/domain-erd.mmd).
