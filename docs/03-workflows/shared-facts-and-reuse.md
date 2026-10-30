# Shared facts and reuse

Owner: R05. Reviewers: R21, R24. Status: proposed. Sources: prompt. IDs: WF-SESSION, WF-SCENARIO; AC-02, AC-03, AC-07.
Practice profile: [skill registry](../00-governance/skill-registry.md). Open: owner authentication/tenancy decision.

A fact is {semantic_key, canonical_value, state, answer_id, answer_revision, flow_release_id, project_id, offering_id, segment_id, period, observed_at, source, consent_scope}. User-reported, measured/imported and inferred facts stay distinguishable. An inference includes its rule/revision and full input chain. Stale evidence retains history but cannot imply current certainty.

Reuse within one run is automatic only when meaning, unit, period and scope match. Reuse from another project or flow is an explicit preview: identify source project/date, show the proposed values and ask user to confirm each affected group. Authorization and provenance checks run before reading values. Ownership, current access, context and consent failures prohibit reuse. Currency, language, jurisdiction, market, current proficiency, target proficiency, complexity and commercial package are separate facts.

A scenario forks an immutable parent answer revision. Copy unchanged facts with provenance; changes derive new facts and new estimates. Comparisons flag different price books, currencies, periods and quality targets. Do not combine incompatible totals. An answer correction never rewrites a historical report. New derivation supersedes current caches transactionally; older artifacts remain visibly historical subject to retention and access controls.

Correction example: a website+apps answer originally requires offline task editing. A later website-only answer invalidates offline/device/shared-account details, removes mobile incremental WBS, and requests confirmation of revised scope. It does not delete the original audit history or silently preserve its mobile charge.

Freshness is typed by fact family rather than a blanket expiry. A launch date or live integration availability may expire; a historical observed transaction remains historical. Reconfirm stale commercial or delivery constraints before a new estimate. Security revocation is live even when a business release is pinned.
