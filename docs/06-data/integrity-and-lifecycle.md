# Integrity and data lifecycle

Owner: R21. Reviewers: R23, R24, R26. Status: proposed. Source: prompt. Related: AC-02, AC-03, AC-07, AC-15. Open: jurisdiction-specific retention.

Answer edits lock/check the run revision and atomically append assertions, update current pointers, mark dependencies inactive, regenerate facts and append an outbox event. Database uniqueness complements application validation. Publication transaction checks effective periods and validation hashes; workers cannot partially publish a manifest. Estimate transactions snapshot the book and input hash together so a concurrent price change cannot produce mixed versions.

| Data class | Proposed handling | Disposal verification |
|---|---|---|
| Anonymous run | 30 days inactivity, pending owner policy; clear expiry notice | Purge payload/export pointers; record nonpersonal aggregate count |
| Authenticated answers/reports | Owner-selected retention schedule, not invented legal default | Confirm ownership/legal holds, redact/purge approved classes |
| Export file | 7 days proposed; report may persist longer | Object missing after deadline; download denied immediately |
| Security/audit events | Restricted retention to be approved | Remove identifiers according to schedule; preserve allowed integrity metadata |
| Backups | 35-day proposed rotating window | Deletion ledger reapplied during restore; backups inaccessible for ordinary use |

No production retention is approved until markets/purposes/legal obligations are known. Legal holds must be scoped, justified and reviewable. Erasure stops further processing/downloads promptly; asynchronous deletion exposes a tracking status and exception reasons. Deletion of a user does not silently transfer project ownership. Recovery cannot resurrect deleted records: restore runbook reapplies deletion ledger before opening traffic.

Indexes cover project/run ownership lookup, current answer pointer, release key, estimate input digest and due jobs; volume/growth modeling determines further indexes. Backup and restore verify referential integrity, snapshot hashes and encryption keys. Every lifecycle action records actor, purpose, classes, count, timestamps and outcome without storing deleted payload in audit.
