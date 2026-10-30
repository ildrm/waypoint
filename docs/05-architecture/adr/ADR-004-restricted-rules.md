# ADR-004: Restricted deterministic rules

Owner: R05. Reviewers: R18, R23, R27. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: RULE-CORE-05, AC-08. Open decisions: [register](../../00-governance/assumptions-and-open-questions.md). Practice: [architecture-data-api](../../00-governance/skills/architecture-data-api.md).

Context: staff need configurable adaptive flows/pricing without arbitrary executable content. Proposal: typed expression trees, approved operators and bounded DAG with deterministic traces; separate navigation, classification, scope/pricing and narrative categories. Unknown uses three-valued logic. Material conflicts block publication.

Alternatives: unrestricted PHP/SQL/JavaScript rejected for injection/audit/termination risk; opaque proprietary decisions weaken explainability. Consequences: less flexibility but bounded validation/versioning. Revisit when approved domain cannot be modeled safely; extend DSL via reviewed implementation, not admin code. Status proposed, not owner accepted.
