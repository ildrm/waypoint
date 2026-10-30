# ADR-001: Modular PHP platform

Owner: R17. Reviewers: R18, R26. Status: proposed, not owner accepted. Source: PHP requirement. Related: AC-03. Open: ownership/hosting.

Context: transactional answers, deterministic traces, editable pricing and historical reports need consistent revision boundaries. No measured workload establishes a service split.

Proposal: PHP 8.5, Laravel 13, Filament 5 admin, public SSR, relational persistence, background rendering. [Compatibility findings](../platform-architecture.md) are source verification, not dependency resolution.

Alternatives: plain PHP increases bespoke admin/security work; another PHP framework remains possible if team/package validation rejects Laravel. Microservices add distributed consistency and operations cost without proven benefit. Filament remains a presentation adapter.

Consequences: simpler transactions/staffing, with module discipline required. Revisit for measured scaling, separate ownership or legal isolation. Acceptance: public/admin commands produce identical facts and prices; build-time package lock and security review later pass.
