# Operations and recovery

## Local processes

Serve only public/. The public interface needs no Node build. Filament assets are built by the installed package and published with artisan filament:assets. Re-publish after dependency changes. Do not deploy a PHP development server as production hosting.

~~~powershell
rtk proxy php artisan platform:health
rtk proxy php artisan queue:work --tries=3
rtk proxy php artisan schedule:work
~~~

The local queue driver is database. JSON/HTML export jobs contain IDs, use private storage and recheck live access. Retry backoff is10/60/300 seconds, maximum3 attempts. Reports stay available if rendering fails. Export retry is authorized under Operations. A run lock serializes export and erasure, including artifact cleanup.

SQLite uses WAL and a five-second busy timeout. Transactional writes reserve the writer before reading business state. On PHP8.2/8.3 an application connection adapter first updates the mutex row inside PDO's tracked transaction; PHP8.4+ uses the framework's native BEGIN IMMEDIATE. Run migrations before concurrent workers. SQLite remains a single-writer local runtime; the passing two-process overlap test is not a production throughput or PostgreSQL qualification.

For production use a supervised worker, scheduler invocation, TLS, correct APP_URL and secure cookies. Configure APP_ENV=production, APP_DEBUG=false, SESSION_SECURE_COOKIE=true and WAYPOINT_SOURCE_PREVIEW=false. Supply an approved retention policy, reviewed language/content releases and real approved business data before publication. Production database/hosting/mail choices are not provisioned by this task.

## Staff and secrets

~~~powershell
rtk proxy php artisan platform:bootstrap-admin
rtk proxy php artisan platform:reset-password staff@example.invalid
~~~

Passwords are entered privately and never seeded. Authenticator setup is mandatory for real staff bootstrap. Password changes invalidate existing authenticated session hashes on the next request. Disabled staff, changed grants, ownership and suspended/deleted runs are checked server-side. Temporary browser-QA credentials belong only to a synthetic local account; they must be retired after verification.

The task's temporary QA staff account has been disabled, stripped of grants, given a new unretained random password, and cleared of MFA secret/recovery material. Audit references remain. No enabled default staff account is provided; use interactive bootstrap for an authorized operator.

Keep .env, APP_KEY, private database/backups, session files and .build outside public web access and source control. APP_KEY is needed to recover encrypted MFA/session/temporary credential material; store it separately under the chosen secret-management policy. Do not rotate it casually or put a real key in phpunit.xml. PHPUnit forces an isolated in-memory database and a known test-only key.

## Backups and restore

~~~powershell
rtk proxy php artisan platform:backup --verify-restore
~~~

SQLite backup uses VACUUM INTO a new private file, checks integrity, copies into a separately named restore file without overwriting existing data, and compares row counts/hash. The exercise was actually run locally. This is database recovery evidence, not a production RTO/RPO claim.

Backups are under storage/app/private/backups; restored exercise copies are under its restores subdirectory. A full production procedure must also protect APP_KEY/environment, deployable code/lockfiles, and any non-regenerable private attachments. Current generated exports can be regenerated from immutable reports. Access, encryption, retention, offsite copies and production restore authorization remain operator decisions.

For an actual recovery: stop writes/queue workers; preserve the damaged database; validate a new restored copy; configure the application to use the verified copy; verify integrity and representative authorized reports; restart workers; reconcile jobs. Never overwrite a live database merely to run an exercise. Non-SQLite drivers use their reviewed native backup tools and require separate qualification.

## Retention and requests

~~~powershell
rtk proxy php artisan platform:prune
rtk proxy php artisan platform:prune --apply
~~~

The first is a dry run. --apply removes expired technical idempotency cache entries; personal-data erasure remains blocked until RETENTION_POLICY_APPROVED=true. Anonymous expiry currently uses a proposed7-day limit. Verified privacy requests can be fulfilled by a privacy operator; that removes answers/history/private artifacts and associated scenario evidence while keeping minimal tombstone/audit identity as configured. Backup expiry and jurisdiction-specific exceptions require policy approval; they are not inferred from language.

## Troubleshooting

- Repeated host warning 'Module openssl is already loaded' comes from the existing PHP installation. No global ini was modified. It did not fail the application tests.
- Direct Packagist TLS timed out during installation. The workspace used copied existing cache metadata/archives plus reachable upstream GitHub artifacts; no TLS verification was disabled. The committed lock is portable and contains no local repository requirement. Fresh dependency/advisory fetch needs working upstream access.
- Missing/stale locale text: synchronize keys, inspect source hash, review the target and review/publish the exact new manifest. Preview is local/test only.
- Incomplete price: verify context/currency, approved validity, scoped package conditions, rates, missing evidence and capacity. Do not replace unknown with zero.
-412: reload and compare the current revision; do not overwrite another saved change.409 with an operation key: reuse the exact payload or generate a new key for a genuinely new intent.
- Existing report differs from current settings: inspect its release/book/input versions. New settings and rates intentionally do not rewrite history.
