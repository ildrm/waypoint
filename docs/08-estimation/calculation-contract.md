# Exact calculation contract

Owner: R29. Reviewers: R21, R27. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: OUT-PRICE, RULE-CORE-08,09, AC-04. Open decisions: [register](../00-governance/assumptions-and-open-questions.md). Practice: [estimation-commercial](../00-governance/skills/estimation-commercial.md).

Inputs: active feature quantities, scoped WBS/role hours, pinned business/input revision, approved price-book version, currency/context and explicit assumptions. A calculation has unique charge keys (feature/platform/deliverable/unit/catalog version); shared backend/foundation counted once. Negative quantities, missing rates, overlapping scope or mixed currencies are errors, not zero.

Calculation order: (1) labor lines=exact hours×sell rate; fixed/unit lines according to catalog inclusion rules; round each line half-up to currency exponent; sum eligible one-time base B. (2) Contingency C=round(B_eligible×configured ratio). (3) Eligible adjustment subtotal A=B+C only where explicitly allowed. (4) Discount D=round(A_eligible×approved ratio), cap to eligible subtotal. (5) One-time net N=B+C−D. (6) Jurisdiction-approved tax lines on configured taxable bases after discount; if applicability unknown show tax excluded/to confirm, not tax-free. (7) Recurring lines calculated separately per period, never folded into one-time total.

Illustrative fixture: B=17600.00, C=1760.00, A=19360.00, D=968.00, N=18392.00 USD, excluding unconfigured taxes. Internal base cost10280.00 is separate; sell rates already include commercial pricing policy, so do not add margin again. Contingency is a disclosed risk allowance, not hidden margin. Observed gross margin before actual risk/taxes=(N−internal cost)/N; no profitability guarantee.

Exact rational/decimal inputs only; no binary float for authoritative money. Hours may be decimal with defined precision; line rounding and sum reconciliation retained. Example0.005 USD rounds0.01 at a2-decimal half-up line boundary;100 such separately billed lines sum1.00, while one aggregate line0.500 rounds0.50. Catalog explicitly defines billable aggregation unit to prevent arbitrary split effects.

Output snapshot: calculation_id, input_revision, business_manifest, price_book_version, rate/item references, quantity/unit, exact/rounded amount, eligible bases, adjustment sequence, one_time_net, taxes state, recurring totals, valid_until, assumptions, unresolved and trace. Repricing creates a new revision with explicit old/new book; expired estimates remain historical. No payment object or purchase action.
