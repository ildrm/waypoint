# Flow and rule engine

Owner: R05. Reviewers: R18, R23, R27. Status: proposed. Source: prompt. Related: AC-02, AC-09, AC-14. Open: empirical calibration unavailable.

At answer commit: authorize ownership and revision; validate state/type/applicability; append answer; invalidate transitive facts and dependent answer applicability; evaluate the acyclic dependency graph; return next active question, conflicts and trace. Historic hidden values stay auditable but cannot affect current scope or charges. Reactivating an old branch requires confirmation of stale answers.

Rules are JSON expression trees with approved operators `eq`, `ne`, `in`, `contains`, `gt`, `gte`, `lt`, `lte`, `all`, `any`, `not`, `is_known`, `is_unknown`, `is_applicable`. Operands are registered facts, parameters, options or typed literals. No PHP/SQL/JavaScript, network access, admin regex, reflection or loops. Proposed limits: depth 12, 200 nodes per expression; publication rejects excess.

| Category | Effect | Conflict policy |
|---|---|---|
| Navigation | Activate question or clarification | Acyclic; explicit combined conditions |
| Classification | Domain labels/match eligibility | UNIQUE hit; contradictory multiple hits block publication |
| Scope/architecture | Feature facts and candidate reasons | Collect; incompatible constraints create review state |
| Pricing | Eligible item quantities/adjustment IDs | Estimator deduplicates item/source/unit; no floating totals |
| Narrative | Approved explanatory passages | Cannot override facts, blockers or price |

Three-valued predicates preserve unresolved inputs: `not unknown` stays unresolved. Missing prerequisites trigger relevant clarification or partial output, not false. Blockers outrank guidance; no universal startup probability exists. Trace records every consumed answer, rule and parameter revision. Priority controls deterministic evaluation order but cannot excuse an ambiguous classification.

Publication checks IDs, cycles, unreachable questions, termination, conflicting decision rows, duplicate charges, unused parameters and outputs without evidence/assumption provenance. Structural errors block publication; unprovable semantic equivalence requires fixtures and reviewer evidence. See [simulation](../09-admin/simulator-and-validation.md).
