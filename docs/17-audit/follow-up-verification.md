# Follow-up documentation verification

Owner: R31. Reviewers: R09, R13, R15, R22, R27 (simulated perspectives). Status: completed documentation checks,2026-10-29. Sources: [prior report](documentation-validation-report.md),[official OpenAPI references](../00-governance/references/openapi/README.md),[SVG text anchoring](https://www.w3.org/TR/SVG2/text.html#TextAnchoringProperties). Related: AC-06, AC-11, AC-15, G13, G17–G20. Open decisions: [owner register](../00-governance/assumptions-and-open-questions.md).

This follow-up supersedes the earlier statements that static previews and full schema checks were unavailable. It does not change the docs-only boundary,14 named languages, free use or no-payment requirement. No dependencies were installed.

| Check | Actual evidence/result | Boundary |
|---|---|---|
| Question schemas | All 123 schemas pass installed jsonschema 4.25.1 Draft 2020-12 meta-validation | Documentation schemas, not a running engine |
| Customer/startup/matching fixtures |8 fixtures,370 known answers pass full schema validation with FormatChecker | No psychometric/domain/linguistic validation |
| OpenAPI | Explicit OAS dialect2024-11-10; current official3.1 schema-base2026-08-03 passes with no errors | Informational schema cannot prove every normative/semantic requirement; no server test |
| Schema provenance | Four official JSON references preserved with URLs/SHA256; validation resolved OAS resources locally | Read-only reference retrieval, not package installation |
| Static visual previews |6 screen PNGs plus C4 preview,2560 ×1760, viewed by coordinating agent | Pillow/FreeType/RAQM subset renderer, not browser or general SVG conformance |
| Text bounds | Zero text outside1280 ×880 source canvases after correction | Bounds do not prove keyboard/DOM/reflow/accessibility |

Resolved findings:

- G17: RTL SVG text-anchor=end with direction=rtl anchored Persian labels at their left edge near the right canvas boundary. Eight RTL elements now use start anchoring. Four headings were outside the canvas before correction; all now fit.
- G18: Segoe UI did not render the checkbox glyphs in the preview. Replaced three glyphs with explicit vector boxes/checkmark; selected state is visible without a font symbol.
- G19: Shared question model incorrectly described stored answers as OPT IDs while bank schemas and rules use canonical values. Clarified option-record IDs versus canonical values, and topological dependency scheduling versus editorial bank order.
- G20: Nullable record fields could be mistaken for complete knowledge. Clarified per-field unknowns, whole-record unknown state and explicit no-deadline null meanings; schema annotations record deadline exceptions.
- C4 static preview now has directional arrowheads and an explicit transactional email boundary; it is labeled as a context subset with AI deferred.

The renderer supported every graphical element used here (rect, text, line and straight path segments), with installed Segoe UI/semibold and RAQM shaping. This is a task-specific rendering route; it cannot validate arbitrary SVG/CSS, embedded web content or production fonts. No font files were copied into the project. Persian sample copy remains unreviewed linguistically.

Evidence files: [question validation](question-schema-verification.json),[OpenAPI validation](openapi-schema-verification.json),[preview metrics/review](static-preview-checks.json).

Still open: full Mermaid/PlantUML parsing/rendering (no installed runtime found; the similarly named local file was a TextMate grammar), interactive/browser accessibility, qualified14-language review, actual rates/markets/operating policies and missing platform blueprint. These remain assigned, not reported as passing.
