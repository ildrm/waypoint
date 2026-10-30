# Versioning and snapshots

Owner: R21. Reviewers: R05, R29, R23. Status: proposed. Source: prompt. Related: AC-03, AC-06, AC-08. Open: jurisdiction/retention.

| Object | Snapshot moment | Immutable evidence |
|---|---|---|
| Run | Start | Release/content/rule/parameter/library/locale-pack hashes |
| Answer | Accepted edit | Value/state/source/actor/time/applicability/run revision |
| Estimate | Calculation transaction | Answer hash, book revision/effective time, currency/exponent, quantities/rates/intermediates/rounding/adjustments, WBS/calendar, validity |
| Report | Generation | Facts/trace, estimate, template and locale pack |
| Export | Render completion | Report/locale, renderer/font manifest, checksum/status/retention |

Content, rules and translations version independently; a release pins compatible revisions. Price books have separate effective periods. An old run uses the applicable approved book at first calculation; the estimate snapshots it. Updates never alter old estimates. Repricing creates a successor with visible deltas, `supersedesId` and new validity.

Corrections create successors. Retirement prevents new starts. A critical defect may suspend old-run continuation with explanation and reviewed migration, never invisible substitution. Security grants, suspension, download expiry and deletion restrictions remain live. Locale switching selects only a compatible approved pack; absent/stale critical copy requires explicit choice of another locale. Historical exports retain pack identity; corrected exports are new versions. Erasure may redact personal payloads while retaining permitted deidentified integrity records; history retention cannot override legal obligations.
