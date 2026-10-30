# Reports and PDF localization

Owner: R15. Reviewers: R16, R22, R13. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: WF-RESULT, AC-06,10. Open decisions: [register](../00-governance/assumptions-and-open-questions.md). Practice: [content-localization](../00-governance/skills/content-localization.md).

Structured facts/amounts are locale-neutral. Rendering selects approved locale template/version and embeds licensed fonts with shaping and accessible reading order. Include locale, direction, currency code, release/input/result versions, validity, caveats and source references. Preserve exact money and meaning across language changes; label draft translation/export limitations.

Check line wrapping, page breaks, table headers, repeated units, RTL navigation/mixed-script IDs, CJK breaks, Devanagari/Armenian glyphs and selectable text. Tagged PDF/accessibility requires renderer capability proof; provide accessible HTML/structured report alternative if PDF support incomplete. Failed export leaves structured report accessible, job retry idempotent and download live-authorized. No actual production PDF or language approval was generated.
