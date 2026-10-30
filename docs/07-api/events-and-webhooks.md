# Events and webhooks

Owner: R22. Reviewers: R21, R23, R26. Status: proposed. Source: prompt. Related: AC-03, AC-08, AC-15. Open: whether external subscribers are needed.

Internal events are an outbox contract; externally delivered webhooks are conditional and disabled initially. Envelope: eventId UUID, type, schemaVersion, occurredAt UTC, aggregateId, aggregateRevision, correlationId, minimal data. Do not include raw answers or access secrets.

| Event | Producer/commit point | Payload/consumer |
|---|---|---|
| run.answers_changed.v1 | Answer transaction | Run/revision, changed question IDs; analytics/read model invalidation |
| report.requested.v1 | Report request | Snapshot/operation IDs; renderer |
| report.ready.v1 | Successful render pointer update | Report/export IDs; notification/read model |
| estimate.revised.v1 | New estimate snapshot | Old/new IDs; revision notification |
| flow.published.v1 | Publication transaction | Release ID/hash; cache invalidation |
| translation.stale.v1 | Source revision | Unit IDs/locale IDs; localization queue |
| privacy.deletion_completed.v1 | Approved purge | Request ID and counts; no deleted payload |

Delivery is at least once with no global order. Consumers deduplicate eventId and reject stale aggregate revisions. On transient faults retry with exponential backoff+jitter, five attempts/30-minute proposed budget then dead-letter; manual replay retains the original event ID. A replay requires scoped operator permission and audit.

If external webhooks are enabled, HTTPS destinations must be preapproved and protected against SSRF/private networks and redirect rebinding. Sign timestamp plus exact body with HMAC, support key rotation, maximum replay skew five minutes proposed. Receiver acknowledges 2xx after durable inbox commit; duplicates acknowledge safely; 4xx schema/auth errors do not trigger blind retries. Provide delivery logs with redacted payloads and explicit replays. No external network sends were performed for this task.
