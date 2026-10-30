# Evaluation — Attempt1

## Overall Verdict: MAJOR REVISION

## Overall Assessment
The implementation follows the planning-desk direction: warm neutral surfaces, deep teal, editorial headings, a custom waypoint/evidence composition, and calm assessment screens. Static structure is promising, but several visible controls do not fulfill their actual server contracts. Visual approval is withheld because no browser provider was available during this review.

This is a fresh-context review by the same model provider, not a cross-provider review. Reviewed2026-10-29. Browser inventory returned apps=[] and browsers=[]; the parent is investigating installed-browser alternatives. No screenshot, responsive-render, hover, screen-reader or keyboard interaction claim is made here.

## Scores
| Criterion | Score | Status | Weight | Notes |
|---|---|---|---|---|
| Design Quality |2/3 provisional | Unverified visually | HIGH | Consistent ink/teal/paper tokens and deliberate calm question/report composition in source; screenshots required before final approval. |
| Originality |2/3 provisional | Unverified visually | HIGH | Custom waypoint trail, evidence cards, numbered flow list and original native motion rather than reference-library assets. |
| Craft |1/3 provisional | PASS on static evidence only | MEDIUM | Logical CSS, system/script fonts, restrained type hierarchy, responsive breakpoints, forced-colors and reduced-motion alternatives exist. Actual reflow and script shaping remain unverified. |
| Functionality |0/3 | FAIL | MEDIUM | Matching output is omitted, anonymous recovery code is flashed to a page that does not show it, and every visible support form uses server-invalid request types. |

## What's Working Well
- The public application is server rendered and forms preserve canonical values, CSRF, revisions and idempotency keys.
- The question screen explicitly offers unknown/not-decided answers and avoids default substantive choices.
- Separate numeric zero, boolean false and missing states are represented in controls and report fallbacks.
- Choice controls have large label targets; skip link, error-summary focus, semantic fieldsets, headings and native progress exist.
- Native CSS/JavaScript includes explicit reduced-motion and forced-colors behavior. Authoritative price text is immediately shown without animated counting.
- Fourteen locale options and the English source-preview notice are explicit; the UI does not falsely claim reviewed translations.

## Issues Found

### Issue1 [P1]: Anonymous recovery code is consumed before it is visible
- **What**: AssessmentController::store flashes resume_token then redirects to runs.show. Only runs/index renders the token, so the first question request consumes the flash without showing the code.
- **Where**: app/Http/Controllers/AssessmentController.php:16; resources/views/runs/index.blade.php:8; resources/views/runs/show.blade.php.
- **Why it matters**: An anonymous user cannot retain the promised recovery code for interruption recovery.
- **Suggested fix**: Render a recovery notice on the first question screen, or preserve the token until the recovery screen is visited. Keep the code out of URLs and logs.

### Issue2 [P1]: Matching results are never shown
- **What**: ReportBuilder emits output.matching with eligible/excluded alternatives and tie policy. Neither the public report nor readable export renders matching.
- **Where**: app/Domain/ReportBuilder.php:34; resources/views/reports/show.blade.php:39; resources/views/reports/export.blade.php:2.
- **Why it matters**: A completed matching assessment gives a generic summary without its actual recommended offerings, eligibility reasons or alternatives.
- **Suggested fix**: Add a semantic matching section to screen and export, showing eligible alternatives, exclusions, unresolved evidence and tie policy.

### Issue3 [P1]: Every visible support request is rejected
- **What**: Form options support/export/deletion/correction do not match the controller values help/estimate_review/export_problem/privacy_export/privacy_delete. There is no deletion-consent checkbox.
- **Where**: resources/views/reports/show.blade.php:81; app/Http/Controllers/AssessmentController.php:30.
- **Why it matters**: The support/privacy workflow is unusable; authorized deletion can never meet its consent condition through the UI.
- **Suggested fix**: Align canonical option values with the server contract, label them clearly and add an explicit unchecked confirmation for deletion requests.

### Issue4 [P2]: Work-package effort details display unknown despite known role hours
- **What**: The estimator supplies work_packages[].role_hours, but the table reads role and hours/base_hours only.
- **Where**: resources/views/reports/show.blade.php:50; app/Domain/ProjectEstimator.php:18.
- **Why it matters**: A real estimate's breakdown hides its known effort and roles, making totals hard to reconcile.
- **Suggested fix**: Render one row per role_hours entry or normalize packages to the table contract before rendering.

### Issue5 [P2]: Administration exposes unsupported operations
- **What**: Catalog shows clone/preview, prices shows preview, staff shows clone/preview/suspend/restore and support shows retry, while backend branches reject or do not implement those actions.
- **Where**: resources/views/filament/pages/workspace.blade.php:36-37 and57; app/Domain/AdminWorkspace.php:18-25.
- **Why it matters**: Visible staff controls lead to422 instead of completing the named task.
- **Suggested fix**: Limit controls to section-supported operations, or implement the corresponding authorized behavior. Support should expose the implemented status/assignment form.

### Issue6 [P2]: Staff with specialized roles land on an unauthorized default page
- **What**: Workspace defaults to flows before rendering, then requires content_author. A translator/support/price-only user receives403 instead of section navigation.
- **Where**: app/Domain/AdminWorkspace.php:10; resources/views/filament/pages/workspace.blade.php navigation.
- **Why it matters**: Valid staff cannot find their authorized tools after sign-in.
- **Suggested fix**: Select the first authorized section or a shared dashboard and omit unauthorized section links.

### Issue7 [P2]: Select-question validation errors are not programmatically associated
- **What**: Choice error text has no ID and the question fieldset only references help; radio/checkbox controls do not expose the generic value error through aria-describedby or aria-invalid.
- **Where**: resources/views/runs/show.blade.php:39-61.
- **Why it matters**: Screen-reader users returning to an invalid choice group lack a reliable error association.
- **Suggested fix**: Give the error a stable ID, add it to the fieldset/control descriptions and mark invalid controls/groups when the server reports value errors.

### Issue8 [P2]: Translation review omits the original source
- **What**: The translation editor shows key and target text but not source_text/current source revision/hash or approval metadata.
- **Where**: resources/views/filament/pages/workspace.blade.php translation editor.
- **Why it matters**: A reviewer cannot compare meaning or see why a stale translation needs review without a separate data tool.
- **Suggested fix**: Show escaped read-only source text, source locale/hash, current status and last reviewer beside target text.

## Priority Fixes for Next Attempt
1. Repair recovery-code visibility, support values/consent, and matching result rendering; verify the real journey with browser or Laravel feature tests.
2. Reconcile estimate table and admin operations with their service contracts; make specialized staff landing usable.
3. Add error associations and source-side translation review, then obtain desktop1440/tablet768/mobile375 screenshots with LTR/RTL and reduced motion.

## Should the next attempt REFINE or PIVOT?
REFINE. The direction is coherent and its native implementation matches the brief. The blocking work is contract correctness and verified execution, not a new art direction. This evaluation must not be presented as visual approval or WCAG conformance.
