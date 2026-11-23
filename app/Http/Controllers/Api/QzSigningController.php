<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QzSigningController extends Controller
{
    public function sign(Request $request)
    {
        $request->validate([
            'request' => ['required', 'string'],
        ]);

        $keyPath = storage_path('app/keys/private-key.pem');

        if (!file_exists($keyPath)) {
            return response('No se encontró la llave privada.', 500);
        }

        $privateKey = openssl_pkey_get_private(
            file_get_contents($keyPath)
        );

        if (!$privateKey) {
            return response('No se pudo cargar la llave privada.', 500);
        }

        $signature = '';

        $signed = openssl_sign(
            $request->input('request'),
            $signature,
            $privateKey,
            OPENSSL_ALGO_SHA512
        );

        if (!$signed) {
            return response('No se pudo generar la firma.', 500);
        }

        return response(
            base64_encode($signature),
            200,
            ['Content-Type' => 'text/plain']
        );
    }
}
