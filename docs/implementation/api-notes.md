# Runtime API and proposed-contract adaptations

The proposed original docs/07-api/openapi.yaml is preserved. docs/implementation/openapi.yaml describes the implemented wire surface. API calls use the same cookie ownership and CSRF boundary as the web app; no external bearer/OAuth service was invented.

- Canonical field names are camelCase. POST /runs accepts flowId,locale and optional projectId. The private X-Recovery-Code response header carries the recovery credential. Only its hash is stored on the run; the24-hour idempotency replay cache holds an encrypted copy.
- PUT /runs/{id}/answers accepts an answers array. It validates and saves all supplied answers atomically, emits one aggregate revision, and derives inactive answers server-side. If-Match is a quoted integer. Missing/stale headers yield428/412; changed-payload key reuse yields409.
- Legacy/form-oriented snake_case POST extensions remain for the server-rendered client. They use the same services, ownership, validation and idempotency. Canonical schema tests exercise the documented PUT/camelCase surface.
- Canonical reports/repricing/publication return202 operation objects. Report and publish work currently finishes transactionally during the request; a completed operation can be polled immediately. Exports use the configured queue. This is actual completion status, not a simulated remote integration.
- Report outputs follow the proposed OUT-* summary records; rich structured output is available in the private JSON export and browser report. Estimates expose exact minor-unit Money objects. No private internal cost is serialized.
- The proposed schema made currency, priceBookId and validUntil mandatory non-null strings even when requirements require unpriced/unknown results. Runtime types explicitly allow null for those fields; no currency/book/date is invented. A missing authoritative total is omitted. Currency exponents0–6 are supported with exact strings.
- Offering/flow authors can create independent flow keys, so runtime Flow.key is a bounded stable slug instead of an enum restricted to the initial three flows.
- JSON and printable HTML export are available. PDF requests produce a clear422 readiness error pending renderer/font review. The structured report remains available.
- Staff draft metadata/publication routes require authentication, enabled grants and MFA. Publishing validates the exact review hash/evidence ID and current revision. Future effective publication is rejected until a reviewed scheduling policy exists.
- Confirmed reuse checks live source ownership/revision, compatible semantic key, typed equality and configured confirmation age. Reused inputs never bypass the destination schema or branch validation.
- Errors use application/problem+json with status and correlation ID; throttled responses retain Retry-After. API rate limits are a local safe baseline and need production capacity validation.

These are explicit implementation decisions against proposed documents, not claims that an external customer approved an API revision.
