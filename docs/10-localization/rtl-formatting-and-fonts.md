# RTL formatting and fonts

Owner: R15. Reviewers: R13, R16. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: NFR-10, AC-06,11. Open decisions: [register](../00-governance/assumptions-and-open-questions.md). Practice: [content-localization](../00-governance/skills/content-localization.md).

Persian fa and Arabic ar are RTL; other named language entries LTR in current product surface. Set document language/direction semantically; use logical start/end spacing. Do not reverse source arrays, financial digits, logos, code or media controls. Isolate mixed-script values such as project IDs, email, currency codes and URLs; user text uses direction auto as appropriate.

Format numbers, dates, currency and plurals using pinned locale/ICU data. Storage uses canonical ISO timestamps/decimal values independent from display. Language never selects tax/currency. Propose script-appropriate licensed font families with Latin, Cyrillic, Arabic, Devanagari, CJK and Armenian coverage; test shaping, diacritics, line boxes, weight matching and fallback. Font candidates such as Noto families require license/bundle review before adoption. Chinese Hans/Hant and region and Portuguese regional preferences remain owner decisions. At200% zoom and 320CSSpx long strings reflow; dense tables provide named region scrolling.
