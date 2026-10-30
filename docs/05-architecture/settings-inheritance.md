# Typed settings inheritance

Owner: R18. Reviewers: R05, R29. Status: proposed. Source: prompt. Related: WF-SETTINGS, AC-03, AC-08. Open: approval thresholds/retention.

Each parameter has stable ID, type, unit, allowed domain, null policy, default revision, override permission, owner and consuming rules. Missing differs from null; coercing text into number/boolean at publication is forbidden.

| Parameter | Type/unit | Proposed shared value | Allowed override | Consumer |
|---|---|---|---|---|
| PARAM-ESTIMATE-VALIDITY | integer/days | 30 | 1–90 with approval | Estimate expiry |
| PARAM-WEEK-HOURS | decimal/person-week hours | 40 | 1–60 with calendar evidence | Capacity |
| PARAM-REPORT-UNKNOWN | enum | partial_or_review | No | Completeness gate |
| PARAM-STARTUP-PRIORITIES | integer/items | 3 | Startup 1–5 | Action list |
| PARAM-REUSE-MAX-AGE | integer/days | 30 | 0–365 | Reuse confirmation |

These are illustrative design defaults; rates/base fees/discounts/factors/commercial defaults belong to an approved price book. Resolve a permitted flow override at pinned revision, otherwise the pinned shared default. Values replace the complete typed setting; no implicit nested-map merge. Security restrictions are not overridable business defaults.

Impact preview shows old/new effective values, affected flows/rules, retained overrides, invalid values and simulation deltas. A shared change publishes successor manifests for affected flows; old runs never silently change. Removing an override previews the inherited value and creates a new release. Live access revocation remains current. See [administration](../09-admin/general-and-flow-settings.md).
