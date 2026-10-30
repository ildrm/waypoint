# Authenticated administration — visual appendix

Reviewed2026-10-29. Extends evaluation-attempt-2.md with actual authenticated-workspace captures:
- output/playwright/admin-workspace-1440.png
- output/playwright/admin-workspace-375.png
- output/playwright/admin-general-1440.png

The parent reports that real password/TOTP UI authentication was completed using a temporary synthetic local QA identity. This evaluator inspected the resulting screenshots; it did not independently perform authentication or change administrative data. No production account, credential or real-user review is implied.

## Verdict
The inspected admin surfaces are usable and retain the planning-desk colors in a calmer, denser Filament shell. The overview and editor stack correctly on375px without visible page-wide clipping, and the general-setting editor presents the selected record/revision and a typed value control clearly. The prior unsupported-operation and missing-source-review fixes were already checked in source. Admin craft is currently1/3 because of the two concrete P2 issues below; functionality is at least1/3 within the visually inspected scope. This does not change the2/3 public-screen scores or establish a whole-product production approval.

## P2: Hidden-text utility missing in the Filament workspace
The workspace uses class sr-only for captions, column labels and link context, but that public CSS utility is not available in the Filament shell. Captions such as “Flows & releases” and link context such as “Open Find a suitable option” become visible. The longer link text squeezes the State column so “Preview” wraps as “Previe / w”; even the “State” header and “Draft” labels wrap midword in the desktop general-settings capture.

Suggested fix: add the standard visually-hidden sr-only rule scoped to .wp-admin, and reserve sufficient no-wrap width for the state column. Preserve the accessible link context while reducing the visible link to “Open”. Re-capture both widths to verify the repair.

## P2: General-setting definition/provenance is not visible
The selected general-setting panel shows its name and JSON value but omits the declared type, unit, constraints, nullable/override policy and definition provenance. These already exist on the selected model. Without them the operator has to consult documentation or provoke validation errors to understand valid values.

Suggested fix: show a compact read-only definition list for those fields. For flow overrides, compute the effective value/origin from the selected override's flow_id as well as the optional GET flow_id, so the normal edit/redirect path actually exposes inheritance provenance. The domain's typed-value behavior is not disputed by this visual observation.

## Remaining scope
These captures cover the workspace overview and general-setting editor. Translation/price/release/support editors have static contract evidence but were not individually captured by this evaluator. Server authorization, MFA enforcement and actual mutations require the separately recorded functional/security tests. Priced-report visuals and a refreshed capture of the repaired RTL source-text bidi behavior remain outside this appendix.
