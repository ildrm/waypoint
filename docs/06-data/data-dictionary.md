# Data dictionary

Owner: R21. Reviewers: R05, R24. Status: proposed. Source: prompt. Related: AC-02, AC-04, AC-06. Open: locale/currency registry and retention.

| Field | Type/domain | Meaning/validation | Sensitivity |
|---|---|---|---|
| project.id/run.id | opaque UUID | No ownership authority encoded in ID | Internal identifier |
| run.revision | integer >=1 | Compare-and-set token; increments per accepted mutation | Internal |
| run.status | active/paused/reviewable/completed/suspended/expired/deleted | Completed does not imply every unknown resolved | Internal |
| answer.questionId | stable semantic ID | Must exist and be applicable in pinned release | Internal |
| answer.state | known/unknown/not_decided/not_applicable | Missing = absent; inactive = server applicability metadata | Potentially confidential |
| answer.value | typed scalar/list/record | Required only for known; units from question schema | Customer confidential |
| answer.provenance | source project/run/question/revision, confirmedAt | Reuse is explicit and freshness checked | Customer confidential |
| fact.sources | array of answer/rule/assumption IDs | Material output must have at least one source | Internal |
| locale | BCP 47 tag | Approved registry member, independent of currency/market | Preference |
| currency | ISO currency code + exponent snapshot | Configured approved book; never inferred from locale | Commercial |
| quantity/rate | canonical decimal strings | Unit compatible, finite, nonnegative unless approved credit | Commercial |
| amountMinor | signed integer string | Exact rounded total in snapshotted exponent | Commercial |
| estimate.status | complete/partial/manual_review/expired/superseded | Missing price cannot be complete | Commercial |
| estimate.validUntil | UTC instant | Expiry does not erase historical price | Commercial |
| trace | answer/rule/parameter IDs, effects | Redact sensitive values from operator logs | Customer confidential |
| translation.sourceHash | SHA-256 text/meaning revision | Source changes invalidate review | Internal |
| audit.reason | bounded plain text | Required for privileged access/change; avoid answer payloads | Restricted |

Numeric workload facts always include unit, scope, observed/forecast status and horizon. Example: peak concurrent active sessions = distinct active sessions in the busiest five-minute window, not simultaneous requests. Files include count, average/max bytes and retention. Unknown quantity has no numeric value; zero must be an explicit known assertion. Long text limits and rich-text sanitization are server controlled; free-form evidence is not interpreted as a rule. See [answer interface schema](../07-api/openapi.yaml).
