# Attempt2 — additional screenshot verification

Reviewed2026-10-29. This appendix extends the evidence scope of evaluation-attempt-2.md; it does not establish native-language approval, production readiness or full accessibility conformance.

## New actual artifacts inspected
- output/playwright/matching-report-1440.png
- output/playwright/matching-report-375.png
- output/playwright/review-1440.png
- output/playwright/rtl-question-375.png
- output/playwright/rtl-question-reduced-1440.png

The parent captured these from the running application. This evaluator inspected the image artifacts directly.

## Findings

The matching report now visibly presents both explicitly synthetic alternatives, their supported deliverables, catalog versions, exclusions section, tie policy and next action. The partial state and unknown answers remain visible. The report no longer displays irrelevant software effort/price/delivery sections for matching. Desktop and phone captures retain readable hierarchy with no visible horizontal clipping or overlapping controls. This resolves the visual portion of the previous missing-matching-results issue.

The desktop review capture supports the earlier phone review result: actual question labels and unknown states are readable, edit controls remain adjacent and the result-generation panel has a clear hierarchy.

The RTL captures show correctly mirrored page geometry, choice controls, progress and navigation. The reduced-motion desktop capture also displays a strong keyboard-focus ring around the selected choice. The parent reported scrollWidth1440 at a1440 viewport and Tab focus on INPUT; those are parent-observed browser measurements, not measurements independently executed by this evaluator. A static screenshot alone cannot prove every animation respects reduced motion.

### P2 remaining in the RTL English-source preview: text direction
English source text inherits RTL direction. In the captures, question punctuation appears at the start (“?What outcome…”) and periods move to the beginning of several English paragraphs. The layout may remain RTL, but each English text run should be isolated with dir=auto/bdi or suitable plaintext bidi handling so punctuation follows its actual text direction. This is a source-preview rendering issue, not a Persian/Arabic linguistic judgment. Do not describe the RTL text rendering as fully verified until the repair is captured.

### Minor usability polish
The open-questions list uses canonical Q-MA-* IDs without the corresponding question wording. It remains legible as provenance, but respondent-facing labels would make those gaps easier to act on. Keep IDs as secondary references.

The earlier mobile footer-spacing P3 remains visible in these captures.

## Scope update
- Landing, question, review and matching-report presentation: PASS within the inspected English-source-preview scope; overall scores remain2/3.
- RTL geometry: visually supported, with the source-text bidi P2 above outstanding.
- Persian/Arabic language quality and shaping: unverified; no reviewed translations supplied.
- Priced software report and authenticated administration: visual evidence still pending at this point.
