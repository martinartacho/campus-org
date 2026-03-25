<?php

namespace App\Services;

use App\Models\CampusTeacher;

class TeacherCodeService
{
    /**
     * Generate an amicable teacher code based on syllables
     */
    public function generateAmicableTeacherCode($firstName, $lastName)
    {
        // Normalitzar noms (treure accents, caràcters especials)
        $firstName = $this->normalizeName($firstName);
        $lastName = $this->normalizeName($lastName);
        
        // Opció 1: 1 síl·laba nom + 1 síl·laba cognom
        $code1 = $this->generateSyllableCode($firstName, $lastName, 1, 1);
        if (!empty($code1) && !$this->codeExists($code1)) {
            \Log::info("Selected code (Option 1): " . $code1);
            return $code1;
        }
        
        // Opció 2: 1 síl·laba nom + 1 síl·laba cognom (alternativa)
        $code2 = $this->generateSyllableCode($firstName, $lastName, 1, 1, 'alt');
        if (!empty($code2) && !$this->codeExists($code2)) {
            \Log::info("Selected code (Option 2): " . $code2);
            return $code2;
        }
        
        // Opció 3: 1 síl·laba nom + 2 síl·labes cognom
        $code3 = $this->generateSyllableCode($firstName, $lastName, 1, 2);
        if (!empty($code3) && !$this->codeExists($code3)) {
            \Log::info("Selected code (Option 3): " . $code3);
            return $code3;
        }
        
        // Si tots existeixen, afegir sufix numèric a la primera opció
        if (!empty($code1)) {
            \Log::info("All codes exist, generating with suffix for base: " . $code1);
            return $this->generateWithSuffix($code1);
        }
        
        // Si no es pot generar, tornar al mètode antic
        return $this->generateFallbackCode($firstName, $lastName);
    }
    
    /**
     * Check if teacher code already exists
     */
    private function codeExists($code)
    {
        return CampusTeacher::where('teacher_code', $code)->exists();
    }
    
    /**
     * Generate code with numeric suffix (01, 02, etc.)
     */
    private function generateWithSuffix($baseCode)
    {
        $suffix = 1;
        do {
            $code = $baseCode . str_pad($suffix, 2, '0', STR_PAD_LEFT);
            $suffix++;
        } while ($this->codeExists($code));
        
        return $code;
    }
    
    /**
     * Generate syllable-based code
     */
    private function generateSyllableCode($firstName, $lastName, $nameSyllables, $surnameSyllables, $type = 'default')
    {
        $nameSyllableList = $this->getSyllables($firstName);
        $surnameSyllableList = $this->getSyllables($lastName);
        
        if (empty($nameSyllableList) || empty($surnameSyllableList)) {
            return '';
        }
        
        // Obtenir síl·labes segons el tipus
        $namePart = $this->getSyllablePart($nameSyllableList, $nameSyllables, $type);
        $surnamePart = $this->getSyllablePart($surnameSyllableList, $surnameSyllables, $type);
        
        return strtoupper($namePart . $surnamePart);
    }
    
    /**
     * Get syllable part based on type
     */
    private function getSyllablePart($syllableList, $count, $type)
    {
        $part = '';
        
        if ($type === 'default') {
            // Tipus per defecte: primeres síl·labes
            for ($i = 0; $i < min($count, count($syllableList)); $i++) {
                $part .= $syllableList[$i];
            }
        } elseif ($type === 'alt') {
            // Tipus alternatiu: síl·labes diferents
            if ($count === 1) {
                // Agafar la segona síl·laba si existeix, sinó la primera
                $part = count($syllableList) > 1 ? $syllableList[1] : $syllableList[0];
            } else {
                // Agafar les últimes síl·labes
                $start = max(0, count($syllableList) - $count);
                for ($i = $start; $i < count($syllableList); $i++) {
                    $part .= $syllableList[$i];
                }
            }
        }
        
        return $part;
    }
    
    /**
     * Get syllables from a word (simplified version for Spanish/Catalan)
     */
    private function getSyllables($word)
    {
        if (empty($word)) {
            return [];
        }
        
        $syllables = [];
        $current = '';
        $vowels = ['A', 'E', 'I', 'O', 'U'];
        
        for ($i = 0; $i < strlen($word); $i++) {
            $char = $word[$i];
            $current .= $char;
            
            // Check if current forms a syllable
            if (in_array($char, $vowels)) {
                // Check next character to decide if we should split
                $nextChar = $i + 1 < strlen($word) ? $word[$i + 1] : '';
                $nextNextChar = $i + 2 < strlen($word) ? $word[$i + 2] : '';
                
                // If next character is a consonant that doesn't form a cluster, split here
                if (!in_array($nextChar, $vowels)) {
                    // Check for consonant clusters
                    $cluster = $nextChar . $nextNextChar;
                    if (!in_array($cluster, ['LL', 'NY', 'RR', 'CH', 'PL', 'PR', 'BR', 'BL', 'CR', 'CL', 'FR', 'FL', 'GR', 'GL', 'DR', 'TR'])) {
                        $syllables[] = $current;
                        $current = '';
                    }
                }
            }
        }
        
        // Add remaining characters
        if (!empty($current)) {
            $syllables[] = $current;
        }
        
        return $syllables;
    }
    
    /**
     * Fallback method using initials
     */
    private function generateFallbackCode($firstName, $lastName)
    {
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
        
        \Log::info("Fallback code generated: " . $baseCode);
        return $baseCode;
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
