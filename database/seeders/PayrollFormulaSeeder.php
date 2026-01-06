<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PayrollFormula;
use Illuminate\Support\Facades\DB;

class PayrollFormulaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $formulas = [
            [
                'formula_type' => 'PAYE',
                'name' => 'Pay As You Earn Tax',
                'formula' => 'calculatePAYE(gross_salary)',
                'explanation' => 'Progressive tax brackets: 0% for 0-270,000 TZS, 8% for 270,001-520,000 TZS, 20% for 520,001-760,000 TZS, 25% for 760,001-1,000,000 TZS, 30% for above 1,000,000 TZS. Tax is calculated on each bracket separately.',
                'parameters' => [
                    'brackets' => [
                        ['min' => 0, 'max' => 270000, 'rate' => 0],
                        ['min' => 270000, 'max' => 520000, 'rate' => 0.08],
                        ['min' => 520000, 'max' => 760000, 'rate' => 0.20],
                        ['min' => 760000, 'max' => 1000000, 'rate' => 0.25],
                        ['min' => 1000000, 'max' => PHP_FLOAT_MAX, 'rate' => 0.30],
                    ]
                ],
                'is_locked' => false,
                'is_active' => true,
            ],
            [
                'formula_type' => 'NSSF',
                'name' => 'National Social Security Fund',
                'formula' => 'min(gross_salary, 2000000) * 0.05',
                'explanation' => 'Employee contributes 5% of gross salary, capped at TZS 2,000,000. Employer also contributes 5%. Total NSSF rate is 10% (5% employee + 5% employer).',
                'parameters' => [
                    'employee_rate' => 0.05,
                    'employer_rate' => 0.05,
                    'total_rate' => 0.10,
                    'ceiling' => 2000000,
                ],
                'is_locked' => false,
                'is_active' => true,
            ],
            [
                'formula_type' => 'NHIF',
                'name' => 'National Health Insurance Fund',
                'formula' => 'min(gross_salary, 1000000) * 0.03',
                'explanation' => 'Employee contributes 3% of gross salary, capped at TZS 1,000,000. This is a monthly health insurance contribution.',
                'parameters' => [
                    'rate' => 0.03,
                    'ceiling' => 1000000,
                ],
                'is_locked' => false,
                'is_active' => true,
            ],
            [
                'formula_type' => 'HESLB',
                'name' => 'Higher Education Student Loans Board',
                'formula' => 'has_student_loan ? min(gross_salary, 5000000) * 0.05 : 0',
                'explanation' => 'Employee contributes 5% of gross salary, capped at TZS 5,000,000. Only applicable to employees with student loans (has_student_loan = true).',
                'parameters' => [
                    'rate' => 0.05,
                    'ceiling' => 5000000,
                    'requires_student_loan' => true,
                ],
                'is_locked' => false,
                'is_active' => true,
            ],
            [
                'formula_type' => 'WCF',
                'name' => 'Workers Compensation Fund',
                'formula' => 'gross_salary * 0.01',
                'explanation' => 'Employee contributes 1% of gross salary. This fund provides compensation for work-related injuries and disabilities.',
                'parameters' => [
                    'rate' => 0.01,
                ],
                'is_locked' => false,
                'is_active' => true,
            ],
            [
                'formula_type' => 'SDL',
                'name' => 'Skills Development Levy',
                'formula' => 'gross_salary * 0.035',
                'explanation' => 'Employee contributes 3.5% of gross salary. This levy funds skills development and training programs.',
                'parameters' => [
                    'rate' => 0.035,
                ],
                'is_locked' => false,
                'is_active' => true,
            ],
        ];

        foreach ($formulas as $formula) {
            PayrollFormula::updateOrCreate(
                ['formula_type' => $formula['formula_type']],
                $formula
            );
        }
    }
}
