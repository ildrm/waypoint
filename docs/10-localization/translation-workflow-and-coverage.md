# Translation workflow and coverage

Owner: R16. Reviewers: R15, R27. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: WF-LOCALIZE, AC-06. Open decisions: [register](../00-governance/assumptions-and-open-questions.md). Practice: [content-localization](../00-governance/skills/content-localization.md).

Coverage denominator is all applicable required source keys for a flow/release, including questions/options/help/errors/units/explanations/report templates. Track per language: translated, reviewed, approved-current-source, stale and export/font QA. Exactly 14 owner-named languages must pass production release gate. Source locale proposed en; not assumed approved.

Workflow: extract context/keys -> assign qualified language reviewer -> draft -> linguistic and domain review -> visual/assistive technology checks -> approve exact source hash -> publish manifest. Source edit invalidates affected approvals; harmless spelling correction still requires triage and explicit semantic decision. Critical fallback denied in published assessment; noncritical help fallback only if policy approved and visibly labeled. Translation WBS includes source editing, words,14-language review, domain glossary, RTL, CJK/font and report QA. Cost coefficients remain unset rather than fabricated.
