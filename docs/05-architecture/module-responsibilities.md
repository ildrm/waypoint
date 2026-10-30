# Module responsibilities

Owner: R18. Reviewers: R17, R19, R22. Status: proposed. Source: prompt. Related: AC-02, AC-04, AC-08, AC-15. Open: package selection.

| Module | Owns | Dependencies | Prohibition |
|---|---|---|---|
| Identity/access | Users, projects, staff grants, revocation | Audit | Never infer access from old release |
| Definition/publishing | Drafts, manifests, publication gates | Localization, rules, parameters | No released-record edits |
| Session/answers | Runs, revisions, provenance | Access, definitions, facts | No client-authoritative applicability |
| Facts/rules | Derivation, navigation, classification, trace | Pinned definitions | No arbitrary executable rules |
| Recommendation | Scope, architecture candidates/reasons | Facts, catalogs | No authoritative price setting |
| Estimation | Effort, line items, exact arithmetic | Facts, book, schedule | No invented missing rates |
| Schedule/team | Dependency/capacity scenarios | WBS, calendar | No hours/headcount shortcut |
| Reporting | Structured rendering and export | Immutable snapshots | No arbitrary remote assets |
| Operations | Outbox, jobs, diagnostics | Stable events | No raw-answer logging by default |

Controllers and Filament resources validate, authorize and invoke shared domain commands. They do not maintain alternate business algorithms. Background jobs recheck access-sensitive actions. Answer updates, publication pointer changes and estimate snapshots use transactions; PDF generation/network calls stay outside. Cross-module writes use commands; read models may join approved database records. Reporting cannot mutate sessions. Module owners are review responsibilities, not necessarily separate hires or services.
