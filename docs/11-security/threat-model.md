# Proposed-design threat model

Owner: R23. Reviewers: R17, R24, R27. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: NFR-08, FR-27, AC-07. Open decisions: [register](../00-governance/assumptions-and-open-questions.md). Practice: [security-privacy](../00-governance/skills/security-privacy.md).

Scope is proposed architecture, not a repository code audit. Assets: confidential answers, ownership/resume secrets, business releases, commercial rates/internal cost, report exports, audit history and staff authority. Actors: anonymous user, authenticated owner, staff, malicious visitor, compromised staff/integration/provider. Trust crossings: browser->public API; staff browser->admin; application->DB/queue/object storage; worker->renderer; import->restricted configuration; optional future provider.

| Threat | Attack path | Required control / residual validation |
|---|---|---|
| Object access spoofing | guessed run/report/export ID | Server-side ownership each request/job/download; opaque IDs insufficient alone |
| Resume-token theft | logs/referrer/browser storage | Secret hash at rest, short scope, redacted logs, secure transport/cookies and safe claim |
| Staff privilege misuse | author publishes hostile rules/prices | Restricted DSL/templates, least privilege, MFA, separation of duties, diff audit |
| Injection/XSS/SSRF | user text, report template, import URL | Typed validation, context escaping, no arbitrary evaluation, no renderer network or arbitrary file access |
| Catalog tampering | direct rate overwrite or repricing history | Immutable approved books, snapshot lines, approval thresholds |
| Resource exhaustion | free anonymous reports/imports | Purposeful quotas, request sizes, rate limits, job bounds, backpressure; no payment gate |
| Cross-project leakage | reused facts/shared cache | Scope provenance, per-owner checks, private cache keys and encrypted exports |
| Supply/dependency compromise | framework/renderer package | Supported versions, locked dependencies, review/advisory process during implementation |
| Export/data leakage | public object URL or stale authorization | Private storage, short-lived authorized URL/revocation-aware proxy, retention/deletion |
| Optional AI injection | narrative asks model to override money | Deferred; untrusted text cannot alter structured facts/rules/prices |

Residual risks: endpoint implementation absent, token lifecycle/auth integration/renderer sandbox unverified, hosting/legal scope pending. R23 owns later ASVS-based verification and independent product security review. Threat diagrams in 15-diagrams are conceptual data-flow/trust views, not proof of controls.
