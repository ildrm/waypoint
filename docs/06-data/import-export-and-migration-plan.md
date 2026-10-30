# Import, export and migration specification

Owner: R21. Reviewers: R22, R27, R24. Status: proposed. Source: prompt. Related: AC-08, AC-10. Open: source systems and legal portability scope.

Definition imports contain schema version, stable flow IDs, revision provenance, questions/options, rule trees, parameter references, translation units and checksums. Price imports are separate privileged batches with currency/exponent, unit, market, effective dates and approval provenance. Customer answer exports use authorized project IDs and canonical answer states; never export session tokens, secrets or internal-only staff notes.

Import is a staged transaction: upload to quarantined private storage; verify size/type and parse schema; resolve IDs/types and graph constraints; show mapping/conflict preview; simulate; approve; create draft only. Unknown IDs, incompatible units, formulas/executable content and duplicate keys fail with row/path/error code. All-or-nothing is default. An explicit partial import creates separate valid drafts and a machine-readable rejection report; it cannot partially publish. Limits proposed: 10 MiB definitions, 100,000 rows per price batch; tune from measured legitimate data.

| Mapping situation | Policy |
|---|---|
| Stable ID and identical digest | Deduplicate |
| Same ID, different meaning | Reject; new semantic ID required |
| New translation/source hash mismatch | Mark stale, never approve automatically |
| Existing price interval overlap | Reject until explicit replacement preview |
| CSV formula-like cell | Treat as text; neutralize on spreadsheet export |

Migration planning documents source owner, counts/files/bytes, quality/deduplication, mapping, downtime window, URL redirects, reconciliation totals and rollback checkpoint. Rehearse on deidentified data during implementation; no migration is executed here. Before cutover compare counts/hashes and sampled semantic records; after acceptance retain rollback data only under approved policy. Exports are queued, access rechecked at download, failure visible with retry preserving job identity. See [export runbook](../12-operations/incident-and-support-runbooks.md).
