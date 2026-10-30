# Simulator and semantic validation

Owner: R27. Reviewers: R05, R29. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: WF-AUTHOR, AC-02,04,08,09. Open decisions: [register](../00-governance/assumptions-and-open-questions.md). Practice: [quality-documentation](../00-governance/skills/quality-documentation.md).

Simulator accepts fictional scoped answer fixtures, run revision, locale and selected business/commercial versions. It shows active/inactive questions, fact provenance, matched/unmatched rule conditions, conflicts, scope lines, architecture alternatives, WBS and pricing/schedule/team output. Mark simulation prominently; never append to real user history.

Gate checks: orphan question/fact/parameter; undefined option/rule/output reference; nonterminal/unreachable branch; cycle; missing critical translation; competing exclusive outputs; duplicate charge key; unresolved price reference; missing output source; infeasible person allocation. Unknown paths and mutually compatible multiselect combinations required. Finite fixture coverage is not proof of all real input behavior; bounded schema/rule constraints require later engine verification. Store fixture version, expected result, actual trace and reviewer.
