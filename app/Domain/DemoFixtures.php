<?php

namespace App\Domain;

use App\Models\CatalogItem;
use App\Models\PriceBook;

final class DemoFixtures
{
    public function load(): array
    {
        abort_unless(app()->environment(['local', 'testing']), 403);
        $fixture = json_decode(file_get_contents(base_path('docs/16-examples/C01-informational-website.json')), true, 512, JSON_THROW_ON_ERROR);
        $when = ['all' => [['eq' => ['question_id' => 'Q-SW-SURFACES', 'value' => ['web']]], ['eq' => ['question_id' => 'Q-SW-WEB-KINDS', 'value' => ['content']]], ['eq' => ['question_id' => 'Q-SW-CONTENT', 'path' => 'pages_launch', 'value' => 12]], ['eq' => ['question_id' => 'Q-SW-CONTENT', 'path' => 'types', 'value' => ['page', 'article', 'service']]]]];
        $packages = [];
        foreach ($fixture['outputs']['work_packages'] as $p) {
            $hours = $p['hours'];
            $deps = $p['predecessors'];
            if ($p['role'] === 'QA') {
                $hours = 56;
            }if ($p['role'] === 'OPS') {
                $deps = ['WP-C01-quality'];
            }$packages[] = ['id' => $p['id'], 'name' => $p['id'], 'role_hours' => [$p['role'] => $hours], 'dependencies' => $deps, 'when' => $when, 'source_status' => 'hypothetical documentation fixture'];
        }$packages[] = ['id' => 'WP-C01-post-release-smoke', 'name' => 'Post-release smoke test', 'role_hours' => ['QA' => 8], 'dependencies' => ['WP-C01-release'], 'when' => $when, 'source_status' => 'hypothetical documentation fixture'];
        $book = PriceBook::firstOrCreate(['name' => 'C01 synthetic documentation fixture', 'synthetic' => true], ['version' => 1, 'currency' => 'USD', 'exponent' => 2, 'market' => 'synthetic-demo', 'status' => 'approved', 'rates' => ['BA' => '60.00', 'UX' => '50.00', 'DEV' => '60.00', 'QA' => '45.00', 'OPS' => '45.00'], 'internal_rates' => ['BA' => '35.00', 'UX' => '30.00', 'DEV' => '35.00', 'QA' => '25.00', 'OPS' => '30.00'], 'work_packages' => $packages, 'recurring' => [['interval' => 'month', 'currency' => 'USD', 'amount' => '350.00', 'description' => 'Hypothetical operations budget; no supplier quote.']], 'contingency' => '0.10', 'discount' => '0.05', 'validity_days' => 30, 'effective_from' => now()->subDay()]);
        foreach (['OFFER-DEMO-A', 'OFFER-DEMO-B'] as $id) {
            CatalogItem::firstOrCreate(['stable_id' => $id], ['name' => $id === 'OFFER-DEMO-A' ? 'Synthetic configured-service option' : 'Synthetic custom-service option', 'domain' => 'software', 'description' => 'Demonstration of tied eligible alternatives; no actual service commitment.', 'eligibility' => ['eq' => ['question_id' => 'Q-MA-GOAL', 'value' => 'software']], 'deliverables' => ['Review scope and eligibility evidence'], 'preferences' => ['balanced' => 1], 'dimensions' => ['assessment_status' => 'self_reported_context_only'], 'status' => 'approved', 'synthetic' => true]);
        }

        return ['price_book' => $book->id, 'synthetic' => true];
    }
}
