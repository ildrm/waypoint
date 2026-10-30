<?php

namespace App\Domain;

use App\Models\AssessmentRun;
use App\Models\PriceBook;

final class ReportBuilder
{
    public function __construct(private FlowEngine $engine, private Predicate $predicate, private ProjectEstimator $estimator) {}

    public function build(AssessmentRun $run, ?PriceBook $book): array
    {
        $manifest = $run->release->manifest;
        $locale = session('locale', $run->locale);
        $tm = app(TranslationManager::class);
        $bank = $tm->localize($manifest['bank'], $manifest['translations'] ?? [], $locale, (bool) ($manifest['source_preview'] ?? false));
        $copy = fn ($s, $v = []) => $tm->text($s, $manifest, $locale, $v);
        $active = $this->engine->active($bank, $run->answers, $manifest);
        $answers = array_intersect_key($run->answers, array_flip(array_column($active, 'id')));
        $v = fn ($id) => ($answers[$id]['state'] ?? null) === 'known' ? ($answers[$id]['value'] ?? null) : null;
        $unresolved = [];
        foreach ($active as $q) {
            $a = $answers[$q['id']] ?? null;
            if (! $a || ! in_array($a['state'], ['known', 'not_applicable'], true)) {
                $unresolved[] = $q['id'].':not known or not decided';
            } elseif (is_array($a['value']) && ($q['schema']['type'] ?? null) === 'object') {
                foreach ($a['value'] as $key => $value) {
                    if ($value === null && empty($q['schema']['properties'][$key]['x-null-meaning'])) {
                        $unresolved[] = $q['id'].'.'.$key.':unknown';
                    }
                }
            }
        }
        foreach ($this->engine->pending($bank, $run->answers, $manifest) as $q) {
            $unresolved[] = $q['id'].':applicability pending';
        }
        $conflicts = $this->engine->conflicts($answers);
        foreach ($conflicts as $c) {
            $unresolved[] = $c['message'];
        }
        $out = ['workflow' => $bank['workflow_id'], 'locale' => ($manifest['source_preview'] ?? false) ? 'en' : $locale, 'flow' => $bank['name'] ?? $run->flow->name, 'summary' => 'A preliminary result based on the answers and listed assumptions.', 'status' => $unresolved ? 'partial' : 'preliminary', 'findings' => [], 'priorities' => [], 'scope' => [], 'architecture' => null, 'effort' => null, 'price' => null, 'schedule' => null, 'sources' => [], 'unresolved' => $unresolved, 'answers' => [], 'versions' => ['release' => $run->release->version, 'release_hash' => $run->release->hash, 'input_revision' => $run->revision, 'method' => '1.0', 'source_preview' => $manifest['source_preview'] ?? false], 'limitations' => ['Self-reported evidence is not independently validated.', 'An estimate is not a contract,guarantee or payment authorization.', 'Platform use and exports are free.']];
        foreach ($active as $q) {
            $out['answers'][] = ['question_id' => $q['id'], 'question' => $q['wording'], 'answer' => $answers[$q['id']] ?? ['state' => 'unanswered', 'value' => null]];
        }
        $workflow = $bank['workflow_id'];
        $effects = app(RuleEffects::class)->run($manifest['rules'] ?? [], $answers, $this->engine->context($answers, $manifest));
        $out['derived_facts'] = $effects['derived'];
        $out['classifications'] = $effects['classifications'];
        $out['scope'] = array_merge($out['scope'], $effects['scope']);
        foreach ($effects['recommendations'] as $recommendation) {
            $out['findings'][] = ['text' => $recommendation['text'], 'sources' => $recommendation['sources'], 'rule_id' => $recommendation['rule_id']];
        }foreach ($effects['validation'] as $finding) {
            $out['unresolved'][] = $finding['message'];
        }$out['next_action'] = 'Review the findings and unresolved evidence before making a commitment.';
        if ($workflow === 'WF-STARTUP') {
            $out['summary'] = 'Your next useful work follows the strongest evidence gaps and known blockers,not a universal success score.';
            foreach (['Q-ST-15' => 'Resolve critical feasibility obstacles before expansion.', 'Q-ST-16' => 'Resolve the known operating prerequisite before affected activity.'] as $id => $text) {
                if ($v($id) === 'D') {
                    $out['priorities'][] = ['title' => $text, 'sources' => [$id], 'rule_id' => 'RULE-ST-BLOCKER', 'severity' => 'blocker'];
                }
            }
            if (in_array($v('Q-ST-C03'), ['B', 'C'], true)) {
                $out['priorities'][] = ['title' => 'Triage committed cash payments before further expansion.', 'sources' => ['Q-ST-22', 'Q-ST-C03'], 'severity' => 'blocker'];
            }
            $tasks = ['Q-ST-03' => 'Define the initial segment,user,buyer and decision maker.', 'Q-ST-04' => 'Observe recent customer problems and consequences.', 'Q-ST-07' => 'Measure a customer outcome in an external pilot.', 'Q-ST-10' => 'Test the normal price with independent prospects;interest is not payment.', 'Q-ST-11' => 'Distinguish delivered purchases from deposits and unpaid interest.', 'Q-ST-12' => 'Record an experiment hypothesis,measure and decision rule before testing.', 'Q-ST-13' => 'Assign a capable owner to the next milestone.', 'Q-ST-21' => 'Calculate contribution including variable delivery/support costs.', 'Q-ST-22' => 'Prepare a cash forecast for commitments and the next milestone.', 'Q-ST-29' => 'Identify a fallback for the consequential dependency.'];
            foreach ($tasks as $id => $text) {
                if (! in_array($v($id), in_array($id, ['Q-ST-04', 'Q-ST-11', 'Q-ST-22'], true) ? ['A', 'B'] : ($id === 'Q-ST-29' ? ['A', 'E'] : ['A']), true)) {
                    $out['priorities'][] = ['title' => $text, 'sources' => [$id], 'rule_id' => 'RULE-ST-'.substr($id, 5), 'severity' => 'evidence_needed'];
                } else {
                    $out['findings'][] = ['text' => $copy('Reported evidence for {question_id}: {answer_value}.', ['question_id' => $id, 'answer_value' => $v($id)]), 'sources' => [$id], 'evidence' => 'self_reported'];
                }
            }
            if ($v('Q-ST-C01') === 'C') {
                $out['findings'][] = ['text' => 'A naturally once-only purchase does not require repeat-purchase retention.', 'sources' => ['Q-ST-09', 'Q-ST-C01'], 'rule_id' => 'RULE-ST-RETENTION'];
            }
            foreach (['Q-ST-14', 'Q-ST-17', 'Q-ST-18', 'Q-ST-19', 'Q-ST-20', 'Q-ST-23', 'Q-ST-24', 'Q-ST-25', 'Q-ST-26', 'Q-ST-27', 'Q-ST-28', 'Q-ST-30'] as $id) {
                if ($v($id) !== null) {
                    $out['findings'][] = ['text' => $copy('This context informs the action review: {question_id}.', ['question_id' => $id]), 'value' => $v($id), 'sources' => [$id]];
                }
            }
            $limit = $manifest['settings']['PARAM-STARTUP-PRIORITIES']['value'] ?? 3;
            $out['priorities'] = array_slice($out['priorities'], 0, max(1, (int) $limit));
            $out['next_action'] = 'Define a small evidence-building experiment with owner,measure,decision rule and review date.';
        } elseif ($workflow === 'WF-SOFTWARE') {
            $surfaces = $v('Q-SW-SURFACES') ?? [];
            $kinds = $v('Q-SW-WEB-KINDS') ?? [];
            foreach ($surfaces as $surface) {
                $out['scope'][] = ['id' => 'surface.'.$surface, 'name' => $copy('Surface: {surface}.', ['surface' => $surface]), 'sources' => ['Q-SW-SURFACES']];
            }foreach ($kinds as $kind) {
                $out['scope'][] = ['id' => 'domain.'.$kind, 'name' => $copy('Domain: {domain}.', ['domain' => $kind]), 'sources' => ['Q-SW-WEB-KINDS']];
            }
            $mobile = in_array('mobile', $surfaces, true);
            $alternatives = ['Compare a configured hosted/CMS product against custom behavior,ownership and integration needs.'];
            $recommendation = in_array('content', $kinds, true) && count($kinds) === 1 && ! $mobile ? 'Compare standard managed/self-hosted CMS with server-rendered pages;custom build only if capability constraints fail.' : 'Compare a custom modular application with suitable hosted/configured alternatives;validate exact feature and integration fit.';
            if ($mobile) {
                $recommendation .= ' Share account/API/data foundation across surfaces and add Android/iOS-specific device,offline and QA work once per required capability.';
                $alternatives[] = 'Compare native,cross-platform and PWA against actual distribution,offline and device constraints;budget alone is insufficient.';
            }
            if ($v('Q-SW-ORGANIZATIONS') === 'isolated') {
                $recommendation .= ' Enforce evaluated-project organization isolation and tenant-scoped data lifecycle.';
            }
            $transactions = $v('Q-SW-TRANSACTIONS');
            if (is_array($transactions) && (($transactions['realtime_connections'] ?? 0) > 0 || ($transactions['messages_sec'] ?? 0) > 0)) {
                $recommendation .= ' Capacity-plan persistent channels/fanout independently from HTTP and registrations.';
                $alternatives[] = 'Compare managed realtime and independently scaled gateway only when workload and ownership justify it.';
            }
            $out['architecture'] = ['recommendation' => $recommendation, 'alternatives' => $alternatives, 'validation' => ['Verify external API documentation,sandbox,limits and reconciliation.', 'Validate forecast operations,data growth,recovery and jurisdiction with specialists.', 'Platform PHP constraint does not restrict customer-project technology.'], 'sources' => ['Q-SW-SURFACES', 'Q-SW-WEB-KINDS', 'Q-SW-INTEGRATIONS', 'Q-SW-REQUESTS', 'Q-SW-TRANSACTIONS', 'Q-SW-RECOVERY']];
            $discovery = app(SoftwareDiscovery::class)->derive($bank, $answers);
            $out['feature_matrix'] = $discovery['feature_matrix'];
            $out['scope'] = array_merge($out['scope'], $discovery['feature_matrix']);
            $out['architecture']['constraints'] = $discovery['constraints'];
            $out['architecture']['rejected_candidates'] = $discovery['rejected_candidates'];
            $out['phases'] = $discovery['phases'];
            $settings = ['pricing_eligibility' => $effects['pricing_eligibility'], 'pricing_pending' => $effects['pricing_pending']];
            foreach (['PARAM-WEEK-HOURS' => 'week_hours', 'PARAM-CONTINGENCY' => 'contingency', 'PARAM-DISCOUNT' => 'discount'] as $id => $field) {
                if (isset($manifest['settings'][$id]['value'])) {
                    $settings[$field] = $manifest['settings'][$id]['value'];
                }
            }$team = $v('Q-SW-AVAILABILITY-TEAM');
            if (isset($team['capacity']) && is_array($team['capacity'])) {
                $settings['capacity'] = app(CapacityInput::class)->parse($team['capacity']);
            }
            $estimate = $this->estimator->estimate($answers, $book, $settings);
            $out['hard_constraint_conflicts'] = app(ConstraintReview::class)->evaluate($answers, $estimate);
            foreach ($out['hard_constraint_conflicts'] as $conflict) {
                $out['unresolved'][] = $conflict['message'];
            }
            foreach (['effort', 'price', 'schedule'] as $field) {
                $out[$field] = $estimate[$field];
            }$out['unresolved'] = array_values(array_unique(array_merge($out['unresolved'], $estimate['unresolved'])));
            $out['sources'] = $estimate['trace'] ?? [];
            $out['versions']['price_book'] = $book?->version;
            $out['next_action'] = 'Confirm scope and material assumptions,then validate integration/workload constraints before commissioning.';
            $internal = $estimate['internal'];
        } elseif ($workflow === 'WF-MATCH') {
            $eligible = [];
            $excluded = [];
            $undetermined = false;
            foreach ($manifest['catalog'] ?? [] as $item) {
                if ($item['domain'] !== $v('Q-MA-GOAL')) {
                    continue;
                }$hit = $this->predicate->evaluate($item['eligibility'] ?: ['always' => true], $answers);
                if ($hit === true) {
                    $item['eligibility_sources'] = $this->predicate->references($item['eligibility']);
                    $eligible[] = $item;
                } else {
                    $undetermined = $undetermined || $hit === null;
                    $excluded[] = ['id' => $item['stable_id'], 'reason' => $hit === null ? 'Prerequisite evidence unresolved.' : 'Mandatory prerequisite or constraint not met.'];
                }
            }
            $priority = $v('Q-MA-PRIORITY');
            $rankable = $priority !== null;
            foreach ($eligible as $item) {
                if (! isset($item['preferences'][$priority]) || ! is_numeric($item['preferences'][$priority])) {
                    $rankable = false;
                }
            }if ($rankable) {
                usort($eligible, fn ($a, $b) => $a['preferences'][$priority] <=> $b['preferences'][$priority]);
            } elseif ($eligible) {
                $out['unresolved'][] = 'A preference and comparable catalog evidence are required before ranking eligible alternatives.';
            }
            $out['matching'] = ['status' => $eligible ? ($rankable ? 'matched' : 'manual_review') : ($v('Q-MA-GOAL') === null ? 'insufficient_evidence' : ($undetermined ? 'manual_review' : 'no_match')), 'eligible' => array_map(fn ($i) => array_intersect_key($i, array_flip(['stable_id', 'name', 'description', 'deliverables', 'dimensions', 'version', 'eligibility_sources'])), $eligible), 'excluded' => $excluded, 'tie_policy' => 'Equal preference metrics retain all eligible alternatives;commercial margin never ranks.'];
            $out['summary'] = $eligible ? 'Eligible alternatives match the supplied prerequisites and constraints.' : 'No approved eligible catalog option can be recommended from this context.';
            $out['next_action'] = 'Review eligibility evidence or ask the catalog owner to assess a supported alternative.';
        }
        $out['stage_summary'] = [];
        foreach ($active as $q) {
            $a = $answers[$q['id']] ?? ['state' => 'unanswered', 'value' => null];
            $labels = array_column($q['options'] ?? [], 'label', 'value');
            $display = $a['state'] === 'known' ? (is_scalar($a['value']) ? ($labels[$a['value']] ?? $a['value']) : $a['value']) : $a['state'];
            $out['stage_summary'][$q['stage']][] = ['question_id' => $q['id'], 'purpose' => $q['purpose'], 'state' => $a['state'], 'evidence' => $display];
        }foreach ($out['answers'] as &$row) {
            $q = current(array_filter($active, fn ($q) => $q['id'] === $row['question_id']));
            $labels = array_column($q['options'] ?? [], 'label', 'value');
            $value = $row['answer']['value'] ?? null;
            if (is_scalar($value)) {
                $row['answer']['display_value'] = $labels[$value] ?? $value;
            } elseif (is_array($value) && array_is_list($value)) {
                $row['answer']['display_value'] = array_map(fn ($v) => is_scalar($v) ? ($labels[$v] ?? $v) : $v, $value);
            }
        }unset($row);
        $out['action_plan'] = array_map(fn ($p) => ['action' => $p['title'], 'owner_role' => 'Venture decision owner', 'hypothesis' => 'State the context-specific assumption before testing.', 'measure' => 'Choose an observable customer or operating outcome and record its denominator/period.', 'decision_rule' => 'Set a context-specific continue/change/stop threshold before collecting results.', 'review_trigger' => 'Review at the agreed date or when a material prerequisite changes.', 'target' => null, 'review_date' => null, 'sources' => $p['sources']], $out['priorities']);
        foreach ($manifest['rules'] ?? [] as $rule) {
            $hit = $this->predicate->evaluate($rule['when'], $answers, $this->engine->context($answers, $manifest));
            $source = $this->predicate->references($rule['when']);
            $out['sources'][] = ['rule_id' => $rule['id'], 'matched' => $hit, 'sources' => $source];
            if ($hit === true) {
                $effect = $rule['effect'];
                if (isset($effect['message'])) {
                    $out['findings'][] = ['text' => $effect['message'], 'sources' => $source, 'rule_id' => $rule['id']];
                }if (($effect['manual_review'] ?? false) === true) {
                    $out['status'] = 'manual_review';
                }
            }
        }
        if (! empty($out['hard_constraint_conflicts'])) {
            $out['status'] = 'manual_review';
        } elseif (! $effects['completion']) {
            $out['status'] = 'manual_review';
        } elseif ($out['unresolved']) {
            $out['status'] = 'partial';
        }$catalog = SourceCopy::keys();
        $translate = function ($value) use (&$translate, $catalog, $copy) {
            if (is_array($value)) {
                $out = [];
                foreach ($value as $key => $child) {
                    $out[$key] = in_array($key, ['value', 'evidence', 'answers', 'sources', 'versions', 'question_id', 'id'], true) ? $child : $translate($child);
                }

                return $out;
            }if (is_string($value) && isset($catalog[SourceCopy::key($value)])) {
                return $copy($value);
            }

            return $value;
        };
        $out = $translate($out);

        return ['public' => $out, 'internal' => $internal ?? []];
    }
}
