# C01 detailed end-to-end reconciliation

Owner: R29. Reviewers: R28, R27. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: AC-04,05,14,16. Open decisions: [register](../00-governance/assumptions-and-open-questions.md). Practice: [estimation-commercial](../00-governance/skills/estimation-commercial.md).

Scope12 pages,3 CMS types,2 editorial roles, one lead route. Work packages and all 75 answer dispositions in C01 JSON.

| Role | Hours | Sell/hour USD | Sell amount | Internal cost/hour | Internal amount |
|---|---:|---:|---:|---:|---:|
| BA |40|60|2400|35|1400|
| UX |40|50|2000|30|1200|
| DEV |160|60|9600|35|5600|
| QA |64|45|2880|25|1600|
| OPS |16|45|720|30|480|
| Total |320||17600||10280|

DEV160=foundation40+CMS64+lead24+SEO/search16+permissions16. No fixed feature fee duplicates these hours. Contingency1760 (10% of 17600); discount968 (5% of 19360); net18392 excluding unconfigured taxes. No additional margin factor. Recurring350=hosting120+email20+backup30+maintenance4 ×45=180. First-year ownership22592=18392+4200. Platform charge0.

W1 BA40 -> W2 UX40 -> W3–6 DEV40/week -> W7 QA32 -> W8 QA32 and OPS16. In W8 QA24 Monday–Wednesday, OPS8 Thursday readiness, OPS8 Friday deployment, QA8 Friday smoke. Pre-release quality gate is56QA hours; final8 postdeploy smoke. Five distinct people, peak2, average aggregate1.0 FTE across 8 weeks; no person above40 h/week. No holidays assumed. Planning8–11 weeks, not calibrated confidence.

Trace: content/page counts and authoring from Q-SW-CONTENT, identity/roles from IDENTITY/ROLES, lead route from LEADS, forecast20 RPS from REQUESTS, quality/recovery from AVAILABILITY/RECOVERY. Work-package hours are explicit expert assumptions, not a false formula from page count. Vendor fit, hosting region/legal review and real rates still require validation.

Lean248 h uses BA32, UX32, DEV120, QA48, OPS16 =>B13600, N14212. Accelerated336 h adds BA8+DEV8coordination =>B18560, N19395.20; split developer tracks shorten build only, not content/approval waits.
