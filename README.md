# Waypoint

A free PHP application for startup guidance, software discovery, and offering matching. It imports the complete documented banks (40 +75 +8 questions), saves adaptive assessments, and produces explainable, versioned reports. There is no platform checkout, billing, paid entitlement, or payment capture.

The local build is an **English source preview** with14 configured languages and RTL layouts. Production publication requires reviewed critical copy and operating-policy approval. Real estimates and matches additionally require approved rates and catalog data; missing business data produces incomplete or manual-review results. See [handover](docs/implementation/handover.md), [verification](docs/implementation/verification.md), and [acceptance traceability](docs/implementation/traceability.md).

## Run the existing workspace

Dependencies and the local SQLite database have been prepared. Start another development server if needed:

~~~powershell
rtk proxy php artisan serve --host=127.0.0.1 --port=8000
~~~

Open http://127.0.0.1:8000 . The implementation verification server used http://127.0.0.1:8460 . Serve only the public directory; never expose the repository root.

## Clean local setup

Requires PHP8.2+ with bcmath, intl, mbstring, PDO SQLite and the extensions required by Composer; Composer2. No Node build is needed for the public interface. The verified local combination is PHP8.2.12, Laravel12.67.0 and Filament5.7.6. Upgrade the old local PHP patch before any production use.

1. Copy .env.example to .env **only when .env does not exist**. Keep the environment file private.
2. Run these commands. If SQLite asks to create its database, create a new database; do not replace an existing one.

~~~powershell
rtk proxy cmd /c composer install
rtk proxy php artisan key:generate
rtk proxy php artisan migrate --seed
rtk proxy php artisan filament:assets
rtk proxy php artisan serve --host=127.0.0.1 --port=8000
~~~

Keep APP_ENV=local and WAYPOINT_SOURCE_PREVIEW=true for evaluation. Do not overwrite an existing APP_KEY; it protects encrypted staff MFA secrets, sessions and temporary recovery-code responses.

## Administration

There is no default staff password. Bootstrap an authorized local account interactively:

~~~powershell
rtk proxy php artisan platform:bootstrap-admin
~~~

Open /admin. The command asks for a new email/name and a hidden password twice (minimum14 characters). Authenticator MFA enrollment is required. Staff capabilities are enforced on the server; an independent reviewer must approve the author's latest edit before publication/commercial activation. Translator and language-reviewer permissions are separate.

## Verification and operations

~~~powershell
rtk proxy cmd /c composer test
rtk proxy php vendor/bin/pint --test
rtk proxy php artisan platform:health
rtk proxy php artisan queue:work --tries=3
rtk proxy php artisan schedule:work
~~~

The queue and scheduler commands run in separate terminals. Private JSON and printable HTML exports are implemented. PDF requests return an explicit readiness error until a reviewed multiscript renderer/font configuration is supplied.

Optional synthetic examples, restricted to local/test environments:

~~~powershell
rtk proxy php artisan platform:demo
rtk proxy php artisan platform:preview
~~~

The C01 price book and two fictional catalog options were loaded for browser verification in this workspace. They are labeled synthetic and excluded from production selection. They are not actual rates, offers, approvals, or customer commitments.

[Runtime API](docs/implementation/openapi.yaml) · [Authoring and localization](docs/implementation/authoring.md) · [Operations and recovery](docs/implementation/operations.md)
