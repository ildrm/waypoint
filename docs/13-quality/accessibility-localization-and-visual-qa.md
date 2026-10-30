# Accessibility, localization and visual QA

Owner: R13. Reviewers: R09, R15, R16, R27. Status: static preview review completed,2026-10-29; interactive and linguistic validation pending. Sources: authorized brief,[preview evidence](../17-audit/static-preview-checks.json),[follow-up](../17-audit/follow-up-verification.md). Related: AC-06, AC-11. Open decisions: source/region/script choices and qualified language reviewers.

Six static screen PNGs now cover desktop LTR flow/question/result/editor, mobile390 px review, and Persian RTL admin. A seventh PNG previews C4 context. Installed Edge failed in the initial pass; the successful route used existing Pillow, FreeType and RAQM to render the exact rect/text/line/straight-path subset. The coordinating agent viewed all seven; this is simulated visual review, not browser validation.

Findings resolved: RTL headings left the canvas because text-anchor=end reverses its physical edge for RTL; changed8elements to start. Checkbox symbols rendered as missing glyphs; replaced3with vectors. Improved spacing and C4 arrow directions/email boundary. Final metrics show no text outside source canvases.

Future product matrix:320/390/768/1280CSSpx,200%text/400%zoom, forced colors, reduced motion, keyboard/touch and representative screen readers. Cover all 14 named language scripts with qualified reviewers; source, Chinese variant and Portuguese region still open. Check expansion, shaping, glyphs, bidi isolation, reading order, labels/errors, unobscured focus, non-drag alternatives and 48 px product targets.

Selected contrast pairs in 04-ux-ui/accessibility.md are computed evidence, not proof for every background/state. Static PNG review does not certify WCAG 2.2 or production font/layout equivalence. Native-language usability, assistive technology and qualified linguistic approval remain pending.
