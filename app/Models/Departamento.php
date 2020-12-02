<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $primaryKey = 'Codigo';
    protected $table = 'Departamentos';

    protected $fillable = [
        'Codigo',
        'Nome',
        'Descricao',
        'DataAlteracao',
        'Editor',
        'DataCriacao',
        'Criador',
    ];

    public function gerente()
    {
        $gerente = DB::table('MembrosPessoasJuridicas')
            ->join('PessoasFisicas', 'PessoasFisicas.Codigo', '=', 'MembrosPessoasJuridicas.CodigoPessoaFisica')
            ->join('Departamentos', 'Departamentos.Codigo', '=', 'MembrosPessoasJuridicas.CodigoDepartamento')
            ->join('Cargos', 'Cargos.Codigo', '=', 'MembrosPessoasJuridicas.CodigoCargo')
            ->where('MembrosPessoasJuridicas.CodigoTipoPessoaFisica', '!=', 4)
            ->where('MembrosPessoasJuridicas.CodigoPessoaJuridica', 1)
            ->where('Departamentos.Codigo', $this->Codigo)
            ->where('Cargos.Nome', 'like', 'Gerente%')
            ->select([
                'Departamentos.Nome AS NomeDepartamento',
                'PessoasFisicas.Codigo AS CodigoPessoaFisica',
                'PessoasFisicas.ContaDominio',
                'PessoasFisicas.Nome',
                'PessoasFisicas.CPF',
                'Cargos.Nome AS Cargo',
            ])
            ->first();

        return [
            'codigoPessoaFisica' => intval($gerente->CodigoPessoaFisica),
            'cpf' => $gerente->CPF,
            'contaDominio' => $gerente->ContaDominio,
            'nome' => $gerente->Nome,
            'cargo' => $gerente->Cargo,
            'gerente' => [
                'codigo' => intval($this->Codigo),
                'nome' => $gerente->NomeDepartamento,
            ],
        ];
    }

    public function funcionarios()
    {
        $pessoasFisicas = DB::table('Departamentos')
            ->join('MembrosPessoasJuridicas', 'MembrosPessoasJuridicas.CodigoDepartamento', '=', 'Departamentos.Codigo')
            ->join('PessoasFisicas', 'PessoasFisicas.Codigo', '=', 'MembrosPessoasJuridicas.CodigoPessoaFisica')
            ->join('Cargos', 'Cargos.Codigo', '=', 'MembrosPessoasJuridicas.CodigoCargo')
            ->where('MembrosPessoasJuridicas.CodigoTipoPessoaFisica', '!=', 4)
            ->where('MembrosPessoasJuridicas.CodigoPessoaJuridica', 1)
            ->where('Departamentos.Codigo', $this->Codigo)
            ->orderBy('PessoasFisicas.Nome')
            ->select([
                'PessoasFisicas.Codigo AS CodigoPessoaFisica',
                'PessoasFisicas.ContaDominio',
                'PessoasFisicas.CPF',
                'PessoasFisicas.Nome',
                'Cargos.Nome AS NomeCargo',
                'Departamentos.Nome AS NomeDepartamento',
                'Departamentos.Codigo AS CodigoDepartamento',
            ])
            ->get();

        $response = [];
        foreach ($pessoasFisicas as $pessoa)
            array_push($response, [
                'codigoPessoaFisica' => intval($pessoa->CodigoPessoaFisica),
                'cpf' => $pessoa->CPF,
                'nome' => $pessoa->Nome,
                'contaDominio' => $pessoa->ContaDominio,
                'cargo' => $pessoa->NomeCargo,
                'departamento' => [
                    'codigo' => intval($this->CodigoDepartamento),
                    'nome' => $pessoa->NomeDepartamento,
                ],
            ]);

        return $response;
    }
}
