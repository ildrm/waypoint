# Logical data model

Owner: R21. Reviewers: R18, R23. Status: proposed. Source: prompt. Related: AC-02, AC-03, AC-07, AC-15. Open: retention and organization ownership.

The model describes tables and constraints; it is not a migration. Opaque UUIDs are stable keys; user-entered labels are never foreign keys. Timestamps are UTC instants, with an IANA timezone stored separately for user/calendar context. Money stores decimal quantities/rates plus currency and minor-unit exponent; no binary float arithmetic.

| Entity | Primary/foreign keys | Cardinality and invariant |
|---|---|---|
| users | id | User owns zero/many projects |
| projects | id, owner_user_id | One current owner; transfers audited and revoke old grants |
| flows | id, stable_key | One/many releases; stable key unique |
| flow_releases | id, flow_id, manifest_hash | Unique flow/revision; released payload immutable |
| content_revisions | id, flow_id | Questions/options/stages use stable semantic IDs scoped to flow |
| rule_revisions | id, flow_id | Every referenced fact/question/parameter resolves in manifest |
| parameter_revisions | id, parameter_id | Schema and value hashed; explicit permitted flow scope |
| translation_packs | id, locale, source_hash | Immutable approved key/value set tied to content meaning |
| runs | id, project_id, flow_release_id, revision | Many per project; one pinned release each |
| answer_revisions | id, run_id, question_id, run_revision | Unique run/question/revision; current pointer separate |
| derived_facts | id, run_id, run_revision, semantic_key | Unique run/revision/key with source references |
| price_books | id, currency, market, effective interval | Approved periods do not overlap within matching scope |
| price_items | id, book_id, item_key, unit, role | Unique book/item/unit/role; exact numeric rate |
| estimates | id, run_id, input_revision, book_id, supersedes_id | Snapshot and digest immutable; successors form acyclic chain |
| estimate_lines | id, estimate_id, source_key | Unique estimate/source/item/unit prevents double charge |
| reports/exports | id, estimate_id or report_id | Each export belongs to exactly one report and locale |
| outbox/inbox | event_id | Outbox transactional; inbox unique provider/event ID |
| audit_entries | id, actor_id, resource_id | Append-only metadata; sensitive values redacted |

Use foreign-key restrict for published configuration and referential snapshots. Personal payload deletion is an explicit lifecycle command rather than cascading destruction of all audit history. Do not infer platform tenancy from customer facts. If organizations are required, add organization membership and enforce scoped keys/policies across every relevant entity before build. See [ERD](../15-diagrams/domain-erd.mmd).
