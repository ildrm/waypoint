<?php

namespace Database\Seeders;

use App\Domain\FlowEngine;
use App\Domain\PublicationService;
use App\Domain\SourceCopy;
use App\Domain\TranslationManager;
use App\Models\Flow;
use App\Models\ParameterDefinition;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['startup' => ['Startup guidance', 'Choose practical next experiments from evidence and constraints.'], 'software' => ['Software discovery', 'Explore web, mobile, operations and delivery requirements.'], 'match' => ['Find a suitable option', 'Check eligibility and compare approved alternatives.']] as $slug => [$name,$description]) {
            $bank = json_decode(file_get_contents(base_path('docs/03-workflows/'.$slug.'/question-bank.yaml')), true, 512, JSON_THROW_ON_ERROR);
            if ($slug === 'startup') {
                foreach ($bank['questions'] as $i => &$q) {
                    $q['order'] = $i * 10;
                    if ($q['id'] === 'Q-ST-C01') {
                        $q['order'] = 95;
                    }
                }unset($q);
            }$bank['name'] = $name;
            $bank['description'] = $description;
            app(FlowEngine::class)->validate($bank);
            $flow = Flow::firstOrCreate(['slug' => $slug], ['name' => $name, 'description' => $description, 'bank' => $bank, 'rules' => [], 'question_count' => count($bank['questions'])]);
            app(TranslationManager::class)->sync($flow->id, app(TranslationManager::class)->sourceKeys($flow->bank));
        }
        $definitions = [['PARAM-ESTIMATE-VALIDITY', 'Estimate validity', 'integer', 'days', 30, ['min' => 1, 'max' => 90], true], ['PARAM-WEEK-HOURS', 'FTE reference week', 'decimal', 'hours/person/week', '40', ['min' => 1, 'max' => 60], true], ['PARAM-REPORT-UNKNOWN', 'Unresolved evidence', 'enum', 'state', 'partial_or_review', ['values' => ['partial_or_review']], false], ['PARAM-STARTUP-PRIORITIES', 'Priorities to display', 'integer', 'items', 3, ['min' => 1, 'max' => 5], true], ['PARAM-REUSE-MAX-AGE', 'Reuse confirmation age', 'integer', 'days', 30, ['min' => 0, 'max' => 365], true], ['PARAM-CONTINGENCY', 'Contingency policy default', 'decimal', 'ratio', null, ['min' => 0, 'max' => 1], true], ['PARAM-DISCOUNT', 'Discount policy default', 'decimal', 'ratio', null, ['min' => 0, 'max' => 1], true]];
        foreach ($definitions as [$id,$name,$type,$unit,$value,$constraints,$override]) {
            ParameterDefinition::firstOrCreate(['id' => $id], ['name' => $name, 'type' => $type, 'unit' => $unit, 'value' => $value, 'constraints' => $constraints, 'allow_override' => $override, 'nullable' => $value === null]);
        }app(TranslationManager::class)->sync('ui', SourceCopy::keys());
        if (app()->environment(['local', 'testing']) && config('platform.source_preview')) {
            foreach (Flow::whereNull('active_release_id')->get() as $flow) {
                app(PublicationService::class)->publish($flow, null, true);
            }
        }
    }
}
