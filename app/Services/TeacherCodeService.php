<?php

namespace App\Services;

use App\Models\CampusTeacher;

class TeacherCodeService
{
    /**
     * Generate an amicable teacher code based on name initials + surnames
     */
    public function generateAmicableTeacherCode($firstName, $lastName)
    {
        // Normalitzar noms (treure accents, caràcters especials)
        $firstName = $this->normalizeName($firstName);
        $lastName = $this->normalizeName($lastName);
        
        // Separar paraules del cognom
        $surnameWords = explode(' ', $lastName);
        
        // Algoritme: inicial del nom + cognoms fins a 6 caràcters
        $initial = substr(strtoupper($firstName), 0, 1);
        $surnames = '';
        
        foreach ($surnameWords as $word) {
            $word = trim($word);
            if (!empty($word)) {
                $surnames .= substr(strtoupper($word), 0, 1);
            }
        }
        
        // Combinar inicial del nom + cognoms, limitat a 6 caràcters
        $baseCode = $initial . $surnames;
        $baseCode = substr($baseCode, 0, 6);
        
        \Log::info("Generated base code: " . $baseCode);
        
        // Provar si existeix
        if (!$this->codeExists($baseCode)) {
            \Log::info("Selected code: " . $baseCode);
            return $baseCode;
        }
        
        // Si existeix, afegir sufix numèric 1...9,10
        \Log::info("Code exists, generating with suffix for base: " . $baseCode);
        return $this->generateWithSuffix($baseCode);
    }
    
    /**
     * Check if teacher code already exists
     */
    private function codeExists($code)
    {
        return CampusTeacher::where('teacher_code', $code)->exists();
    }
    
    /**
     * Generate code with numeric suffix (1...9,10)
     */
    private function generateWithSuffix($baseCode)
    {
        $suffix = 1;
        do {
            if ($suffix <= 9) {
                $code = $baseCode . $suffix;
            } else {
                $code = $baseCode . '10';
            }
            $suffix++;
        } while ($this->codeExists($code));
        
        return $code;
    }
    
    /**
     * Normalize name (remove accents, special characters)
     */
    private function normalizeName($name)
    {
        // Convertir a majúscules
        $name = strtoupper($name);
        
        // Reemplaçar caràcters especials
        $replacements = [
            'Á' => 'A', 'À' => 'A', 'Ä' => 'A',
            'É' => 'E', 'È' => 'E', 'Ë' => 'E',
            'Í' => 'I', 'Ì' => 'I', 'Ï' => 'I',
            'Ó' => 'O', 'Ò' => 'O', 'Ö' => 'O',
            'Ú' => 'U', 'Ù' => 'U', 'Ü' => 'U',
            'Ñ' => 'N', 'Ç' => 'C',
            '´' => '', '`' => '', '^' => '',
            '~' => '', '¨' => '', "'" => '',
        ];
        
        $name = strtr($name, $replacements);
        
        // Mantenir només lletres i espais
        $name = preg_replace('/[^A-Z\s]/', '', $name);
        
        // Treure espais extra
        $name = preg_replace('/\s+/', ' ', $name);
        
        return trim($name);
    }
}
