# Static annotated screen set

Owner: R09. Reviewers: R08, R13, R16. Status: static preview inspected,2026-10-29; not human/linguistic approval. Sources: authorized brief and [visual verification](../../17-audit/follow-up-verification.md). Related: UI-01, UI-02, UI-04, UI-05, UI-08, UI-09, UI-10. Open decisions: source/region/script choices, brand and qualified language review.

These are static documentation illustrations, not a functional UI. Each SVG preserves editable vectors, text, title/description and annotations. PNGs are2× previews rendered with existing Pillow/FreeType/RAQM; the coordinating agent inspected all six.

| Screen | Vector source | PNG preview |
|---|---|---|
| flow choice | [SVG](01-flow-choice.svg) | [PNG](01-flow-choice.png) |
| question clarification | [SVG](02-question-clarification.svg) | [PNG](02-question-clarification.png) |
| answer review | [SVG](03-answer-review.svg) | [PNG](03-answer-review.png) |
| result estimate | [SVG](04-result-estimate.svg) | [PNG](04-result-estimate.png) |
| admin flow editor | [SVG](05-admin-flow-editor.svg) | [PNG](05-admin-flow-editor.png) |
| translation pricing | [SVG](06-translation-pricing.svg) | [PNG](06-translation-pricing.png) |

Desktop canvases1280 ×880; review includes390 px mobile frame. RTL Persian admin uses isolated Latin IDs/numbers. Text anchoring and missing checkbox symbols were corrected after rendering; no text leaves the canvas in recorded metrics.

Limits: the renderer implements only the vector subset used in these files; browser rendering, DOM semantics, keyboard, reflow and qualified linguistic review remain unverified. Persian labels are illustrative; no translation is marked approved. [Metrics and review](../../17-audit/static-preview-checks.json).
