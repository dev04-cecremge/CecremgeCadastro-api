<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cooperativa extends Model
{
    protected $primaryKey = 'Codigo';
    protected $table = 'Cooperativas';

    protected $fillable = [
        'Codigo',
        'Matricula',
        'Sigla',
        'InscricaoEstadual',
        'DataRegistro',
        'Agencia',
        'Fax',
        'TelefonePrincipal',
        'TelefoneSecundario',
        'TelefoneCelular',
        'TelefoneVOIP',
        'EMailPrincipal',
        'DataLiquidacao',
        'EMailSecundario',
        'WebSite',
        'DataConstituicao',
        'CodigoTipoConvenioBancoob',
        'DataConvenioBancoob',
        'DataCancelamentoConvenioBancoob',
        'NumeroContaCentralizacaoFinanceira',
        'IndicadorParticipacaoFGS',
        'QuantidadeAssociadosAtivos',
        'QuantidadeAssociadosCorrentistas',
        'QuantidadeAssociadosNaoCorrentistas',
        'QuantidadeFuncionarios',
        'QuantidadeEstagiarios',
        'KMCentral',
        'SiglaDiretorio',
        'IndicadorSedePropria',
        'ValorAluguelSede',
        'ProprietarioSede',
        'NomeResponsavelLiquidacao',
        'DataReferenciaStatusFiliacao',
        'OutrasInformacoes',
        'CodigoPessoaJuridica',
        'CodigoRamoAtuacao',
        'CodigoSegmento',
        'CodigoStatusCadastro',
        'CodigoStatusFiliacao',
        'CodigoModalidadeSeguroPrestamista',
        'CodigoRegiao',
        'CodigoCooperativaIncorporadora',
        'NomeGrupoPortal',
        'DataAlteracao',
        'Editor',
        'DataCriacao',
        'Criador',
        'CodigoPessoaJuridicaCentral',
        'NumeroSISBR',
        'IndicadorSCIRMesPar',
        'CodigoSubStatusFiliacao',
        'IndicadorMarcaSicoob',
        'IndicadorLicencaSicoob',
        'QuantidadeAssociadosCorrentistasPF',
        'QuantidadeAssociadosNaoCorrentistasPF',
        'QuantidadeAssociadosCorrentistasPJ',
        'QuantidadeAssociadosNaoCorrentistasPJ',
        'DataAdesaoCadeiaSites',
        'DataPublicacaoSiteCadeia',
        'CodigoCentralIncorporadora',
    ];
}
