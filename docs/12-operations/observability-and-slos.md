# Observability and SLOs

Owner: R26. Reviewers: R25, R30. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: NFR-02–06,13,14. Open decisions: [register](../00-governance/assumptions-and-open-questions.md). Practice: [performance-operations](../00-governance/skills/performance-operations.md).

Platform design hypothesis:10k active runs/day,200 concurrent questionnaire users, peak100 saves/s,20 result jobs/min. These independent peaks are not inferred from total accounts and require workload validation. Proposed targets align02-requirements: p95 save≤500 ms, p99≤1.5 s server processing;95% structured jobs≤30 s,99%≤120 s;99.5% eligible core requests successful per calendar month. Eligible means valid authorized core read/save/result requests; malformed/auth-denied requests excluded; planned downtime counts. Track job success and user-visible latency separately from request acceptance.

SLIs: good core responses/eligible requests; processing latency histogram; report queue wait/runtime/success; export failure rate; DB/storage saturation; rule conflict/missing-rate counts; translation coverage and stale-copy blockers. Correlation ID links request/run revision/release/book/job without raw answers. Web p75 LCP≤2.5 s, INP≤200 ms, CLS≤0.1 over 28-day representative locale/device cohorts is proposed, not measured.

Alerts: sustained5xx or save failures, oldest job>120 s, backup failure/restore overdue, authorization anomaly. Alert severity/paging hours depend DEC-06. Error budget=0.5% eligible monthly failures; consume with change planning, not hide exclusions. Runbook: on alert verify scope/signals, assign incident owner, mitigate queue/load/dependency, verify recovery with authorized synthetic canary and record cause. No metrics or load tests exist yet.
