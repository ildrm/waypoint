# Visual direction: a clear planning desk

Owner: R09. Reviewers: R07, R10, R12, R13. Status: proposed art direction, 2026-10-29; no brand approval. Sources: prompt §13; design/design-system skills; [visual profile](../00-governance/skills/visual-system-motion.md). Related: UI-01–UI-12, CMP-01–CMP-13, AC-11. Open decisions: identity, licensed fonts and source/region/script choices.

The audience includes founders exploring uncertainty and technical/commercial staff reviewing detailed plans. The direction resembles an organized planning desk: warm paper canvas, white cards, ink headings, deep teal actions, fine separators, generous question spacing and compact evidence tables. A waypoint motif indicates next useful work. Avoid trophies, rockets and scores implying guaranteed success.

Documentation illustrations use a system UI sans serif. Production fonts are selected per script with similar perceived size rather than identical metrics. Proposed Latin scale: 14 px metadata, 16 px body, 20 px card title, 28 px section title, 40 px hero; body line height 1.5, headings 1.2; weights 400/600/700. Arabic-script text starts at 18 px with line height 1.7 pending native-reader QA. Financial columns use tabular numerals with explicit currency codes.

| Relationship | Representative values | Usage |
|---|---|---|
| Canvas / surface | #F5F7F6 / #FFFFFF | Frame the task / contain editable content |
| Ink / supporting text | #172B2A / #526260 | Headings and body / explanatory copy |
| Action / selected tint | #006B5E / #E5F2EE | Actions and links / selected background plus border/check |
| Boundary / decoration | #748582 / #D6DEDB | Essential input boundary / nonessential separator |
| Danger / caution | #A12835 / #775100 | Errors / review warnings, both accompanied by text |

Spacing: 4, 8, 12, 16, 24, 32, 48, 64 px. Public questions max 680 px; optional help rail 280 px when it fits reading order. Cards have 12 px radius, 1 px boundary and light elevation. Avoid nested cards. Controls are 48 px high. Admin rows use 48 px and 14–16 px text; dense context comes from grouping, never illegible scaling.

Icons use 24 ×24 units, 2 unit strokes and rounded joins. Every action has a text name. Illustrations use the same ink/teal/paper palette and no embedded words. Direct chart labels replace decorative gradients. Light mode is the release-one baseline; forced colors are required. Dark semantic tokens are a proposal for later verification, not a completed dark theme. Do not invert illustrations automatically.

Financial/architecture graphics must use exact structured facts. The result mockup's USD 18,392 reconciles a disclosed hypothetical subtotal, contingency and discount. No generated image represents exact numbers. [Tokens](design-tokens.json), [responsive rules](responsive-layouts.md) and [mockups](mockups/README.md) define handoff.
