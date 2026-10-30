# Locale registry

Owner: R15. Reviewers: R16, R01. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: FR-26, NFR-10, AC-06. Open decisions: [register](../00-governance/assumptions-and-open-questions.md). Practice: [content-localization](../00-governance/skills/content-localization.md).

Production language identities are owner-confirmed. Base BCP47 tags are registry identifiers, not a claim that regional formatting/script choices are settled.

| Language | Base tag | Direction | Script/font requirement | Open detail |
|---|---|---|---|---|
| English | en | LTR | Latin | Source language proposed en |
| Persian | fa | RTL | Arabic shaping + Persian glyphs | Country/calendar preference |
| French | fr | LTR | Latin accents | Region |
| German | de | LTR | Latin | Region |
| Russian | ru | LTR | Cyrillic | Region |
| Hindi | hi | LTR | Devanagari shaping | Numbering preference |
| Chinese | zh | LTR | Han; appropriate CJK font | Hans/Hant and region owner decision |
| Japanese | ja | LTR | Kana/Han | Locale formatting |
| Spanish | es | LTR | Latin | Region |
| Portuguese | pt | LTR | Latin | pt-BR/pt-PT or other region |
| Arabic | ar | RTL | Arabic shaping | Regional language/reviewer policy |
| Turkish | tr | LTR | Latin dotted/dotless I | Locale-aware casing |
| Armenian | hy | LTR | Armenian | Font coverage |
| Italian | it | LTR | Latin | Region |

Exactly 14 language entries are required; aliases/formatting variants do not silently count as extra languages. No language is marked linguistically approved. Source locale proposed en; admin UI locale is independent from the locale of edited business content. Currency, market and jurisdiction remain separate fields, never inferred from the selected language. Invalid/unsupported tags yield a supported-language choice, not silent semantic fallback. Locale rows include canonical tag, direction, display/native name, source flag, fallback policy, reviewer, font bundle/version, coverage status and publication manifest.
