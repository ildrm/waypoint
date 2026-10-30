# Frontend verification runners

These checks originally ran as PHP -r strings held in the persistent Node tool session. There was no earlier on-disk runner. This directory materializes those exact fixtures and assertions, with explicit nonzero exit codes for failed checks.

The generated runner sources are preserved byte-for-byte and excluded from Pint formatting in the repository configuration. Domain/application/test code remains covered by Pint. The execution evidence records fixture checks, not style or production approval.

After copying these runners into tools/verification/, run each command as a separate process from the repository root. The original temporary execution paths are recorded in invocation.json.

```powershell
rtk proxy php -d display_errors=stderr tools/verification/public-render-contracts.php
rtk proxy php -d display_errors=stderr tools/verification/staff-livewire-contracts.php
```

## Requirements

- The repository's configured Laravel 12 / Filament 5 / Livewire 4 environment and installed Composer dependencies, including testing dependencies.
- The PHP CLI used by this repository, including DOM/libxml and intl; normal application extension requirements also apply.
- The documented question banks at docs/03-workflows/{software,startup,match}/question-bank.yaml. They contain JSON-formatted schemas.
- A bootable test/development application environment. The runners do not migrate or seed a database. Application providers/translation services may read the configured environment.
- No Node, browser, HTTP server, network service or extra package installation is needed to run the saved PHP files.

## Public runner

public-render-contracts.php bootstraps Laravel, uses array-backed sessions, and registers named routes only in its own in-memory router. It creates synthetic, unsaved Flow/AssessmentRun/AssessmentReport models. It renders all 123 bank questions plus public/recovery/error/empty/RTL cases (142 renders), and performs 96 DOM/text assertions. Coverage includes known/unknown/not-decided save-and-exit submitters, native submitter/idempotency form structure and retained retry keys, recovery-code privacy, choice errors, contributor capacity fields and saved/legacy values, matching results, role-hours, report scope/action data, escaped strings, support types/consent, source-copy isolation within RTL layout, legacy/new workflow-specific sections, snapshot-bound export preview, actual report validity and expired historical notices.

It temporarily injects synthetic translation lines to test translated validation text and non-string translation fallback. It verifies that the snapshot output is unchanged by validity rendering. All model content, aliases, UUIDs, recovery strings and example.test email values are synthetic fixtures.

## Staff runner

staff-livewire-contracts.php bootstraps Laravel/Filament and uses a synthetic unsaved enabled User. Its FrontendWorkspaceFixture subclass overrides getViewData() with synthetic rows instead of querying workspace records. It renders all 11 sections through Livewire and performs 33 assertions covering supported operations, source approval metadata, role-filtered navigation, independent reviewer/publisher/language-reviewer/commercial-approver/privacy-operator controls, read-only fields, general parameter metadata, scoped hidden-label/table rules and explicit override zero/null provenance. It calls the real AdminWorkspace::allowed()/canOperate() policy helpers to choose permitted operations.

## Safety and limits

The scripts make no domain-model saves, no migration/seed calls and no external requests. Laravel may write compiled view/framework caches as normal. The session, router, translator additions and fake authentication exist only for each short-lived CLI process. No production credentials or real assessment data are included.

These are rendering/DOM/presentation checks, not browser screenshots, HTTP endpoint authorization tests, persistence tests, scheduler tests, translation approval evidence or a replacement for the repository domain suite. Staff HTTP middleware is bypassed by Livewire testing; server authorization must be verified separately.

Both runners emit a JSON result to stdout and return exit code 1 if an assertion/render check fails. PHP fatal errors also return a failing exit code. display_errors=stderr keeps PHP configuration warnings separate from JSON. The pre-existing duplicated openssl extension warning may appear on stderr.

The saved *.result.json files are captures of executing these saved files. *.stderr.txt captures runtime diagnostics. invocation.json records commands, working directory, counts and exit codes.

## Clarification revision

This revision adds seven public assertions for Q-ST-C07: exact legacy editorial-marker removal by the actual TranslationManager, an optional panel before the answer form, exact source question wording and selected labels, same-run source-question correction links, escaped messages/wording/labels, no substantive answer defaults, and omission of the panel when no clarification data is supplied. The fixture strings containing HTML are intentional escaping probes, not executable assets.

## Transparency revision

This revision adds 21 assertions for configured-only low/base/high effort ranges, exact public allowance/minimum quantities and amounts (including zeros), escaped units, supplied schedule assumptions and calendar-week peak interval, explicit synthetic/nonbinding labeling, immutable output preservation and optional-field omission. C01 checks use docs/16-examples/C01-informational-website.json and verify exact 17600.00 subtotal, 1760.00 contingency, 968.00 discount and 18392.00 total in report and HTML export. The documented internal-cost sentinel is deliberately supplied to the HTML fixture to verify that it remains absent from the rendered result. No calculation, financial rounding or production price is performed by this runner.

Evidence: public-render-contracts.result.json contains 142 successful broad render cases and 96 passing assertions; staff-livewire-contracts.result.json contains 33 passing assertions. invocation.json links the exact executed files, commands, timestamps, working directory and exit codes. runner-sha256.json identifies the saved runner sources. Both stdout captures are parseable JSON; stderr contains only the existing PHP configuration warning when emitted.
