# Actual landing-page visual observations

Reviewed screenshot artifacts from the running application at http://127.0.0.1:8460 on2026-10-29:
- output/playwright/home-1440.png (1440×2854)
- output/playwright/home-768.png (768×3183)
- output/playwright/home-375.png (375×4020)

The actual render supports the chosen planning-desk identity. Teal/ink typography, restrained lines, numbered assessment rows, original evidence cards and broad breathing room form a coherent composition. The375px capture stacks the landing content and preserves readable headings, CTA labels and flow introductions; the768px capture keeps a two-column hero and secondary sections. There is no visible page-wide horizontal clipping in these captures. This visual check does not prove absence of DOM overflow at every state.

A concrete P2 craft issue remains in this capture set: the absolutely positioned evidence cards obscure text. At1440px the teal next-step card overlaps the footnote “Your answers lead the way. Uncertainty stays visible.” At768px the evidence card covers the question card's “Start with the outcome…” note. Reserve space for actual text height, reposition the footnote, and use a responsive stack/grid where the two-column hero becomes too narrow. Do not solve this by hiding meaningful copy.

Minor P3: on375px the two-sentence starting-point introduction renders “time.Only” because its br is display:none with no separating whitespace. Preserve a space or use separate block text.

Browser action/hover/keyboard/reduced-motion checks were driven by the parent, not this evaluator. At this stage this evaluator has inspected the screenshot artifacts and static source only. Original contract failures are being repaired and no final re-score is assigned in this note.
