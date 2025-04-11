<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Encryption\Encrypter;
use App\Rules\ValidCBU;
use App\Events\CBUConsultado;
use App\Jobs\AuditCBUAccess;

class CBUController extends Controller
{
    private $customEncrypter;

    public function __construct()
    {
        // Clave de encriptación específica para CBUs (separada de APP_KEY) -- private $customEncrypter: Crea un encriptador exclusivo para CBUs (no usa el de Laravel por defecto).
		//__construct(): Al iniciar el controlador, genera una instancia de encriptación usando: Una clave específica (cbu_encryption_key definida en .env). El algoritmo AES-256-CBC (estándar bancario).
        $key = config('app.cbu_encryption_key');
        $this->customEncrypter = new Encrypter($key, 'aes-256-cbc');
    }

    /**
     * @OA\Get(
     *     path="/api/cbu/masked/{empleado}",
     *     tags={"CBU"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="CBU enmascarado"
     *     )
     * )
     */
    public function showMasked(Empleado $empleado)
	//Enmascaramiento: Muestra solo los primeros 4 y últimos 4 dígitos del CBU (ej: 1234••••••••••••5678). Hash de verificación: Genera un código único usando:  El ID del empleado + una "sal" secreta (para detectar manipulaciones).
    {
        return response()->json([
            'success' => true,
            'cbu_masked' => $empleado->getMaskedCBU(),
            'hash_verification' => hash('sha256', $empleado->id.env('CBU_SALT'))
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/cbu/full/{empleado}",
     *     tags={"CBU"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"password", "token_2fa"},
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="token_2fa", type="string")
     *         )
     *     )
     * )
     */
    public function showFull(Request $request, Empleado $empleado)//Acceso al CBU Completo (Nivel Seguridad CIA)
    {
        $validated = $request->validate([
            'password' => 'required|string',
            'token_2fa' => 'required|string'
        ]);

        // 1. Verificación en dos pasos
        if (!auth()->user()->verify2FAToken($validated['token_2fa'])) {
            AuditCBUAccess::dispatch(
                auth()->id(),
                $empleado->id,
                'failed_2fa',
                $request->ip()
            );
            return $this->securityResponse('Autenticación en dos pasos fallida');
        }

        // 2. Verificación de contraseña
        if (!Hash::check($validated['password'], auth()->user()->password)) {
            AuditCBUAccess::dispatch(
                auth()->id(),
                $empleado->id,
                'failed_password',
                $request->ip()
            );
            return $this->securityResponse('Credenciales inválidas');
        }

        // 3. Verificación de horario laboral (opcional)
        if ($this->isAfterHours() && !auth()->user()->hasRole('admin')) {
            AuditCBUAccess::dispatch(
                auth()->id(),
                $empleado->id,
                'after_hours_access',
                $request->ip()
            );
            return $this->securityResponse('Acceso restringido fuera de horario laboral');
        }

        // 4. Desencriptación segura
        try {
            $fullCBU = $this->customEncrypter->decrypt($empleado->cbu_encrypted);
            
            CBUConsultado::dispatch(
                auth()->user(),
                $empleado,
                $request->ip()
            );

            return response()->json([
                'success' => true,
                'cbu_full' => $fullCBU,
                'expires_in' => 15,
                'one_time_use' => true
            ]);

        } catch (\Exception $e) {
            Log::channel('security')->critical('CBU decryption failed', [
                'error' => $e->getMessage()
            ]);
            return $this->securityResponse('Error al procesar el CBU');
        }
    }

    private function securityResponse($message)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'cooldown' => 30, // segundos
            'attempts_left' => auth()->user()->decrementLoginAttempts()
        ], 403);
    }

    private function isAfterHours()
    {
        $now = now();
        $start = config('app.business_hours.start');
        $end = config('app.business_hours.end');
        
        return $now->hour < $start || $now->hour >= $end;
    }
}