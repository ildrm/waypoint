# Security requirements

Owner: R23. Reviewers: R18, R27. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: SEC-01–12, AC-07. Open decisions: [register](../00-governance/assumptions-and-open-questions.md). Practice: [security-privacy](../00-governance/skills/security-privacy.md).

| ID | Condition/required control | Evidence later |
|---|---|---|
| SEC-01 | Every object access binds current principal, project and action | Foreign-ID and revoked-user scenarios |
| SEC-02 | Staff MFA, secure sessions, CSRF protection, rotation and recovery | Auth review and recovery drill |
| SEC-03 | Server-side schemas, allowed enums, limits and restricted operators | Malformed/oversize/type cases |
| SEC-04 | Report/template output context escaping, allowlisted variables | XSS/template injection cases |
| SEC-05 | Renderer no arbitrary network/file access; approved assets only | SSRF/file access sandbox cases |
| SEC-06 | TLS, in-transit secrets protection, encryption at rest, key access separation | Deployment/config/key review |
| SEC-07 | Rate limit anonymous/staff APIs and jobs, backpressure and budgets | Abuse workload/queue cases |
| SEC-08 | Live revocation for sessions/jobs/downloads and short-lived export links | Revocation race cases |
| SEC-09 | Redacted audit/logs with access/retention policy | Sensitive-field inspection |
| SEC-10 | Dependency/version/advisory review and patch ownership | Supported stack resolution later |
| SEC-11 | Safe import mapping/no executable payload; upload size/type restrictions | Malicious configuration/upload cases |
| SEC-12 | Backup restore/deletion/export honor ownership and policy | Recovery/privacy drills |

Use OWASP ASVS 5.0.0 as verification reference; choose exact applicable control IDs/level during implementation security planning, not a claim of full conformance. API2023 threats and conditional MASVS supplement evaluated mobile requirements. No penetration tests executed.
