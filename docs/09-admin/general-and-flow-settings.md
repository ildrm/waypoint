# General and flow settings

Owner: R18. Reviewers: R05, R29. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: PARAM-*, WF-SETTINGS. Open decisions: [register](../00-governance/assumptions-and-open-questions.md). Practice: [architecture-data-api](../00-governance/skills/architecture-data-api.md).

Parameter definitions in 05-architecture/settings-inheritance.md own IDs/types/defaults. Form displays definition type, unit, allowed scope, null policy, shared value, flow override, effective value/source and impacted rules. Explicit override toggle differs from empty value. No string-to-number/boolean coercion.

Before publication compare old/new effective values in all affected flows, validate retained overrides and rerun reference scenarios. A default change creates successor manifests; existing runs retain pinned business settings. Revocation/session security remains live and cannot be overridden. Commercial rates/adjustments are references to approved estimate price books, never hardcoded settings. Missing definition/unused parameter is a validation error; unused example-only parameters must be labeled illustrative.
