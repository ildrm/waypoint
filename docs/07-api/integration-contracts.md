# Integration contracts

Owner: R22. Reviewers: R23, R26. Status: proposed. Source: prompt. Related: AC-09, AC-10, AC-15. Open: selected vendors and their actual contracts.

| Integration | Input/output | Failure and security contract |
|---|---|---|
| Email | Minimal recipient, template ID, verification/recovery grant | Queue after commit; expiring single-use token; no raw answer/report attachments by default |
| Object storage | Opaque object key, checksum, content type | Private bucket; least-privilege identity; bounded signed download; live access check first |
| PDF renderer | Approved HTML/template, structured data, local fonts/assets | Isolated worker; no network/remote URLs; CPU/memory/time limits; escaped content |
| Analytics | Event ID/type, flow/release, stage, locale, outcome code | No free text/answers; consent policy and deletion handling |
| Customer software dependency | API docs, sandbox, auth/limits, ownership, source-of-truth | Discovery evidence only; platform does not connect to customer ERP/payment accounts during assessment |

For each selected provider, record owner, region/subprocessors, authentication/rotation, version, sandbox, timeout, quotas, retry eligibility, idempotency capability, deprecation policy and reconciliation path. Proposed synchronous outbound timeout 3 seconds connect/10 seconds total; no critical answer save depends on provider availability. Confirm values against actual contracts later. Retry only safe transient failures; invalid credentials/schema errors escalate. Circuit breaking degrades email/export delivery without losing committed answers.

Estimate unsupported integrations as discovery/manual review, not zero-hour adapters. Customer answer evidence must name documentation and sandbox readiness; a generic provider name is insufficient. Secrets are references to a managed secret store, never flow parameters or translation strings. An external outage cannot cause duplicate report charges, purchases or implicit acceptance. No payment integration is required for platform launch unless separately authorized.
