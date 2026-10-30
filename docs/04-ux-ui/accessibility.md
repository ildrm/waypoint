# Accessibility design and criteria

Owner: R13. Reviewers: R08, R19, R27. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: AC-11, FR-26, NFR-01. Open decisions: [register](../00-governance/assumptions-and-open-questions.md). Practice: [accessibility](../00-governance/skills/accessibility.md).

WCAG 2.2 AA is target, not achieved certification. Native controls preferred over custom ARIA. Correct large-text threshold is18pt (24CSSpx), or14pt bold (~18.67CSSpx), not18CSSpx.

| Criteria | Product behavior / future verification |
|---|---|
|1.1.1,1.3.1,1.3.2 | Text alternatives, headings/fieldset/table associations, reading order incl RTL |
|1.4.3,1.4.11 | Normal text≥4.5:1, large≥3:1; essential boundaries/focus≥3:1; verify all states |
|1.4.4,1.4.10,1.4.12 | Text resize200%, reflow320CSSpx, text-spacing overrides; no clipped copy/actions |
|2.1.1,2.1.2 | All tasks keyboard, including reorder alternatives; no traps except escapable modal |
|2.4.1,2.4.3,2.4.7,2.4.11 | Skip link, logical order, visible unobscured focus, sticky offsets |
|2.5.3,2.5.7,2.5.8 | Visible names in accessible labels; non-drag move controls;24 px AA target baseline,48 px product target |
|3.1.1,3.1.2 | Correct page/content language |
|3.2.2,3.2.6 | No automatic question advance on selection; consistent help placement |
|3.3.1–3.3.4,3.3.7,3.3.8 | Associated errors/suggestions/review, avoid redundant entry, accessible authentication with paste/autofill |
|4.1.2,4.1.3 | Names/roles/states and restrained status messages for save/jobs |

Reduced motion and no-essential-animation practices exceed selected baseline where relevant. Static SVG does not prove DOM/keyboard/screen-reader behavior.

Computed sRGB contrast pairs:

| Pair | Foreground/background | Ratio |
|---|---|---|
| body | #172B2A / #FFFFFF | 14.84:1 |
| supporting | #526260 / #FFFFFF | 6.41:1 |
| action label | #FFFFFF / #006B5E | 6.43:1 |
| error | #A12835 / #FFFFFF | 7.32:1 |
| warning | #775100 / #FFFFFF | 7.09:1 |
| input boundary | #748582 / #FFFFFF | 3.87:1 |
| focus | #172B2A / #F5F7F6 | 13.79:1 |

Decoration #D6DEDB is not an essential input boundary. Focus must be checked on every actual background and forced colors, not only these pairs.
