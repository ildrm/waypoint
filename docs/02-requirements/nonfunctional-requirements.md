# Nonfunctional requirements

Owner: R25. Reviewers: R13, R23, R26, R27. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: NFR-01–16. Open decisions: [register](../00-governance/assumptions-and-open-questions.md). Practice: [performance-operations](../00-governance/skills/performance-operations.md).

Targets are proposals, not measurements. Customer-project workload/SLO answers are distinct from platform targets.

| ID | Quality | Target/condition | Verification |
|---|---|---|---|
| NFR-01 | Accessibility | WCAG 2.2 AA baseline for complete public/admin tasks; keyboard/focus/errors/contrast/reflow | AC-11 |
| NFR-02 | Public web experience | Proposed p75 LCP≤2.5 s, INP≤200 ms, CLS≤0.1 over 28-day locale/device cohorts | Field telemetry later; lab diagnostic only |
| NFR-03 | Save latency | Proposed p95≤500 ms, p99≤1.5 s server processing at 100 saves/s peak | Load validation plan; no achieved metric |
| NFR-04 | Report jobs | Proposed95%≤30 s,99%≤120 s structured generation; export measured separately | Queue saturation/failure scenarios |
| NFR-05 | Availability | Proposed99.5% eligible core requests successful per calendar month; planned downtime counts | SLO instrument definitions in operations |
| NFR-06 | Recovery | Proposed RPO≤1 h, RTO≤4 h for primary region outage | Restore drill and timestamp evidence later |
| NFR-07 | Reproducibility | Same active canonical facts/context/versions produce same structured output and exact money | AC-03,04 |
| NFR-08 | Security | Server-side object/role checks every request/job/download; staff MFA; restricted rules/templates | AC-07; ASVS-based verification plan |
| NFR-09 | Consistency | Atomic answer save+invalidation, revision conflicts rejected, idempotent result generation | AC-02,15 |
| NFR-10 | Localization | Exactly en, fa, fr, de, ru, hi, zh, ja, es, pt, ar, tr, hy, it; source/region/script choices tracked | AC-06 |
| NFR-11 | Maintainability | Business config/rates/translations managed without source edits and with release gates | AC-08 |
| NFR-12 | Privacy | No raw business text, credentials or resume tokens in analytics/logs; retention jurisdiction review | DEC-02,04 and AC-07 |
| NFR-13 | Capacity hypothesis | 100 peak save requests/s,20 report jobs/min,10k active runs/day,200 concurrent question users | Not inferred from actual demand; validate/revise |
| NFR-14 | Observability | Correlation IDs plus release/run/job IDs with restricted/redacted traces | Incident drill later |
| NFR-15 | Portability | Versioned configuration/data export; checksum and mapping failures explicit | AC-10 |
| NFR-16 | Motion/budget | No essential animation; initial compressed public JS≤200KiB, CSS≤80KiB; fonts per-script budget measured | Reduced motion and low-end-device trace later |

Operations documents must use these same targets. Owner ratification and representative workload are prerequisites to production sizing.
