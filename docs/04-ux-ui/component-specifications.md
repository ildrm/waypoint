# Component specifications

Owner: R10. Reviewers: R08, R13, R19. Status: proposed, 2026-10-29. Sources: prompt §13; design-system token/component references; [visual profile](../00-governance/skills/visual-system-motion.md), [accessibility profile](../00-governance/skills/accessibility.md). Related: UI-01–UI-15, AC-02, AC-06, AC-11. Open decisions: frontend library; final script fonts.

Components use [semantic tokens](design-tokens.json). Public target height is 48 px. Focus: 3 px ink outline, 2 px surface offset. Labels persist; reading order agrees with visual order. RTL never reverses numerical strings.

| ID | Anatomy / variants | Interaction and semantics | Layout / states |
|---|---|---|---|
| CMP-01 Question | Stage, heading, help, field group, error, navigation | Fieldset/legend; stable ID; helper/error associations; no auto-advance | Max 680 px; unknown/invalid/read-only; keep input on failure |
| CMP-02 Choice | Native radio/checkbox, label, explanation | Native arrows/Space for radio; independent checkbox; disclose multiselect limit; no substantive default | ≥48 px label target; selected border + check; focus distinct from selection |
| CMP-03 Numeric/detail | Label, value, unit/period, hint, error | Normalize server-side; show localized display; never empty→0 | ≥48 px; long units wrap; retain raw invalid entry |
| CMP-04 Help | Text trigger, definition/example, close | Inline expanded state; dialog for long help; Escape/return focus | No hover-only essential info; collapsed/expanded |
| CMP-05 Progress | Stage, completed applicable stages, index | List/current step; announce change once; no fixed future count | Wrap; current/completed/unavailable text |
| CMP-06 Navigation/save | Back, Next, Save and exit, timestamp | Real buttons; Next submits; Back avoids unrelated validation; polite save status | One primary; pending preserves label; saving/saved/error/offline |
| CMP-07 Alert/error | Icon, heading, explanation, correction | Submitted error summary receives focus; linked field errors; urgent async failure once | Text plus color; blocking messages not dismissible |
| CMP-08 Review | Section, question/answer/status, edit | Named links (“Edit peak requests”); unknown/inactive distinct | Table desktop; definition cards phone; complete/stale/conflict |
| CMP-09 Estimate | Currency/validity, item/quantity/rate/amount, adjustments/tax/recurring | Headers/totals; evidence disclosure; missing price never invented total | Inline-end numeric alignment; isolated values; partial/stale/expired/revised |
| CMP-10 Evidence/chart | Conclusion, source answers, assumptions, visual/data table | Alternative exact table; units and scenario meaning | Direct labels; no universal startup score; empty/review |
| CMP-11 Dialog | Title, impact, affected sections, Cancel/Confirm | Named modal, contained focus, Escape, return focus; safe initial choice | Max560 px or viewport minus32 px; no clipped footer at zoom |
| CMP-12 Admin form/table | Context, fields, inheritance, actions, revision | Move buttons alongside drag; concurrency token; scoped authorization | 48 px rows; inherited/override/draft/published/conflict/read-only |
| CMP-13 Locale | Autonym/label, current value, coverage warning | UI locale distinct from edited content locale; disclose unavailable copy | Script font; partial/stale/unavailable |

| State | Appearance / text | Focus and persistence |
|---|---|---|
| Default | Clear label, recognizable boundary | Ordinary task order |
| Hover | Selected tint | No exclusive functionality |
| Focus | 3 px ink ring, 2 px surface gap | Unobscured, scroll margin |
| Selected | Check/dot + teal border/tint | Native state announcement |
| Disabled | Readable reason, subdued surface | Reason outside disabled target |
| Loading | Stable label/status, reserved geometry | Retain trigger; prevent duplicate commit |
| Empty | Explanation + relevant next step | No fake chart/total |
| Invalid | Summary + corrective field message | Focus submitted summary |
| Server failure | Not saved + Retry | Keep local entry for recovery |
| Saved | Acknowledged timestamp | Polite, not every keystroke |
| Stale | Name changed source/version | Review before recalculation |
| Conflict | Changed elsewhere + compare/reload | No silent overwrite; retain local copy |
| Offline/interrupted | Last saved time + limitation | No sensitive durable browser cache by default |
| Partial | Missing sections + evidence needed | Available content accessible; partial export label |

Verify native behavior on the eventual browser/assistive-technology matrix. SVG pictures cannot prove semantics, keyboard or focus. A library default is not compliance evidence.
