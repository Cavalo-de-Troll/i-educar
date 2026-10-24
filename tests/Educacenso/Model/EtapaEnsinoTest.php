<?php

/**
 * Testes unitários para EtapaEnsino::getEtapasPermitidas.
 *
 * Técnicas utilizadas:
 * - Caixa-preta: particionamento de equivalência e análise de valor-limite.
 *   Os casos CT27, CT28 e CT29 complementam as classes de equivalência,
 *   contemplando códigos não cadastrados e valores ausentes. Não foram
 *   identificados limites numéricos de negócio, pois os parâmetros representam
 *   categorias codificadas e valores booleanos.
 *
 * - Caixa-branca: cobertura de decisões/branches e MC/DC.
 *   Os casos CB01 a CB30 foram projetados a partir das decisões compostas
 *   D1 a D4 do método, garantindo a independência das condições envolvidas.
 *
 * Rastreabilidade:
 * - CB01 a CB30 correspondem aos casos de caixa-branca documentados no relatório.
 * - CT27 a CT29 correspondem aos complementos de caixa-preta documentados no relatório.
 * - Cada identificador relaciona o teste ao caso, às classes de equivalência,
 *   às decisões cobertas e à regra funcional correspondente.
 *
 * Evidências de cobertura:
 * - D1: EAD + curricular — CB01/CB02 e CB01/CB26.
 * - D2: Semipresencial + curricular — CB04/CB05 e CB04/CB15.
 * - D3: Presencial + curricular — CB24/CB21 e CB24/CB22.
 * - D4: Presencial + atividade complementar — CB06/CB21 e CB06/CB07.
 */

namespace Tests\Educacenso\Model;

use PHPUnit\Framework\Attributes\DataProvider;
use iEducar\Modules\Educacenso\Model\EtapaEnsino;
use PHPUnit\Framework\TestCase;

class EtapaEnsinoCaixaPretaBrancaTest extends TestCase
{
    #[DataProvider('casosDeEtapasPermitidas')]
    public function test_retorna_etapas_conforme_combinacao(
        ?int $mediacao,
        bool $curricular,
        bool $curricularComAtividadeComplementar,
        ?int $etapaAgregada,
        bool $temFormacaoGeralBasica,
        ?array $esperado
    ): void {
        $resultado = EtapaEnsino::getEtapasPermitidas(
            $mediacao,
            $curricular,
            $curricularComAtividadeComplementar,
            $etapaAgregada,
            $temFormacaoGeralBasica
        );

        $this->assertSame($esperado, $resultado);
    }

    public static function casosDeEtapasPermitidas(): array
    {
        // Ordem: mediação, curricular, atividade complementar,
        // etapa agregada, FGB e saída esperada.
        return [
            'CB01 - EAD curricular de Ensino Médio com FGB' => [
                3, true, false, 304, true,
                [25, 26, 27, 28, 29],
            ],

            'CB02 - EAD sem indicadores curriculares' => [
                3, false, false, 304, true,
                null,
            ],

            'CB03 - Presencial curricular de Educação Infantil' => [
                1, true, false, 301, false,
                [1, 2, 3],
            ],

            'CB04 - Semipresencial curricular de EJA' => [
                2, true, false, 306, false,
                [69, 70, 71, 72],
            ],

            'CB05 - Semipresencial sem indicadores curriculares' => [
                2, false, false, 306, false,
                null,
            ],

            'CB06 - Presencial com atividade complementar de Fundamental' => [
                1, false, true, 302, false,
                [14, 15, 16, 17, 18, 19, 20, 21, 41],
            ],

            'CB07 - Semipresencial com indicador de atividade complementar' => [
                2, false, true, 302, false,
                null,
            ],

            'CB08 - Mediação ausente e indicadores curriculares falsos' => [
                null, false, false, null, false,
                null,
            ],

            'CB09 - EAD curricular de Ensino Médio sem FGB' => [
                3, true, false, 304, false,
                null,
            ],

            'CB10 - EAD curricular de EJA' => [
                3, true, false, 306, false,
                [71, 74, 67],
            ],

            'CB11 - EAD curricular de Curso Técnico e FIC' => [
                3, true, false, 308, false,
                [39, 40, 64, 68, 75],
            ],

            'CB12 - EAD curricular de etapa não habilitada' => [
                3, true, false, 301, false,
                null,
            ],

            'CB13 - Presencial curricular de Ensino Médio sem FGB' => [
                1, true, false, 304, false,
                null,
            ],

            'CB14 - Presencial curricular de Magistério com FGB' => [
                1, true, false, 305, true,
                [35, 36, 37, 38],
            ],

            'CB15 - Presencial curricular de EJA' => [
                1, true, false, 306, false,
                [69, 70, 72, 71, 74, 73, 67],
            ],

            'CB16 - Presencial curricular de Curso Técnico e FIC' => [
                1, true, false, 308, false,
                [39, 40, 64, 68, 75],
            ],

            'CB17 - Presencial curricular de etapa não cadastrada' => [
                1, true, false, 307, false,
                null,
            ],

            'CB18 - Presencial com atividade complementar de Médio sem FGB' => [
                1, false, true, 304, false,
                null,
            ],

            'CB19 - Presencial com atividade complementar de Magistério sem FGB' => [
                1, false, true, 305, false,
                null,
            ],

            'CB20 - Presencial com atividade complementar de etapa não habilitada' => [
                1, false, true, 301, false,
                null,
            ],

            'CB21 - Presencial sem indicadores curriculares' => [
                1, false, false, 302, false,
                null,
            ],

            'CB22 - Mediação ausente com indicador curricular' => [
                null, true, false, 302, false,
                null,
            ],

            'CB23 - Presencial com atividade complementar de Multi e correção' => [
                1, false, true, 303, false,
                [22, 23],
            ],

            'CB24 - Presencial curricular de Ensino Fundamental' => [
                1, true, false, 302, false,
                [14, 15, 16, 17, 18, 19, 20, 21, 41],
            ],

            'CB25 - Presencial curricular de Multi e correção de fluxo' => [
                1, true, false, 303, false,
                [22, 23, 56],
            ],

            'CB26 - Presencial curricular de Ensino Médio com FGB' => [
                1, true, false, 304, true,
                [25, 26, 27, 28, 29],
            ],

            'CB27 - Presencial com atividade complementar de Médio com FGB' => [
                1, false, true, 304, true,
                [25, 26, 27, 28, 29],
            ],

            'CB28 - Presencial com atividade complementar de Magistério com FGB' => [
                1, false, true, 305, true,
                [35, 36, 37, 38],
            ],

            'CB29 - Semipresencial curricular de etapa não habilitada' => [
                2, true, false, 304, true,
                null,
            ],

            'CB30 - Presencial curricular de Magistério sem FGB' => [
                1, true, false, 305, false,
                null,
            ],

            // Caixa-preta — particionamento de equivalência:
            // CT27, CT28 e CT29 complementam as classes inválidas e ausentes,
            // validando o comportamento para códigos não cadastrados e valores não informados.
            //
            // Caixa-branca — decisões e MC/DC:
            // CB01 a CB30 exercitam as decisões D1 a D4 e suas condições,
            // conforme a tabela de rastreabilidade e os pares de independência do relatório.

            'CT27 - Mediação com código não cadastrado' => [
                4, true, false, 304, true,
                null,
            ],

            'CT28 - Etapa agregada ausente' => [
                1, true, false, null, true,
                null,
            ],

            'CT29 - Etapa agregada com código não cadastrado' => [
                1, true, false, 307, true,
                null,
            ],
        ];
    }
}