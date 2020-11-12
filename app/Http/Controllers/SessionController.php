<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

use Illuminate\Http\Request;

use App\Models\TrilhaDeAuditoria;

use LdapRecord\Container;
use LdapRecord\Models\ActiveDirectory\User;

class SessionController extends Controller
{
    public function index()
    {
        return auth()->user();
    }

    public function store(Request $request)
    {
        try {
            $connection = Container::getConnection();
            $userAd = User::findByOrFail('samaccountname', $request->contaDominio);

            if ($connection->auth()->attempt($userAd->getDn(), $request->senha)) {
                $session = TrilhaDeAuditoria::create([
                    'contaDominio' => $request->contaDominio,
                    'codigoCooperativa' => mb_substr($request->contaDominio, -7, 4),
                    'tokenGeradoEm' => Carbon::now()->toDateTimeString(),
                    'tokenExpiradoEm' => Carbon::now()->addHour()->toDateTimeString(),
                ]);

                if (!$token = auth()->tokenById($session->id)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Não foi possível gerar um token'
                    ]);
                } else {
                    return response()->json([
                        'success' => true,
                        'token' => $token,
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Senha inválida'
                ]);
            }
        } catch (\LdapRecord\Models\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário inválido'
            ]);
        }
    }
}
