# Shared question model

Owner: R06. Reviewers: R05, R14, R27. Status: proposed. Sources: execution prompt, startup source. IDs: WF-STARTUP, WF-SOFTWARE, WF-MATCH; AC-02, AC-06, AC-09.
Practice profile: [skill registry](../00-governance/skill-registry.md). Unresolved: locale registry and empirical questionnaire validation.

## Canonical answer envelope

Each question answer is {question_id, state, value, revision, observed_at, context_id, provenance}. state is known, unknown, not_decided, not_applicable, unanswered or inactive. known requires value conforming to type/schema. Other states have null value and reason; not_applicable requires an applicability proof; inactive is a retained historical answer excluded from current facts, charges and report conclusions. Unknown is never false or zero. not_yet_observable is an allowed reason under unknown, distinct in reports.

question-bank.yaml files use JSON syntax, which is valid YAML 1.2. They are documentation contracts, not executable rules. IDs and enum values are immutable; all labels are translation keys. A semantic key rename requires a migration proposal. Labels in these drafts are English source copy, with no linguistic approval.

## Normative field inheritance

Every question object contains id, semantic_key, stage, purpose, wording, help, glossary_term, input_type, schema/options, requiredness, applicability, default, answer_states, unit/period/range, next, dependencies, contradictions, derived_facts, consumers, effects, translation_keys, sensitivity, acceptance. No substantive default exists. All applicable questions accept unknown; a user can request a partial report. not_decided applies to preferences; not_applicable only if predicates prove it. Inputs are length-bounded and server validated; units persist canonically while presentation is localized.

Select_one stores one canonical option value from the question schema; Select_many stores a bounded array of those values. OPT-* IDs identify versioned option records and map one-to-one to canonical values within a question. The UI may use OPT-* IDs internally, but answer.value and predicate literals use the canonical value (for example, web or A), never translated labels. None is exclusive with other options. Record questions collect one logical concept through a labeled refinement panel, such as demand numbers with a shared horizon, rather than forcing unrelated concepts into one screen. Every nested field has a schema and definition. A record may contain known fields and unknown fields; missing is never inferred zero. Numeric values use exact decimal strings for money, integer counts, ISO 8601 dates, BCP 47 locale strings; no float money. All dates carry calendar/time-zone context. Counts are nonnegative integers. Percentages are decimals in [0,100], not ambiguous bands. Text maximum 2000 code points unless a narrower schema states otherwise.

## Presentation and adaptation

Default one question per screen, source wording and help below label, 3–6 options where suitable. Bounded feature families may exceed six items through progressive groups. A selected band never authorizes server sizing. Numeric refinements define units, observation window and horizon. Show Back, Save and exit, I do not know, and a review screen; never show evaluative per-answer scoring.

A stage is complete when its active questions are known or explicitly deferred. Progress is completed active questions / current active questions, accompanied by a warning that follow-ups may change the total. Branch recalculation occurs after a committed answer revision. Candidate hidden answers become inactive, derived facts are invalidated transitively, and estimates become revised-input. Explain affected scope and charges before recalculation. A later reactivated answer requires reconfirmation.

## Rule semantics

Predicates are declarative documentation: always; contains(question_id, value); eq(question_id, value); any/all; unknown(question_id); conflict(rule_id). Unknown evaluates to indeterminate, never false; ask clarification or include a disclosed assumption. Applicability references declared dependencies and is evaluated in their topological order. Bank array order is editorial order, not proof that prerequisites have already been answered. Conditional insertion schedules dependencies before their consumers: startup C01 follows09 and precedes23. Named conflict steps are evaluated after their required evidence at review. Cycles and unavailable prerequisites cannot be bypassed by treating indeterminate as false. Selection of another applicable question always returns to the next unanswered active question; no unbounded loop. One conflict clarification pass can produce unresolved/manual-review rather than trapping the user.

Question options are identifiers OPT-<question suffix>-<canonical value>. These are emitted explicitly in each bank. Published rule validation checks undefined question/option/parameter references, cycles, coverage and contradictory exclusive outputs. Outcomes carry supporting answer revisions and rule IDs. Navigation, classification, narrative and pricing are separate categories. No admin expression may execute arbitrary code.

## Validation and limits

AC-02: changing mobile need from both to none deactivates device/offline facts and removes incremental mobile work, preserving foundation work once.
AC-06: same canonical answers/context/release across locales produce identical structured outputs.
AC-09: unknown budget yields a scoped preliminary recommendation and incomplete commercial feasibility, not a zero budget.
Per-question consumers and coverage make unnecessary questions removable. Draft question banks require cognitive interviews and accessibility/usability evaluation before production, rather than claiming measurement validity.


API mapping: canonical question_id maps to wire questionId; states known/unknown/not_decided/not_applicable are accepted client assertions subject to schema/applicability. unanswered means absent; inactive is server-derived metadata and cannot be forged by client. Save uses strong If-Match (missing428, stale412); idempotency-key payload conflict409. All material unknowns remain explicit. Session status vocabulary is active/paused/reviewable/completed/suspended/expired/deleted; partial is result completeness, not a separate run status.


Record completeness: known means at least one applicable record field is known, not that every nullable subfield is resolved. A null subfield projects an unknown fact and cannot contribute zero/no evidence, unless its schema documents a substantive absence meaning. In the deadline record, mandatory_date=null explicitly means no mandatory deadline and preferred_date=null means no preferred date. A wholly unresolved record uses state=unknown. Display copy such as “unknown” in narrative text never acts as a business-rule token; typed normalized facts and explicit unresolved items drive conclusions. Report completeness checks must inspect material subfields, not only the envelope state.
