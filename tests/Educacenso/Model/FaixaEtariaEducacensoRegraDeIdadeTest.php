<?php

namespace Tests\Educacenso\Model;

use App_Model_LocalFuncionamentoDiferenciado as Local;
use iEducar\Modules\Educacenso\Model\FaixaEtariaEducacenso;
use iEducar\Modules\Educacenso\Model\OrganizacaoCurricular as Org;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class FaixaEtariaEducacensoRegraDeIdadeTest extends TestCase
{
    private const ETAPA_18 = ['faixa' => [7, 50], 'origem' => FaixaEtariaEducacenso::ORIGEM_ETAPA];

    private const ETAPA_25 = ['faixa' => [12, 58], 'origem' => FaixaEtariaEducacenso::ORIGEM_ETAPA];

    private const PRISIONAL = ['faixa' => [18, 94], 'origem' => FaixaEtariaEducacenso::ORIGEM_PRISIONAL];

    private const SOCIOEDUCATIVO = ['faixa' => [12, 94], 'origem' => FaixaEtariaEducacenso::ORIGEM_SOCIOEDUCATIVO];

    private const ITINERARIO = ['faixa' => [12, 94], 'origem' => FaixaEtariaEducacenso::ORIGEM_ITINERARIO_SEM_FORMACAO_GERAL_BASICA];

    private const ETAPA_SEM_FAIXA = 3;

    // ---------------------------------------------------------------
    // Caixa-preta
    // ---------------------------------------------------------------

    public static function etapasComFaixa(): array
    {
        return [
            'CT01 etapa 1' => [1, [0, 6]],
            'CT01 etapa 2' => [2, [3, 9]],
            'CT01 etapa 14' => [14, [4, 50]],
            'CT01 etapa 15' => [15, [5, 50]],
            'CT01 etapa 16' => [16, [5, 50]],
            'CT01 etapa 17' => [17, [6, 50]],
            'CT01 etapa 18' => [18, [7, 50]],
            'CT01 etapa 19' => [19, [8, 50]],
            'CT01 etapa 20' => [20, [9, 50]],
            'CT01 etapa 21' => [21, [10, 50]],
            'CT01 etapa 25' => [25, [12, 58]],
            'CT01 etapa 26' => [26, [12, 58]],
            'CT01 etapa 27' => [27, [12, 58]],
            'CT01 etapa 28' => [28, [12, 58]],
            'CT01 etapa 29' => [29, [12, 58]],
            'CT01 etapa 35' => [35, [12, 58]],
            'CT01 etapa 36' => [36, [12, 58]],
            'CT01 etapa 37' => [37, [12, 58]],
            'CT01 etapa 38' => [38, [12, 58]],
            'CT01 etapa 39' => [39, [13, 58]],
            'CT01 etapa 40' => [40, [13, 75]],
            'CT01 etapa 41' => [41, [12, 50]],
            'CT01 etapa 67' => [67, [15, 94]],
            'CT01 etapa 68' => [68, [12, 94]],
            'CT01 etapa 69' => [69, [12, 93]],
            'CT01 etapa 70' => [70, [12, 93]],
            'CT01 etapa 71' => [71, [15, 93]],
            'CT01 etapa 73' => [73, [12, 94]],
            'CT01 etapa 74' => [74, [15, 94]],
            'CT01 etapa 75' => [75, [12, 94]],
        ];
    }

    #[Group('caixa-preta')]
    #[DataProvider('etapasComFaixa')]
    public function test_etapa_com_faixa_retorna_a_faixa_da_etapa(int $etapa, array $faixa)
    {
        $this->assertSame(
            ['faixa' => $faixa, 'origem' => FaixaEtariaEducacenso::ORIGEM_ETAPA],
            FaixaEtariaEducacenso::regraDeIdadeDoAluno($etapa, null, null)
        );
    }

    public static function etapasSemFaixa(): array
    {
        return [
            'CT02 etapa 3' => [3],
            'CT02 etapa 13' => [13],
            'CT02 etapa 22' => [22],
            'CT02 etapa 24' => [24],
            'CT02 etapa 30' => [30],
            'CT02 etapa 34' => [34],
            'CT02 etapa 42' => [42],
            'CT02 etapa 66' => [66],
            'CT02 etapa 72' => [72],
            'CT03 etapa 0' => [0],
            'CT03 etapa -1' => [-1],
            'CT03 etapa 76' => [76],
            'CT03 etapa 999' => [999],
            'CT04 etapa nula' => [null],
        ];
    }

    #[Group('caixa-preta')]
    #[DataProvider('etapasSemFaixa')]
    public function test_etapa_sem_faixa_retorna_nulo(?int $etapa)
    {
        $this->assertNull(FaixaEtariaEducacenso::regraDeIdadeDoAluno($etapa, null, null));
    }

    public static function locaisDeFuncionamento(): array
    {
        return [
            'CT05 prisional (3)' => [Local::UNIDADE_PRISIONAL, self::PRISIONAL],
            'CT06 socioeducativo (2)' => [Local::UNIDADE_ATENDIMENTO_SOCIOEDUCATIVO, self::SOCIOEDUCATIVO],
            'CT07 não diferenciado (0)' => [Local::NAO_ESTA, self::ETAPA_18],
            'CT07 sala anexa (1)' => [Local::SALA_ANEXA, self::ETAPA_18],
            'CT08 código 4' => [4, self::ETAPA_18],
            'CT08 código -1' => [-1, self::ETAPA_18],
            'CT08 código 99' => [99, self::ETAPA_18],
        ];
    }

    #[Group('caixa-preta')]
    #[DataProvider('locaisDeFuncionamento')]
    public function test_local_de_funcionamento_define_a_faixa(int $local, array $esperado)
    {
        $this->assertSame($esperado, FaixaEtariaEducacenso::regraDeIdadeDoAluno(18, null, $local));
    }

    public static function organizacoesCurriculares(): array
    {
        $aprofundamento = Org::ITINERARIO_FORMATIVO_APROFUNDAMENTO;
        $tecnico = Org::ITINERARIO_FORMACAO_TECNICA_PROFISSIONAL;
        $formacaoGeral = Org::FORMACAO_GERAL_BASICA;

        return [
            'CT09 só aprofundamento' => [[$aprofundamento], self::ITINERARIO],
            'CT10 só técnico' => [[$tecnico], self::ITINERARIO],
            'CT11 os dois itinerários' => [[$aprofundamento, $tecnico], self::ITINERARIO],
            'CT12 aprofundamento + formação geral' => [[$formacaoGeral, $aprofundamento], self::ETAPA_25],
            'CT12 técnico + formação geral' => [[$formacaoGeral, $tecnico], self::ETAPA_25],
            'CT12 todos os códigos' => [[$aprofundamento, $tecnico, $formacaoGeral], self::ETAPA_25],
            'CT13 só formação geral' => [[$formacaoGeral], self::ETAPA_25],
            'CT14 lista vazia' => [[], self::ETAPA_25],
            'CT14 não informada' => [null, self::ETAPA_25],
            'CT15 código inexistente' => [[2], self::ETAPA_25],
        ];
    }

    #[Group('caixa-preta')]
    #[DataProvider('organizacoesCurriculares')]
    public function test_organizacao_curricular_define_a_faixa(?array $organizacao, array $esperado)
    {
        $this->assertSame($esperado, FaixaEtariaEducacenso::regraDeIdadeDoAluno(25, $organizacao, null));
    }

    public static function caracteristicasComEtapaSemFaixa(): array
    {
        $itinerario = [Org::ITINERARIO_FORMATIVO_APROFUNDAMENTO];

        return [
            'CT16 prisional' => [self::ETAPA_SEM_FAIXA, null, Local::UNIDADE_PRISIONAL, self::PRISIONAL],
            'CT17 socioeducativo, etapa nula' => [null, null, Local::UNIDADE_ATENDIMENTO_SOCIOEDUCATIVO, self::SOCIOEDUCATIVO],
            'CT18 itinerário' => [self::ETAPA_SEM_FAIXA, $itinerario, null, self::ITINERARIO],
        ];
    }

    #[Group('caixa-preta')]
    #[DataProvider('caracteristicasComEtapaSemFaixa')]
    public function test_caracteristica_da_turma_vale_mesmo_sem_faixa_da_etapa(
        ?int $etapa,
        ?array $organizacao,
        ?int $local,
        array $esperado
    ) {
        $this->assertSame($esperado, FaixaEtariaEducacenso::regraDeIdadeDoAluno($etapa, $organizacao, $local));
    }

    // ---------------------------------------------------------------
    // Caixa-branca
    // ---------------------------------------------------------------

    public static function paresMcdcDoItinerario(): array
    {
        $aprofundamento = Org::ITINERARIO_FORMATIVO_APROFUNDAMENTO;
        $tecnico = Org::ITINERARIO_FORMACAO_TECNICA_PROFISSIONAL;

        return [
            'MC1 C1=V C2=F C3=V' => [[$aprofundamento], self::ITINERARIO],
            'MC2 C1=F C2=V C3=V' => [[$tecnico], self::ITINERARIO],
            'MC3 C1=F C2=F C3=V' => [[2], null],
            'MC4 C1=V C2=F C3=F' => [[$aprofundamento, Org::FORMACAO_GERAL_BASICA], null],
        ];
    }

    #[Group('caixa-branca')]
    #[DataProvider('paresMcdcDoItinerario')]
    public function test_mcdc_itinerario_sem_formacao_geral_basica(array $organizacao, ?array $esperado)
    {
        $this->assertSame(
            $esperado,
            FaixaEtariaEducacenso::regraDeIdadeDoAluno(self::ETAPA_SEM_FAIXA, $organizacao, null)
        );
    }

    public static function caracteristicasSimultaneas(): array
    {
        $itinerario = [Org::ITINERARIO_FORMATIVO_APROFUNDAMENTO];

        return [
            'CB01 prisional e itinerário' => [$itinerario, Local::UNIDADE_PRISIONAL, self::PRISIONAL],
            'CB02 socioeducativo e itinerário' => [$itinerario, Local::UNIDADE_ATENDIMENTO_SOCIOEDUCATIVO, self::SOCIOEDUCATIVO],
        ];
    }

    #[Group('caixa-branca')]
    #[DataProvider('caracteristicasSimultaneas')]
    public function test_local_diferenciado_prevalece_sobre_o_itinerario(array $organizacao, int $local, array $esperado)
    {
        $this->assertSame($esperado, FaixaEtariaEducacenso::regraDeIdadeDoAluno(25, $organizacao, $local));
    }

    public static function organizacoesComCodigosEmString(): array
    {
        return [
            'CB03 só aprofundamento' => [['4'], self::ITINERARIO],
            'CB03 só técnico' => [['5'], self::ITINERARIO],
            'CB03 itinerário + formação geral' => [['1', '4'], self::ETAPA_25],
        ];
    }

    #[Group('caixa-branca')]
    #[DataProvider('organizacoesComCodigosEmString')]
    public function test_codigos_da_organizacao_em_string_tem_o_mesmo_efeito(array $organizacao, array $esperado)
    {
        $this->assertSame($esperado, FaixaEtariaEducacenso::regraDeIdadeDoAluno(25, $organizacao, null));
    }
}
