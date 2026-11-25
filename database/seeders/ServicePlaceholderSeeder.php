<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServicePlaceholderSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            ['Legal Consultation', 'legal-consultation', 'Professional legal guidance and assessment of legal concerns to help clients understand their rights, options, and possible courses of action.', 'scale'],
            ['Legal Document Preparation', 'legal-document-preparation', 'Assistance in preparing, reviewing, and organizing legal documents, agreements, and other relevant paperwork.', 'document'],
            ['Family and Civil Matters', 'family-and-civil-matters', 'Legal assistance concerning family relationships, marriage-related matters, child custody, civil obligations, and related concerns.', 'family'],
            ['Property and Land Matters', 'property-and-land-matters', 'Assistance with property ownership, land titles, property transactions, and related legal disputes.', 'property'],
            ['Business and Contract Law', 'business-and-contract-law', 'Legal assistance involving business agreements, contractual obligations, and commercial transactions.', 'business'],
            ['Labor and Employment Matters', 'labor-and-employment-matters', 'Guidance concerning employment rights, workplace concerns, labor disputes, and employer-employee relationships.', 'labor'],
            ['Criminal Law Matters', 'criminal-law-matters', 'Legal assistance and representation concerning criminal allegations, proceedings, and related legal concerns.', 'shield'],
            ['Estate and Inheritance Matters', 'estate-and-inheritance-matters', 'Assistance with wills, succession, estate settlement, and inheritance-related concerns.', 'estate'],
        ];

        foreach ($samples as $order => [$title, $slug, $description, $icon]) {
            Service::query()->firstOrCreate(
                ['slug' => $slug],
                [
                    'title' => $title,
                    'summary' => $description,
                    'description' => $description,
                    'icon' => $icon,
                    'sort_order' => $order + 1,
                    'is_published' => false,
                ],
            );
        }
    }
}
