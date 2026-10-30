# Platform architecture

Owner: R17. Reviewers: R18, R19, R21, R26. Status: proposed. Sources: prompt; official references below. Related: ADR-001, ADR-002, AC-03, AC-15. Open: hosting, patch selection, owner approval.

Use a PHP modular monolith with separate public/staff presentation surfaces, a relational source of truth, background workers and private export storage. Public questionnaire pages use server rendering with progressive save/clarification interactions; Filament serves admin resources. Domain modules own decisions; neither browser scripts nor Filament hooks alone calculate authoritative results. Platform native apps are outside the proposed launch; customer recommendations still compare native/cross-platform/PWA alternatives.

| Candidate | Published compatibility checked 2026-10-29 | Remaining gate |
|---|---|---|
| PHP 8.5 | Active support to 2027-12-31; security to 2029-12-31 | Hosting, extensions and renderer compatibility |
| Laravel 13 | PHP 8.3–8.5; security to 2028-03-17 | Exact dependency lock; upgrade plan |
| Filament 5 | PHP >=8.2; Laravel >=11.28; Tailwind >=4.1; support package permits Illuminate 13 | Complete package graph and chosen plugins |
| PostgreSQL | Relational transactions/constraints fit this proposed model | Select supported provider major at implementation |
| Queue/cache | Database queue is a starting option | Dedicated service only when measured contention warrants |

These are source-level compatibility findings, not successful installation or Composer resolution. [Laravel policy](https://laravel.com/framework/docs/releases), [PHP support](https://www.php.net/supported-versions.php), [Filament requirements](https://filamentphp.com/docs/5.x/introduction/installation), and [Filament constraints](https://raw.githubusercontent.com/filamentphp/filament/5.x/packages/support/composer.json) support the version statements. Exact patches remain a build-time gate.

Application nodes keep no sole copy of business state. Answer, derived-fact invalidation, run revision and outbox changes commit together. Workers read durable IDs and immutable snapshots. Cache is never authoritative for answers, prices or reports. File downloads require live ownership checks before short-lived grants. Extract services only for demonstrated independent scaling, ownership or legal boundaries. See [modules](module-responsibilities.md), [data](../06-data/data-model.md) and [deployment](../12-operations/environments-and-deployment-design.md).
