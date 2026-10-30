# System management guide

Owner: R26. Reviewers: R18, R32. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: WF-AUTHOR, WF-PRICE, WF-SETTINGS. Open decisions: [register](../00-governance/assumptions-and-open-questions.md). Practice: [performance-operations](../00-governance/skills/performance-operations.md).

Daily intended checks: save/report/export error trends, queue age, backup status, storage growth, failed imports, expiring estimates, current staff access alerts and critical translation staleness. Weekly review supported dependency advisories, resource saturation and backlog; monthly restore evidence/retention/catalog effective dates; quarterly role access and continuity drill. Frequency proposed until support model approved.

Change procedure: identify resource and current version -> use draft/staging -> validate dependency/locale/price impact -> reviewer/publisher approval -> atomic activate -> verify representative fixtures and live indicators -> record audit. No direct database edits for prices/questions/permissions. Export/import through documented schemas and dry-run mappings. If import fails keep published state, return per-row errors and checksum; retry after correcting draft. Maintenance must not mutate historical outputs. Internal costs/private answers restricted; export links temporary.
