# content-localization practice profile

Owner: R15. Reviewers: R31, R34. Status: incorporated-as-documentation-profile; revision1, 2026-10-29.
Related capability families: 12. Sources/assumptions: authorized brief and [reference register](../standards-and-references.md). Open decisions: [owner register](../assumptions-and-open-questions.md).

Purpose: provide reusable methods for the named capability families under the documentation-only boundary.

Owner roles: R15, R16.

Inputs: Source keys, terminology, locale list when provided.

Method: Separate business translations/UI strings; preserve canonical IDs; track source hashes and review staleness; format via locale-aware libraries; explicit publication coverage and bidi handling.

Outputs: Locale registry, translation lifecycle, export plan.

Relevant standards/practices: BCP47, W3C i18n, Unicode CLDR/ICU.

Review checklist: Exactly 14 owner-named languages; script/region/source decisions explicit; semantic equivalence and reviewer evidence needed.

Limitations: English documentation does not equal linguistic approval. This is a domain practice reference, not a native skill installation or specialist approval.

