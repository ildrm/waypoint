# System context

Owner: R17. Reviewers: R04, R23. Status: proposed, 2026-10-29. Sources: execution prompt; architecture practice profile. Related: WF-SOFTWARE, WF-STARTUP, WF-MATCH, AC-07. Open: operator/organization model, jurisdictions.

The platform collects facts about one venture or customer project, applies a published flow and produces explainable guidance and preliminary estimates. PHP constrains this platform; evaluated customer systems may use other justified technologies. It does not take payment, sign contracts or approve regulated activity.

| Actor/system | Information exchanged | Boundary |
|---|---|---|
| Visitor/project owner | Answers, consent, reports, export requests | Internet; session/project authorization |
| Author/reviewer/publisher | Drafts, simulations, publication decisions | Staff MFA and resource policies |
| Translator/language reviewer | Strings, semantic context, review decisions | Content scope; no customer answers required |
| Pricing editor/approver | Rates, effective periods, adjustments | Separate commercial permissions |
| Support/privacy operator | Recovery, exports, deletion requests | Ticket-scoped access and audit |
| Email provider, if selected | Verification/recovery notices only | Processor agreement; minimal identifiers |
| Private object storage | Exports and backups | Service identity; expiring grants |
| Optional AI provider | Disabled initially | Separate data/security review |

Single operator with project ownership is provisional. Customer organization isolation requires an owner decision before implementation; a customer saying their proposed SaaS needs tenants does not activate platform tenancy. Analytics receive event metadata, not raw answers. Rendering cannot fetch arbitrary remote URLs. See [context](../15-diagrams/c4-context.mmd) and [trust boundaries](../15-diagrams/deployment-trust-boundaries.mmd). Proposed controls are requirements, not deployed capabilities.
