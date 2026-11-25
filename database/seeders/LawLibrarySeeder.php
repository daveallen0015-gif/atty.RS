<?php

namespace Database\Seeders;

use App\Models\Law;
use App\Models\LawCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LawLibrarySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['Constitutional Law', 'The Constitution, government structure, and fundamental rights.'],
            ['Civil Law', 'Civil rights, obligations, contracts, and private legal relations.'],
            ['Criminal Law', 'Offenses, penalties, and criminal justice statutes.'],
            ['Family and Marriage Law', 'Marriage, family relations, child welfare, and protection.'],
            ['Labor and Employment Law', 'Employment standards, worker protection, and workplace rules.'],
            ['Business and Commercial Law', 'Commerce, secured transactions, and business regulation.'],
            ['Property and Real Estate Law', 'Property rights, land, and real estate statutes.'],
            ['Taxation Law', 'Tax statutes and public revenue rules.'],
            ['Human Rights and Social Legislation', 'Human rights protections and social welfare legislation.'],
            ['Special Laws', 'Other Philippine statutes and executive issuances.'],
        ];

        foreach ($categories as [$name, $description]) {
            LawCategory::query()->updateOrCreate(['slug' => Str::slug($name)], [
                'category_name' => $name,
                'description' => $description,
            ]);
        }

        $references = [
            ['Family and Marriage Law', 'Executive Order', '209', 'The Family Code of the Philippines', 'A code covering marriage, family relations, and related civil matters.', '1987-07-06', 'https://lawphil.net/executive/execord/eo1987/eo_209_1987.html'],
            ['Family and Marriage Law', 'Republic Act', '9262', 'Anti-Violence Against Women and Their Children Act of 2004', 'Provides protective measures for victims and prescribes penalties for violence against women and their children.', '2004-03-08', 'https://lawphil.net/statutes/repacts/ra2004/ra_9262_2004.html'],
            ['Family and Marriage Law', 'Republic Act', '11642', 'Domestic Administrative Adoption and Alternative Child Care Act', 'Provides an administrative process for domestic adoption and reorganizes the national child-care authority.', '2022-01-06', 'https://lawphil.net/statutes/repacts/ra2022/ra_11642_2022.html'],
            ['Civil Law', 'Republic Act', '386', 'Civil Code of the Philippines', 'Ordains and institutes the Civil Code of the Philippines.', '1949-06-18', 'https://lawphil.net/statutes/repacts/ra1949/ra_386_1949.html'],
            ['Civil Law', 'Republic Act', '11057', 'Personal Property Security Act', 'Establishes a legal framework for security interests in personal property.', '2018-08-17', 'https://lawphil.net/statutes/repacts/ra2018/ra_11057_2018.html'],
            ['Criminal Law', 'Act', '3815', 'Revised Penal Code', 'Revises the penal code and other penal laws.', '1930-12-08', 'https://lawphil.net/statutes/acts/act1930/act_3815_1930.html'],
            ['Criminal Law', 'Republic Act', '9165', 'Comprehensive Dangerous Drugs Act of 2002', 'Institutes the Comprehensive Dangerous Drugs Act of 2002.', '2002-06-07', 'https://lawphil.net/statutes/repacts/ra2002/ra_9165_2002.html'],
            ['Human Rights and Social Legislation', 'Republic Act', '11313', 'Safe Spaces Act', 'Defines gender-based sexual harassment in public spaces, online, workplaces, and educational institutions.', '2019-04-17', 'https://lawphil.net/statutes/repacts/ra2019/ra_11313_2019.html'],
            ['Labor and Employment Law', 'Presidential Decree', '442', 'Labor Code of the Philippines', 'Institutes a Labor Code revising and consolidating labor and social laws.', '1974-05-01', 'https://lawphil.net/statutes/presdecs/pd1974/pd_442_1974.html'],
            ['Labor and Employment Law', 'Republic Act', '11058', 'An Act Strengthening Compliance with Occupational Safety and Health Standards and Providing Penalties for Violations Thereof', 'Strengthens compliance with occupational safety and health standards and provides penalties for violations.', '2018-08-17', 'https://lawphil.net/statutes/repacts/ra2018/ra_11058_2018.html'],
        ];

        foreach ($references as [$category, $type, $number, $title, $summary, $date, $url]) {
            $categoryId = LawCategory::query()->where('category_name', $category)->value('id');
            Law::query()->updateOrCreate(['slug' => Str::slug($title)], [
                'law_category_id' => $categoryId,
                'law_type' => $type,
                'law_number' => $number,
                'official_title' => $title,
                'short_description' => $summary,
                'full_text' => null,
                'date_approved' => $date,
                'official_source_url' => $url,
                'legal_status' => null,
                'is_published' => true,
            ]);
        }
    }
}
