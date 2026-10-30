# Standards and references

Owner: R31. Reviewers: R17, R23, R34. Status: primary-source register, accessed2026-10-29. Source: pages below and technical-agent source checks. Related: FR/NFR/ADR; open DEC-01–10. Practice: [assurance](skills/quality-documentation.md).

Version-sensitive choices were checked against primary sources. “Selected edition” is not always latest. Public ISO summaries were accessible; full paid standards were not read or purchased. Referencing a standard is neither certification nor an installed skill. Apple motion content was unavailable beyond its landing; motion timings are explicit proposed design decisions.

| ID | Title/edition | Primary URL | Use and limitations |
|---|---|---|---|
| REF-01 | ISO/IEC/IEEE29148:2018, ed2 | [Primary source](https://www.iso.org/standard/72089.html) | Requirements lifecycle and information-item scope; public abstract only; page says confirmed2024 and draft replacement pending. |
| REF-02 | ISO9241-210:2019, ed2 | [Primary source](https://www.iso.org/standard/77520.html) | Human-centred design scope; public summary; no paid clauses or conformance claim. |
| REF-03 | ISO/IEC25010:2023, ed2 | [Primary source](https://www.iso.org/standard/78176.html) | Quality model categories inform NFR coverage; public abstract only. |
| REF-04 | WCAG 2.2 Recommendation,12Dec2024 | [Primary source](https://www.w3.org/TR/WCAG 22/) | AA target and criteria mapping; static design cannot certify conformance. |
| REF-05 | WAI-ARIA APG radio pattern | [Primary source](https://www.w3.org/WAI/ARIA/apg/patterns/radio/) | Keyboard/semantics reference; prefer native controls and later browser verification. |
| REF-06 | OWASP ASVS 5.0.0 | [Primary source](https://asvs.owasp.org/) | Stable release shown30May2025; proportional security verification planning, not certification. |
| REF-07 | OWASP API Security Top10:2023 | [Primary source](https://api-security.owasp.org/) | API threat categories; does not replace detailed control verification. |
| REF-08 | OWASP MASVS, online catalog | [Primary source](https://mas.owasp.org/MASVS/) | Conditional mobile review domains; version not inferred from landing page. |
| REF-09 | OMG UML2.5.1 | [Primary source](https://www.omg.org/spec/UML/2.5.1) | Use-case/activity/state/class notation; no code generation. |
| REF-10 | OMG BPMN2.0.2 | [Primary source](https://www.omg.org/spec/BPMN/2.0.2) | Process concepts only; conceptual swimlanes not claimed conformant BPMN. |
| REF-11 | OMG DMN1.5 | [Primary source](https://www.omg.org/spec/DMN/1.5) | Decision-table concepts; selected edition checked, not claimed latest. |
| REF-12 | C4 diagrams | [Primary source](https://c4model.com/diagrams) | Context/container/component scope and relationship clarity. |
| REF-13 | OpenAPI 3.2.1 latest /3.1.2 selected | [Primary source](https://spec.openapis.org/oas/latest.html) | Latest page showed3.2.1 dated10Sep2026; interface design deliberately uses3.1.2 for conservative compatibility. |
| REF-14 | OpenAPI 3.1.2 | [Primary source](https://spec.openapis.org/oas/v3.1.2.html) | Selected specification; supported by technical agent source lookup; package tooling not installed. |
| REF-15 | JSON Schema2020-12 | [Primary source](https://json-schema.org/specification) | Interface shape; dialect selected, no full schema-validator execution implied. |
| REF-16 | W3C structural RTL markup | [Primary source](https://www.w3.org/International/questions/qa-html-dir) | Document direction/logical properties and mixed-direction input. |
| REF-17 | Unicode UTS35 /CLDR 49 | [Primary source](https://www.unicode.org/reports/tr35/) | Current page showed49 dated21Oct2026; actual runtime ICU/CLDR version must be pinned later. |
| REF-18 | BCP47 RFC5646 | [Primary source](https://www.rfc-editor.org/info/rfc5646/) | Language tags; locale registry still owner confirmed14 languages; region/script/source pending. |
| REF-19 | Apple HIG Motion | [Primary source](https://developer.apple.com/design/human-interface-guidelines/motion) | Landing reached but substantive content JS-gated; linked Markdown fetch unsupported. No detailed Apple-derived claims. |
| REF-20 | PHP supported versions | [Primary source](https://www.php.net/supported-versions.php) | Technical agent checked PHP 8.5 support dates; patch/runtime dependency resolution still pending. |
| REF-21 | Laravel release support | [Primary source](https://laravel.com/framework/docs/releases) | Technical agent checked Laravel 13 PHP 8.3–8.5, security support to 17Mar2028. |
| REF-22 | Filament 5 installation | [Primary source](https://filamentphp.com/docs/5.x/introduction/installation) | Technical agent checked minimums; PHP/Laravel minimum alone does not prove compatibility. |
| REF-23 | Filament 5 support composer metadata | [Primary source](https://raw.githubusercontent.com/filamentphp/filament/5.x/packages/support/composer.json) | Technical agent checked Illuminate13 acceptance; no dependency installation run. |
| REF-24 | UVA Darden Effectuation | [Primary source](https://www.darden.virginia.edu/effectuation) | Entrepreneurship method context; does not validate startup instrument or success rates. |
| REF-25 | Strategyzer Business Model | [Primary source](https://www.strategyzer.com/library/what-is-a-business-model) | Business-model themes only; assessment not externally validated. |

Reference revisions require impact review when framework support, schema dialect, accessibility/security baselines or locale data change. Freeze actual runtime/library versions during implementation; source checks do not verify a deployable dependency set.



Follow-up primary references, accessed2026-10-29:

| ID | Title/edition | Primary URL | Use/limits |
|---|---|---|---|
| REF-26 | SVG 2 text anchoring | [W3C SVG text](https://www.w3.org/TR/SVG2/text.html#TextAnchoringProperties) | Start/end anchoring follows inline direction; used to fix RTL labels, not linguistic approval |
| REF-27 | OpenAPI 3.1 schema-base2026-08-03; dialect/meta2024-11-10 | [Official schema index](https://spec.openapis.org/oas/) | Informational structural validation; prose prevails. Preserved source copies and hashes in references/openapi |
