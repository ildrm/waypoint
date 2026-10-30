# Abuse and failure cases

Owner: R23. Reviewers: R27, R32. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: AC-02,04,07,09,10,15,16. Open decisions: [register](../00-governance/assumptions-and-open-questions.md). Practice: [security-privacy](../00-governance/skills/security-privacy.md).

| Attempt/failure | Expected behavior |
|---|---|
| Foreign run/export ID | Deny no data; safe response; sanitized audit |
| Revoked owner while job queued | Recheck before calculation/export delivery; deny/revoke artifact access |
| User narrative says override price | Stored as text only; no rule/code execution |
| Admin arbitrary expression/template include | Reject allowlist/schema |
| Huge free report flood | Rate/quota/job bounds and retry-after; never request payment |
| Duplicate generation submission | Same idempotency key+payload same operation; different payload409 |
| Stale answer revision |412 with authorized diff; no last-write silent loss |
| Hidden paid feature after edit | Inactive facts/charges excluded from new result |
| Missing rate/currency | Incomplete price, null not zero |
| Export renderer failure | Preserve structured result; bounded retry; no public partial file |
| Unsupported integration | Discovery/manual review, not invented support |
| Translation source changed | Stale critical copy blocks publication |
