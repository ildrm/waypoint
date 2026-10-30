# Motion contract

Owner: R11. Reviewers: R08, R13, R19. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: CMP-01,05,06,07, NFR-16. Open decisions: [register](../00-governance/assumptions-and-open-questions.md). Practice: [visual-system-motion](../00-governance/skills/visual-system-motion.md).

All values are proposed for validation. Motion never delays input, changes focus by itself or carries sole meaning.

| Trigger | Element/start→end | Duration/easing | Interruption/focus | Reduced motion |
|---|---|---|---|---|
| Choice selected | Border/tint state, check appears |120 ms ease-out | Immediate semantic selected state; repeat selection cancels prior transition; focus stays control |Instant border/check |
| Next valid | Question opacity1→0→1, no spatial slide |160 ms ease-out | Save/revision independent; abort stale view; focus new heading once after update |Instant replacement + heading focus |
| Help expanded | Inline help opacity0→1 |120 ms ease-out | Toggle interrupts; trigger retains focus; no essential hidden hover content |Instant display |
| Autosave acknowledged | Status text pending→saved |No spinner required; optional120 ms crossfade |Polite status throttled; not every keystroke; focus unchanged |Text change |
| Error submit | Summary appears |Instant, no shake |Focus summary; linked correction fields |Same |
| Report loading | Reserved status block |Static status; no endless decorative loop |Cancel/retry accessible; timeout explains |Same |
| Scenario change | Exact values replace and changed badges |≤200 ms opacity |No animated counting of money; new values immediately inspectable |Instant values |

Performance: transform/opacity only where motion needed, no layout-thrashing height interpolation, no background autoplay. Disabled/reduced-motion mode preserves all information. Later test on low-end phone, interrupt rapid Next/back, keyboard and assistive technology.
