# ADR-002: Immutable manifests and snapshots

Owner: R21. Reviewers: R05, R29. Status: proposed. Source: prompt invariants. Related: AC-03, AC-04, AC-06. Open: retention.

Context: content, translations and prices change independently; history must remain explainable while access revocation stays live.

Proposal: runs pin a business manifest; calculations snapshot the approved applicable commercial book; reports pin answers, estimate, template and locale revisions. Explicit successors expose deltas. Permissions/erasure always use current policy.

Rejected: reading latest configuration during resume causes unexplained output changes; permanent run-start prices obscure expiry policy; pinned access permits revoked users. Snapshots cost storage and require redaction design. Revisit for retention law or critical defects. Acceptance: changing rates/copy preserves old identities; explicit reprice creates a new estimate; revoked access fails.
