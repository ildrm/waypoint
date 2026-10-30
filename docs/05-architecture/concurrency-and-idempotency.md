# Concurrency and idempotency

Owner: R22. Reviewers: R21, R27. Status: proposed. Source: prompt. Related: AC-02, AC-08, AC-15. Open: retry/retention tuning.

Mutable aggregates carry integer revisions rendered as strong ETags. Answer/draft edits require `If-Match`: absent 428, stale 412 with reload guidance. Do not merge questionnaire changes by timestamp. Users review a diff and resubmit against a fresh revision. Publication includes the validation-evidence hash, preventing approval of a changed draft.

| Operation | Atomic boundary | Duplicate behavior |
|---|---|---|
| Answer batch | Run revision, answers, facts, outbox | Same key/body returns original; changed body 409 |
| Report/estimate | Unique run revision/request identity/type/locale | Retry same key returns same operation; explicit reprice creates successor |
| Publish | Pointer/effective-period constraint | One active version per scope/instant |
| Export worker | Claim/output key/completion compare-and-set | Repeated delivery produces one visible artifact |
| Incoming webhook | Provider/event ID inbox constraint | Acknowledge processed duplicate without effects |

Keys scope to principal/project/endpoint/request digest. Proposed key retention 24 hours; domain uniqueness survives key expiry. A key grants no access; live authorization precedes cached responses. Timed-out clients retry the same key or poll operation ID. Transactions do not report success before commit.

Outbox delivery is at least once. Consumers deduplicate event IDs and guard stale revisions. Proposed transient retries: exponential backoff+jitter, five attempts within 30 minutes, then dead-letter. Validation/permission failures do not retry. No transaction waits on email/PDF rendering.
