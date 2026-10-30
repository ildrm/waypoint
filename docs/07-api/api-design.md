# API design contract

Owner: R22. Reviewers: R18, R23, R27. Status: proposed specification. Source: prompt; [OpenAPI 3.1.2](https://spec.openapis.org/oas/v3.1.2.html), accessed 2026-10-29. Related: AC-01, AC-02, AC-07, AC-15. Open: production domain/auth provider.

The [OpenAPI document](openapi.yaml) describes the first-party public API; it is JSON serialization, valid YAML. Version 3.1.2 is deliberately selected for tooling compatibility, not claimed the newest edition. `/api/v1` versions externally incompatible contracts. Additive fields remain compatible; never repurpose enum meanings or stable IDs. Dates use UTC RFC 3339; decimals/money serialize as strings, not binary JSON floats.

Browser first-party access uses secure HttpOnly SameSite cookies, CSRF verification on mutations, strict origin checks and HTTPS. An anonymous run uses an unguessable server session principal with identical project-scoped authorization; a run ID is not a bearer secret. Optional external API tokens need separate owner-approved scope/expiry/revocation design and are not implicitly granted by this contract. Staff routes require MFA, least privilege and step-up approval for publication/export; public routes cannot call staff commands.

All resource reads/writes verify live principal/project ownership. Return 404 for inaccessible object IDs to avoid enumeration; 403 is reserved for a known authorized object with forbidden action. Answer edits require strong `If-Match`; 428 absent, 412 stale. Long report/export work returns 202 plus operation URL. `Idempotency-Key` required on creation/calculation/reprice/export/publication. Rate limits are scoped by principal and IP, with 429/Retry-After; anonymous limits cannot be bypassed by changing a run ID.

Errors use `application/problem+json`: stable code, status, title, correlationId, field errors and safe recovery; no traces/secrets/customer values. Cursor pagination uses stable `(createdAt,id)` ordering, opaque scoped cursor and limit 1–100. Cursors cannot grant access. Conditional GET uses ETags only where private responses are not shared cached. API returns unknown/conflict/partial states explicitly; HTTP 200 does not certify an estimate is complete.

Operational contracts include request byte/count limits, cancellation and correlation IDs. [Integration contracts](integration-contracts.md) define timeouts/reconciliation and [events](events-and-webhooks.md) define delivery semantics. All endpoints remain design specifications; no server or client has been generated.


The contract explicitly pins the OAS JSON Schema dialect to 2024-11-10. Verification uses the official3.1 schema-base revision2026-08-03 with all referenced OAS schemas resolved from preserved local copies. Passing that informational schema is not proof of every normative or semantic OpenAPI requirement. Current evidence: [OpenAPI schema verification](../17-audit/openapi-schema-verification.json).
