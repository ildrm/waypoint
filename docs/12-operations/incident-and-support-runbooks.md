# Incident and support runbooks

Owner: R32. Reviewers: R26, R23, R24. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: WF-SUPPORT, AC-07,10. Open decisions: [register](../00-governance/assumptions-and-open-questions.md). Practice: [performance-operations](../00-governance/skills/performance-operations.md).

| Runbook/trigger | Prerequisites/authorized role | Intended steps | Verify | Failure/rollback |
|---|---|---|---|---|
| Save errors/latency | Correlation IDs; incident operator | Establish scope; check dependencies/recent releases; reduce load; disable unsafe new config if needed | Authorized canary save and revision correctness; SLO trend | Revert compatible release; do not discard acknowledged answers |
| Report/export failed | Owner verified; support job capability | Inspect sanitized code; retry same operation; offer structured report | No duplicate result; download owner check | Bound retries, escalate renderer; delete partial files |
| Suspected data breach | Security incident authority | Restrict affected access; preserve redacted evidence; rotate/revoke secrets; privacy/legal escalation | Unauthorized path blocked and legitimate recovery works | No speculative legal notices; owner determines jurisdiction duties |
| Wrong rate/recommendation | Commercial/domain approver | Stop unsafe new outputs; inspect affected versions; publish corrected successor; offer explicit revision | Old history intact, new fixture correct | Roll back new release for new runs; record impact, not silent reprice |
| Account recovery | Verified requester; support scope | Use identity procedure, rotate resume/session secrets, restore scoped access | Foreign data absent; old token revoked | Escalate identity uncertainty; never ask password |
| Deletion/export request | Privacy operator and policy | Verify identity/purpose; collect scoped data; redact; fulfill jobs; record exceptions | Primary/object/cache/jobs handled; backup tombstones | Pending status with reason; rollback accidental delete only under policy and audit |

Every runbook records trigger, incident/request ID, UTC timeline, owner, actions, verification and follow-up. No real messages/contacts or operational actions were performed for this documentation.
