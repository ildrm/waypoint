# Gap and correction log

Owner: R31. Reviewers: R02, R34. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: G01–16. Open decisions: [register](../00-governance/assumptions-and-open-questions.md). Practice: [quality-documentation](../00-governance/skills/quality-documentation.md).

| ID | Original gap | User impact | Revised design/rationale | Affected artifacts | Owner | State |
|---|---|---|---|---|---|---|
| G01 | Missing locale identities | Cannot plan14-language production | Applied owner-confirmed14 names/base tags; kept source/region/script decisions open | 01-product;10-localization; requirements; API | R15 | resolved |
| G02 | Platform assumed potential billing | Free-use requirement could be violated | ADR-005/FR-29/AC-16 prohibit platform payment/paywall; project prices informational | 05-architecture;08-estimation;09-admin;13-quality | R01 | resolved |
| G03 | Matching inherited startup references | Wrong cross-flow clarifications | Replaced RULE-ST-CONFLICT and startup units with RULE-MA-CONFLICT and scoped matching fields | 03-workflows/match/question-bank.yaml | R05 | resolved |
| G04 | Dependencies encoded as text predicates | Cannot validate question graph | Extracted actual question IDs from applicability; validated acyclic graph | 03-workflows/*/question-bank.yaml | R05 | resolved |
| G05 | Architecture/prose status code mismatch | Incompatible API client behavior | Canonical stale If-Match412, missing428; idempotency payload conflict409 | 02-requirements;03-workflows;05-architecture;07-api;13-quality | R22 | resolved |
| G06 | Customer demand contradictory shared defaults | Misleading capacity rationale | Corrected DAU/MAU/horizon and commerce record quantities for each case | 16-examples/C01–06 | R25 | resolved |
| G07 | WBS/role totals and lean subtotal | Unreconciled cost/schedule output | Corrected leanB13600/net14212; feature packages sum role hours; checked exact cents and allocation totals | 08-estimation;16-examples;14-delivery | R29 | resolved |
| G08 | QA/OPS overlapping combined capacity risk | Single person could exceed availability | Separate people; C01 daily QA/release gate explicitly fits W8 | 08-estimation;16-examples/C01 | R28 | resolved |
| G09 | Run/result lifecycle naming conflict | Ambiguous implementation state | Canonical run vocabulary; partial is result completeness; wire inactive/unanswered server derived | 03-workflows/shared-question-model.md;06-data;07-api;15-diagrams | R21 | resolved |
| G10 | Broken diagram links/ADR registry mismatch | Readers cannot trace architecture | Repaired links and made ADR001–005 register agree with actual files | 05-architecture;06-data;00-governance/decision-register.md | R31 | resolved |
| G11 | Skill accessibility large-text shorthand | Wrong contrast threshold | Specified18pt/24CSSpx or14pt bold/~18.67CSSpx; actual chosen color ratios calculated | 04-ux-ui/accessibility.md | R13 | resolved |
| G12 | Source platform blueprint absent | Earlier design may contain additional commitments | Recorded missing source; use authorized brief; reconcile if supplied | 00-governance/source-inventory.md | R02 | open-input |
| G13 | Initial SVG rendering unavailable | Could not claim rendered visual QA | Follow-up used existing Pillow/FreeType/RAQM;7previews generated and inspected; RTL/glyph defects corrected. Browser/interactive verification remains pending | 04-ux-ui/mockups;13-quality;17-audit | R09 | resolved-static-review |
| G14 | Diagram render/parser tools absent | Full syntax/visual verification incomplete | 20sources with notation distinctions; structural inspection only, no install | 15-diagrams;17-audit | R17 | open-verification |
| G15 | Subagents stopped before cross-review | False independent assurance risk | Retained drafts; root simulated10review passes; no completed independent/human approval claimed | 00-governance/reviews.md | R34 | resolved-reporting |
| G16 | Production rates/policy/reviewer evidence absent | Price/legal/language approval impossible | Explicit incomplete/manual-review states and readiness gates; hypothetical examples only | 00-governance;08-estimation;10-localization;13-quality | R01 | open-owner-decision |

Open findings have impact, owner and next evidence in assumptions/register and release readiness. Proposed documentation is complete as a design; open production decisions/verification are not silently called approved.


Follow-up corrections,2026-10-29:

| ID | Original gap | User impact | Revised design/rationale | Affected artifacts | Owner | State |
|---|---|---|---|---|---|---|
| G17 | RTL end anchoring near right canvas edge | Persian headings clipped | Corrected8elements to RTL start; all text within canvas in metrics and inspected preview | 04-ux-ui/mockups/06-translation-pricing.svg | R15 | resolved |
| G18 | Font-dependent checkbox symbols | Selected state rendered as missing glyph | Three explicit vector controls; selected check visible | 04-ux-ui/mockups/02-question-clarification.svg | R10 | resolved |
| G19 | OPT ID/value and editorial/dependency order ambiguous | Implementations could store incompatible answers or ask dependent questions too early | Canonical values match schema/rules; OPT IDs identify option records; scheduler respects topology/conditional insertion | 03-workflows/shared-question-model.md | R05 | resolved |
| G20 | Partial records could imply all fields known | Unknown fields could incorrectly drive conclusions | Per-field unknown semantics and no-deadline exceptions explicit; record completeness gate clarified | 03-workflows/shared-question-model.md; software/question-bank.yaml | R06 | resolved |
