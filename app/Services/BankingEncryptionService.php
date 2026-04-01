<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Encryption\DecryptException;

class BankingEncryptionService
{
    /**
     * Encrypt banking data securely
     */
    public function encrypt(string $data): string
    {
        if (empty($data)) {
            return '';
        }

        try {
            return Crypt::encrypt($data);
        } catch (\Exception $e) {
            Log::error('Failed to encrypt banking data', ['error' => $e->getMessage()]);
            throw new \Exception('No se pudo encriptar la información bancaria');
        }
    }

    /**
     * Decrypt banking data safely
     */
    public function decrypt(string $encryptedData): string
    {
        if (empty($encryptedData)) {
            return '';
        }

        try {
            return Crypt::decrypt($encryptedData);
        } catch (DecryptException $e) {
            Log::warning('Failed to decrypt banking data - possibly corrupted', ['error' => $e->getMessage()]);
            return ''; // Return empty for corrupted data
        } catch (\Exception $e) {
            Log::error('Error decrypting banking data', ['error' => $e->getMessage()]);
            return '';
        }
    }

    /**
     * Check if data is encrypted
     */
    public function isEncrypted(string $data): bool
    {
        if (empty($data)) {
            return false;
        }

        // First check if it's a valid IBAN format - if so, it's not encrypted
        if ($this->isValidIbanFormat($data)) {
            return false;
        }

        // Quick check: Laravel encrypted data always starts with specific base64 pattern
        if (!preg_match('/^[A-Za-z0-9+\/]+=*$/', $data)) {
            return false;
        }

        // Check for Laravel encrypted format (base64 with specific pattern)
        try {
            $decoded = base64_decode($data, true);
            if ($decoded === false) {
                return false;
            }
            
            // Laravel encrypted data has specific characteristics:
            // 1. Must be longer than 16 bytes (IV + MAC + ciphertext)
            // 2. Should not be readable ASCII text
            // 3. Should contain null bytes or non-printable characters typical of encryption
            return strlen($decoded) > 16 && 
                   (strpos($decoded, "\0") !== false || !ctype_print($decoded)) &&
                   !preg_match('/^[A-Za-z0-9\s]+$/', $decoded);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if string looks like a valid IBAN format
     */
    private function isValidIbanFormat(string $data): bool
    {
        // Remove spaces and check Spanish IBAN format
        $clean = preg_replace('/\s+/', '', $data);
        return preg_match('/^ES\d{2}\d{4}\d{4}\d{2}\d{10}$/', $clean);
    }

    /**
     * Get masked version for display
     */
    public function mask(string $data): string
    {
        $decrypted = $this->decrypt($data);
        
        if (empty($decrypted)) {
            return '';
        }

        // Mask IBAN format
        if (preg_match('/^[A-Z]{2}/', $decrypted)) {
            $clean = preg_replace('/\s+/', '', $decrypted);
            if (strlen($clean) >= 24) {
                return substr($clean, 0, 4) . ' **** **** **** ' . substr($clean, -4);
            }
        }

        // Mask other sensitive data (show first 2 and last 2 chars)
        if (strlen($decrypted) > 4) {
            return substr($decrypted, 0, 2) . str_repeat('*', strlen($decrypted) - 4) . substr($decrypted, -2);
        }

        return $decrypted;
    }

    /**
     * Encrypt IBAN with validation
     */
    public function encryptIban(string $iban): string
    {
        $cleanIban = preg_replace('/\s+/', '', $iban);
        
        // Validate IBAN format
        if (!preg_match('/^ES\d{2}\d{4}\d{4}\d{2}\d{10}$/', $cleanIban)) {
            throw new \InvalidArgumentException('Formato IBAN inválido');
        }

        return $this->encrypt($cleanIban);
    }

    /**
     * Migrate unencrypted data to encrypted
     */
    public function migrateToEncrypted(string $data): string
    {
        if (empty($data)) {
            return '';
        }

        // If already encrypted, return as-is
        if ($this->isEncrypted($data)) {
            return $data;
        }

        // If it's corrupted serialized data, clean it
        if (strpos($data, 's:') === 0) {
            Log::warning('Cleaning corrupted serialized data');
            return '';
        }

        // Encrypt the unencrypted data
        return $this->encrypt($data);
    }
}
