# Permissions and audit

Owner: R23. Reviewers: R24, R32. Status: proposed documentation, 2026-10-29; no human approval.
Sources: [authorized brief](../00-governance/sources/documentation-super-mega-prompt.md) and user clarification (14 named languages; free use; no platform payments). Related IDs: FR-23,27, AC-07. Open decisions: [register](../00-governance/assumptions-and-open-questions.md). Practice: [security-privacy](../00-governance/skills/security-privacy.md).

Capabilities: content_author edits drafts; reviewer approves exact diff; publisher activates; translator assigned locales only; language_reviewer approves own assigned languages subject to separation policy; price_editor drafts catalog; commercial_approver approves thresholds; support reads scoped diagnostics; privacy_operator fulfills verified requests; security_admin manages roles/revocation. No implied global support access. Production privileged roles require MFA and least privilege.

Reject self-approval where policy requires independent publisher/reviewer; small-team exception needs explicit authorized recorded risk decision, not silent bypass. Staff invitation has expiry, role scope and recipient verification; revoke invitations and sessions immediately. Audit fields: event_id, actor, action, resource/version, UTC timestamp, reason, redacted before/after diff, request/correlation ID, outcome. Audit append-only with restricted deletion/retention policy; never store secret tokens or raw answer dumps.
