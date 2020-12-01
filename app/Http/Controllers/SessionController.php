<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                    'tokenGeradoEm' => Carbon::now()->toDateTimeString(),
                    'tokenExpiradoEm' => Carbon::now()->addHour()->toDateTimeString(),
                ]);

                if (!$token = auth()->tokenById($session->id)) {
                    return response()->json([
                        'mensagem' => 'Não foi possível gerar um token'
                    ], 401);
                } else {
                    return response()->json([
                        'contaDominio' => $session->contaDominio,
                        'token' => $token,
                    ]);
                }
            } else {
                return response()->json([
                    'mensagem' => 'Senha inválida'
                ], 401);
            }
        } catch (\LdapRecord\Models\ModelNotFoundException $e) {
            return response()->json([
                'mensagem' => 'Usuário inválido'
            ], 401);
        }
    }
}
