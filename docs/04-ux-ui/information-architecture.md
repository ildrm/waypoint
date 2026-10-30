# Information architecture

Owner: R08. Reviewers: R02, R19, R32. Status: proposed, 2026-10-29. Sources: prompt §§7,13,15; [experience profile](../00-governance/skills/experience-research.md). Related: WF-SESSION, WF-RESULT, WF-AUTHOR, WF-SUPPORT, AC-01, AC-07. Open decisions: public name and authenticated save policy.

Public navigation has **Choose a flow**, **Saved work**, and **Help**, with locale control in a consistent utility position. A project contains distinct runs; a run contains released flow configuration, answers, review, reports and estimate revisions. Breadcrumbs identify Project → Flow → Stage; labels translate while IDs remain stable.

| Area | Hierarchy and task | Visibility / entry |
|---|---|---|
| Choose a flow | Startup guidance; software planning; service matching | Published flow cards; retired flows excluded for new runs |
| Questionnaire | Stage overview → current question → clarification | Applicable questions only; stage index permits backward review |
| Answer review | Sections → answer summary → edit | Unknowns and contradictions visible; hidden answers excluded with explanation |
| Results | Overview; scope; recommendations; effort/price; schedule/team; assumptions; actions | Partial sections labeled; every output has evidence |
| Compare scenarios | Base revision → changed scope → compare → adopt | Never replaces base silently; project ownership enforced |
| Saved work | Project list → run status → resume/report | Authenticated ownership or private anonymous resume token |
| Help | Glossary; save/recovery; estimate meaning; support | Consistent order; contextual help returns to trigger |

Admin is a separate authorized surface. Groups are **Flows**, **Library**, **Translations**, **Pricing**, **Publication**, **Simulation**, **Insights**, **Operations**, and **Access & audit**. The [admin architecture](../09-admin/admin-information-architecture.md) owns permissions and resource semantics.

| Group | Main task hierarchy | Cross-links |
|---|---|---|
| Flows | Flow → draft release → stages/questions → rules → output | Edited locale; dependency view; simulator trace |
| Library | Features, classifications, reusable questions | Pinned consumers and affected drafts |
| Translations | Release/content locale → completeness → item → review | Source revision; stale reason; reviewer |
| Pricing | Price book → currency/market → items/rates → dates | Inherited values, overrides and simulation |
| Publication | Review bundle → findings → approvals → schedule | Immutable release manifest and rollback impact |
| Operations | Jobs, exports, incidents, retention requests | Redacted run reference; authorized recovery |

Every workspace shows flow, draft/release version, admin UI locale, edited content locale, market and currency where relevant. Changing edited content locale never changes the admin interface language. Production data and draft simulations receive explicit labels.

Search indexes only authorized records, accepts labels or IDs, and preserves filters on return. Permission denial must not reveal hidden record names. Mobile admin navigation becomes a labeled menu; essential tasks require neither hover nor drag.
