<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;
Use App\Models\FcmToken;
use Google\Auth\OAuth2;
use Kreait\Firebase\Messaging\CloudMessage;  //10/08/2025
use Kreait\Firebase\Messaging\Notification;  //10/08/2025
use Kreait\Firebase\Factory; //10/08/2025
use Kreait\Firebase\Messaging; //10/08/2025

class FCMService
{
    protected string $credentialsPath;

    

	public function __construct()
	{
		try {
			$firebase = (new Factory)
				->withServiceAccount(storage_path(config('services.firebase.credentials')))
				 ->createMessaging();
		
		} catch (\Exception $e) {
			Log::error('Error inicializando FCMService: ' . $e->getMessage());
		}
	} 


	protected function getAccessToken(): ?string
	{
	    if (!file_exists($this->credentialsPath)) {
	        Log::error("No se encontró el archivo de credenciales FCM: {$this->credentialsPath}");
	        return null;
	    }

	    $jsonKey = json_decode(file_get_contents($this->credentialsPath), true);

	    $oauth = new OAuth2([
        	'audience' => 'https://oauth2.googleapis.com/token',
	        'issuer' => $jsonKey['client_email'],
        	'signingAlgorithm' => 'RS256',
	        'signingKey' => $jsonKey['private_key'],
        	'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
	        'tokenCredentialUri' => $jsonKey['token_uri'],
	    ]);

	    $token = $oauth->fetchAuthToken();

	    return $token['access_token'] ?? null;
	}

	public function sendToUser(User $user, $title, $body, $data = [])
	{
		$tokens = $user->fcmTokens()
					->where('is_valid', true)
					->pluck('token')
					->toArray();

		if (empty($tokens)) {
			Log::error("User {$user->id} has no valid FCM tokens");
			return [
				'sent' => 0,
				'total' => 0,
				'results' => []
			];
		}

		return $this->sendToTokens($tokens, $title, $body, $data);
	}

	public function sendToTokens(array $tokens, string $title, string $body, array $data = [])
	{
		if (empty($tokens)) {
			Log::error("No tokens provided for FCM send");
			return false;
		}

		try {
			$validTokens = [];
			$invalidTokens = [];
			
			// Verificar tokens antes de enviar
			foreach ($tokens as $token) {
				$response = $this->messaging->validateRegistrationToken($token);
				if ($response['valid']) {
					$validTokens[] = $token;
				} else {
					$invalidTokens[] = $token;
					Log::warning("Token inválido detectado: $token");
				}
			}

			// Actualizar base de datos
			if (!empty($invalidTokens)) {
				FcmToken::whereIn('token', $invalidTokens)->update(['is_valid' => false]);
			}

			if (empty($validTokens)) {
				Log::error("No valid tokens after validation");
				return false;
			}

			$message = CloudMessage::new()
				->withNotification(Notification::create($title, $body))
				->withData($data);

			$report = $this->messaging->sendMulticast($message, $validTokens);
			
			// Retornar reporte detallado
			return [
				'sent' => $report->successes()->count(),
				'total' => count($validTokens),
				'results' => array_map(function ($result) {
					return [
						'token' => $result->target()->value(),
						'status' => $result->isSuccess() ? 'success' : 'failed',
						'error' => $result->isFailure() ? $result->error() : null
					];
				}, iterator_to_array($report->getIterator()))
			];
		} catch (\Exception $e) {
			Log::error("FCM Error: " . $e->getMessage());
			return false;
		}
	}

}
