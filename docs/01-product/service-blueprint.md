# Service blueprint

Owner: R07. Reviewers: R05, R26, R32. Status: proposed specification, 2026-10-29; no human approval.
Sources: [brief](../00-governance/sources/documentation-super-mega-prompt.md), [source inventory](../00-governance/source-inventory.md). Assumptions/open decisions: [DEC-01–10](../00-governance/assumptions-and-open-questions.md). Related IDs: WF-SESSION, WF-RESULT, WF-AUTHOR. Practice: [experience-research](../00-governance/skills/experience-research.md).

This table is a conceptual service blueprint, not BPMN-conformant notation.

| Customer/staff step | Visible interface | Backstage responsibility | Failure and recovery | Evidence |
|---|---|---|---|---|
| Start | Flow/locale choice and privacy notice | Session service selects published manifest and scoped owner | Unsupported locale: explain, do not silently substitute critical content | release/session IDs |
| Answer/save | Question, help, saved/pending status | Validation, revision check, dependency invalidation, encrypted persistence |412 stale ETag: reload diff; offline: disclose unsaved and retry explicitly | answer revision and trace |
| Review | Scope matrix, gaps and conflict explanations | Rule engine normalizes only active answers | Material unknown: partial/manual review route | fact sources |
| Generate | Job status and eventual report | Snapshot exact inputs/price book; idempotent job; authorized export | Retry same key; export failure preserves report | calculation/result/job IDs |
| Compare | Changed inputs and totals | Scenario fork re-evaluates scope/architecture/schedule | Missing rate shows unavailable price, not zero | baseline/scenario links |
| Publish | Simulation and review evidence | Staff authorization, atomic manifest and translation gates |Failed check leaves old release active | audit/release diff |
| Support/delete | Request status and safe recovery | Purpose-scoped staff access, retention review and job revocation | Legal hold or dependency needs explicit policy; no silent deletion failure | request and audit IDs |

Support communications are intended product workflows only; no real third parties were contacted during documentation.

