<?php

namespace App\Domain;

final class SoftwareDiscovery
{
    public function derive(array $bank, array $answers): array
    {
        $v = fn ($id) => ($answers[$id]['state'] ?? null) === 'known' ? ($answers[$id]['value'] ?? null) : null;
        $surfaces = $v('Q-SW-SURFACES') ?? [];
        $platforms = $v('Q-SW-MOBILE-PLATFORMS') ?? [];
        $matrix = [];
        $add = function ($id, $name, $q, $quantity = null, $unit = null, $surface = null) use (&$matrix, $surfaces) {
            $matrix[$id] = ['id' => $id, 'name' => $name, 'quantity' => $quantity, 'unit' => $unit, 'surfaces' => $surface ?? $surfaces, 'phase' => 'to_confirm', 'sources' => [$q], 'acceptance' => 'Confirm the supported behavior and acceptance criteria.'];
        };
        if ($surfaces) {
            $add('ITEM-SHARED-FOUNDATION', 'Shared account, data and service foundation', 'Q-SW-SURFACES', 1, 'shared foundation', ['shared']);
        }foreach ($platforms as $p) {
            $add('ITEM-'.strtoupper($p).'-APP', ucfirst($p).' application', 'Q-SW-MOBILE-PLATFORMS', 1, 'application', [$p]);
        }
        $questions = array_column($bank['questions'], null, 'id');
        $labels = array_column($questions['Q-SW-DEVICE-FEATURES']['options'] ?? [], 'label', 'value');
        foreach ($v('Q-SW-DEVICE-FEATURES') ?? [] as $feature) {
            if ($feature !== 'none') {
                $add('ITEM-DEVICE-'.strtoupper($feature), $labels[$feature] ?? $feature, 'Q-SW-DEVICE-FEATURES', 1, 'capability', ['mobile']);
            }
        }
        $domains = ['Q-SW-CONTENT' => ['ITEM-CMS-CONTENT', 'Content and editorial workflow'], 'Q-SW-LEADS' => ['ITEM-LEAD-ROUTE', 'Enquiry routing'], 'Q-SW-COMMERCE-CATALOG' => ['ITEM-COMMERCE-CATALOG', 'Commerce catalog'], 'Q-SW-COMMERCE-CHECKOUT' => ['ITEM-COMMERCE-CHECKOUT', 'Checkout and order workflow'], 'Q-SW-COMMERCE-FULFILLMENT' => ['ITEM-COMMERCE-FULFILLMENT', 'Fulfillment and returns'], 'Q-SW-BOOKING-RESOURCE' => ['ITEM-BOOKING', 'Booking and resource availability'], 'Q-SW-MARKET-PARTIES' => ['ITEM-MARKETPLACE', 'Marketplace party workflows'], 'Q-SW-PORTAL-DETAILS' => ['ITEM-PORTAL', 'Portal and access workflows'], 'Q-SW-MEDIA-DETAILS' => ['ITEM-MEDIA', 'Media workflow'], 'Q-SW-AI-TASK' => ['ITEM-AI', 'Bounded AI capability requiring review'], 'Q-SW-MIGRATION' => ['ITEM-MIGRATION', 'Data migration and reconciliation']];
        foreach ($domains as $q => [$id,$name]) {
            if ($v($q) !== null) {
                $add($id, $name, $q);
            }
        }$content = $v('Q-SW-CONTENT');
        if (isset($matrix['ITEM-CMS-CONTENT']) && is_array($content)) {
            $matrix['ITEM-CMS-CONTENT']['quantity'] = $content['pages_launch'] ?? null;
            $matrix['ITEM-CMS-CONTENT']['unit'] = 'pages at launch';
        }$integrations = $v('Q-SW-INTEGRATIONS');
        foreach (is_array($integrations) ? ($integrations['systems'] ?? []) : [] as $system) {
            $add('ITEM-INTEGRATION-'.substr(hash('sha256', $system), 0, 12), $system, 'Q-SW-INTEGRATIONS', 1, 'integration', ['shared']);
        }$confirm = $v('Q-SW-CONFIRM');
        foreach ($matrix as &$item) {
            if (in_array($item['id'], $confirm['accepted_features'] ?? [], true)) {
                $item['phase'] = 'base';
            } elseif (in_array($item['id'], $confirm['optional_features'] ?? [], true)) {
                $item['phase'] = 'optional';
            }
        }unset($item);
        $constraints = [];
        foreach (['Q-SW-DEADLINE', 'Q-SW-BUDGET', 'Q-SW-OWNERSHIP', 'Q-SW-DISTRIBUTION', 'Q-SW-OFFLINE', 'Q-SW-DEVICE-FEATURES', 'Q-SW-ORGANIZATIONS', 'Q-SW-AUTH', 'Q-SW-INTEGRATIONS', 'Q-SW-REQUESTS', 'Q-SW-TRANSACTIONS', 'Q-SW-AVAILABILITY', 'Q-SW-RECOVERY', 'Q-SW-SENSITIVITY', 'Q-SW-GEOGRAPHY', 'Q-SW-REGULATION', 'Q-SW-TEAM'] as $id) {
            $constraints[] = ['question_id' => $id, 'state' => $answers[$id]['state'] ?? 'unanswered', 'value' => $v($id), 'impact' => 'Validate this constraint against each candidate before committing.'];
        }

        return ['feature_matrix' => array_values($matrix), 'constraints' => $constraints, 'phases' => ['base' => array_values(array_filter($matrix, fn ($i) => $i['phase'] === 'base')), 'optional' => array_values(array_filter($matrix, fn ($i) => $i['phase'] === 'optional')), 'to_confirm' => array_values(array_filter($matrix, fn ($i) => $i['phase'] === 'to_confirm'))], 'rejected_candidates' => [['candidate' => 'Independent microservices', 'reason' => 'No independently justified deployment, team or scaling boundary has been established.'], ['candidate' => 'Infrastructure sizing from registered user count', 'reason' => 'Request mix, measured peaks, recovery and operational constraints are required.']]];
    }
}
