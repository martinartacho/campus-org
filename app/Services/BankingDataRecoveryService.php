<?php

namespace App\Services;

use App\Models\CampusTeacher;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Encryption\DecryptException;

class BankingDataRecoveryService
{
    /**
     * Diagnose and fix corrupted banking data
     */
    public function diagnoseAndFix(): array
    {
        $results = [
            'total_teachers' => 0,
            'corrupted_ibans' => 0,
            'empty_ibans' => 0,
            'valid_ibans' => 0,
            'fixed_count' => 0,
            'errors' => []
        ];

        try {
            $allTeachers = CampusTeacher::all();
            $results['total_teachers'] = $allTeachers->count();

            foreach ($allTeachers as $teacher) {
                try {
                    $iban = $teacher->iban;
                    
                    if (empty($iban) || strlen($iban) === 0) {
                        $results['empty_ibans']++;
                    } elseif ($this->isCorrupted($iban)) {
                        $results['corrupted_ibans']++;
                        $this->fixCorruptedIban($teacher);
                        $results['fixed_count']++;
                    } else {
                        $results['valid_ibans']++;
                    }
                } catch (DecryptException $e) {
                    $results['corrupted_ibans']++;
                    $results['errors'][] = "Teacher ID {$teacher->id}: " . $e->getMessage();
                    $this->fixCorruptedIban($teacher);
                    $results['fixed_count']++;
                } catch (\Exception $e) {
                    $results['errors'][] = "Teacher ID {$teacher->id}: " . $e->getMessage();
                }
            }

            Log::info('Banking data recovery completed', $results);
            
        } catch (\Exception $e) {
            $results['errors'][] = 'Service error: ' . $e->getMessage();
            Log::error('Banking data recovery service error', ['error' => $e->getMessage()]);
        }

        return $results;
    }

    /**
     * Check if IBAN is corrupted
     */
    private function isCorrupted(string $iban): bool
    {
        // Check for serialized data corruption
        if (strpos($iban, 's:') === 0) {
            return true;
        }

        // Check for encryption corruption patterns
        if (strlen($iban) > 50 && !preg_match('/^[A-Z]{2}\d{2}/', $iban)) {
            return true;
        }

        return false;
    }

    /**
     * Fix corrupted IBAN by setting it to null
     */
    private function fixCorruptedIban(CampusTeacher $teacher): void
    {
        try {
            $oldIban = $teacher->iban;
            $teacher->iban = null;
            $teacher->save();
            
            Log::warning('Fixed corrupted IBAN', [
                'teacher_id' => $teacher->id,
                'user_email' => $teacher->user->email,
                'old_iban_preview' => substr($oldIban, 0, 20) . '...'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fix corrupted IBAN', [
                'teacher_id' => $teacher->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Validate IBAN format
     */
    public function validateIbanFormat(string $iban): array
    {
        $result = [
            'valid' => false,
            'country_code' => '',
            'bank_code' => '',
            'errors' => []
        ];

        // Remove spaces
        $clean = preg_replace('/\s+/', '', $iban);

        // Check basic format
        if (!preg_match('/^[A-Z]{2}\d{2}[A-Z0-9]{1,30}$/', $clean)) {
            $result['errors'][] = 'Formato IBAN inválido';
            return $result;
        }

        $result['country_code'] = substr($clean, 0, 2);
        $result['valid'] = true;

        return $result;
    }

    /**
     * Get banking data status for a teacher
     */
    public function getTeacherBankingStatus(CampusTeacher $teacher): array
    {
        $status = [
            'has_iban' => false,
            'iban_valid' => false,
            'has_bank_titular' => false,
            'has_fiscal_data' => false,
            'completion_percentage' => 0,
            'issues' => []
        ];

        try {
            // Check IBAN
            if (!empty($teacher->iban)) {
                $status['has_iban'] = true;
                $validation = $this->validateIbanFormat($teacher->iban);
                $status['iban_valid'] = $validation['valid'];
                if (!$validation['valid']) {
                    $status['issues'] = array_merge($status['issues'], $validation['errors']);
                }
            } else {
                $status['issues'][] = 'IBAN no proporcionado';
            }

            // Check bank titular
            $status['has_bank_titular'] = !empty($teacher->bank_titular);
            if (!$status['has_bank_titular']) {
                $status['issues'][] = 'Titular del compte no proporcionado';
            }

            // Check fiscal data
            $status['has_fiscal_data'] = !empty($teacher->fiscal_situation);
            if (!$status['has_fiscal_data']) {
                $status['issues'][] = 'Situación fiscal no especificada';
            }

            // Calculate completion percentage
            $fields = ['has_iban', 'has_bank_titular', 'has_fiscal_data'];
            $completed = array_sum(array_map(fn($field) => $status[$field] ? 1 : 0, $fields));
            $status['completion_percentage'] = ($completed / count($fields)) * 100;

        } catch (\Exception $e) {
            $status['issues'][] = 'Error al verificar datos bancarios: ' . $e->getMessage();
            Log::error('Error checking banking status', [
                'teacher_id' => $teacher->id,
                'error' => $e->getMessage()
            ]);
        }

        return $status;
    }
}
