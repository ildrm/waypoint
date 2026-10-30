# Software branches and states

Owner: R05. Reviewer: R02, R27. Status: proposed specification; no human approval.
Sources: supplied documentation prompt; startup source where stated. Related IDs: WF-SOFTWARE; RULE-SW-CONFLICT; AC-02, AC-09, AC-14.
Open decisions: production locales, markets/currency, organization model; see [owner decisions](../../00-governance/assumptions-and-open-questions.md) from flow documents.
Practice profiles: [workflow and assessment](../../00-governance/skill-registry.md) (relative to flow folders; shared documents use ../00-governance/skill-registry.md).

## Branch evaluation

1. Ask stage A and SURFACES. Unknown surfaces triggers a clarification recommendation and permits a partial report; it does not open every feature branch.
2. Ask WEB-KINDS when public/internal web is selected. Select all applicable domain families; none selected requires user confirmation of an API/mobile-only scope or unresolved web purpose.
3. Ask mobile questions only for mobile. Shared asks only for both mobile and public/internal web. DEVICE-DETAILS asks when a concrete capability other than none is selected. none is exclusive.
4. Ask content/commerce/booking/marketplace/portal/media detail records only for selected web kinds. Mobile-only domains can still need the same features: at the surface review the user declares a shared service feature family using WEB-KINDS in domain-only mode. See correction below.
5. AI-TASK asks only for AI-NEED=yes. Existing/migration questions ask only for extension/replacement/configuration as specified; integration discovery always asks, allowing an explicit empty systems list.
6. Common workload/quality/design/team questions follow. CONFIRM shows a per-surface feature matrix with included/optional/excluded items and unresolved assumptions. No branch can skip the common constraints necessary for an estimate.

Domain-only correction: WEB-KINDS applicability is defined to include mobile-only and API-only scope as a service-domain classification; its wording adapts to “What must the service do?” in that context. This avoids hiding commerce discovery from a mobile shop. Stable option IDs and rule semantics stay the same; wording key has a surface-neutral variant.

## States and termination

draft -> answering -> review -> clarifying -> review -> calculating -> complete|partial|manual_review.
Any editable state -> saved; saved -> answering|expired. complete -> revised-input on material answer edit, never mutate existing report.
A server failure leaves generation pending/failed with retry; same idempotency key never creates a second result. User may finish with explicit unknowns. Clarification revisits only named fields, records one resolution outcome and can end unresolved; it cannot require an infinite loop to complete.

## Contradiction table

| Check | Detection in same context | Resolution |
|---|---|---|
| Demand | DAU > MAU; peak RPS < typical RPS; writes+reads !=100 for same classified set | Show units/time/population; correct or distinguish contexts; do not clamp numbers |
| Data | Max file < typical file; horizon count < launch without deletion explanation | Ask for valid values or documented retention/decline assumption |
| Scope | none combined with concrete device/auth choice; anonymous with account-only action | Require explicit compatible subset or revised identity requirement |
| Delivery | Hard budget/date cannot accommodate mandatory WBS at available capacity | Show phase/alternative options or manual review; never guarantee all three |
| Ownership | Isolated organizations but no tenant authorization/deletion plan | Require discovery before tenancy architecture accepted |
| Integration | Must-have provider has no API/sandbox/owner | Record lead time/feasibility spike, unsupported result if no safe option |
| Quality | RPO=0 but backup-only design; strict offline edits without conflict policy | Preserve requirement, explain unvalidated design and specialist action |

All comparisons use context ID, horizon, unit and provenance. Unresolved conflicts suppress dependent authoritative totals or recommendations. Back-edit invalidation traverses dependencies before pricing and uses only current active known facts.

