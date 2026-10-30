# Localized content schema

Owner: R15. Reviewers: R14, R16. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: FR-21,26, AC-06. Open decisions: [register](../00-governance/assumptions-and-open-questions.md). Practice: [content-localization](../00-governance/skills/content-localization.md).

Business translations are database-managed entities tied to stable flow/question/option/report IDs and source hashes. Application UI strings are versioned developer-controlled catalogs; both have locale, key, text, placeholder specification, criticality, source revision, review status, reviewer/date and context. No business logic on translated labels.

Use ICU-style messages for plurals/selects with typed placeholders; reject dropped/renamed placeholders, unsafe markup and invalid syntax. Do not concatenate sentences. Canonical input normalization uses Unicode NFC where appropriate; preserve original user text for display/audit subject to privacy, do not normalize passwords/secrets or mutate identifiers. Glossary terms include estimate (preliminary, not quote), scope, unknown, not applicable, runway, contribution, RTO, RPO, FTE and commercial tier; each has definition, forbidden ambiguity, locale translation and reviewer. No translations marked approved without evidence.
