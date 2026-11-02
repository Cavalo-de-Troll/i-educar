<?php

namespace Tests\Educacenso\Validator;

use iEducar\Modules\Educacenso\Validator\NameValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class NameValidatorRegraTest extends TestCase
{
    private const VALIDO = [true, null];

    private const TAMANHO = [false, 'O Nome deve ter no máximo 100 caracteres.'];

    private const REPETICAO = [false, 'Nome não pode ter a repetição de 4 caracteres seguidos.'];

    private const ESPECIAIS = [false, 'O Nome ou Nome Social não pode conter números ou caracteres especiais como: (0-9!@#\$%^&*?_~-.)'];

    private function validar(string $nome): array
    {
        $validator = new NameValidator($nome);

        return [$validator->isValid(), $validator->getMessage()];
    }

    // ---------------------------------------------------------------
    // Caixa-branca
    // ---------------------------------------------------------------

    public static function decisoes(): array
    {
        return [
            'CB01 D1=V' => [str_repeat('Ab', 50) . 'A', self::TAMANHO],
            'CB02 D1=F D2=V' => ['Mariaaaa', self::REPETICAO],
            'CB03 D1=F D2=F D3=V' => ['Ana1', self::ESPECIAIS],
            'CB04 D1=F D2=F D3=F' => ['Maria', self::VALIDO],
        ];
    }

    #[Group('caixa-branca')]
    #[DataProvider('decisoes')]
    public function test_cada_decisao_verdadeira_e_falsa(string $nome, array $esperado)
    {
        $this->assertSame($esperado, $this->validar($nome));
    }

    public static function paresMcdcDoTamanho(): array
    {
        return [
            'MC1 C1=V C2=V' => [str_repeat('Ab', 50) . 'A', self::TAMANHO],
            'MC2 C1=V C2=F' => ['Ana', self::VALIDO],
            'MC3 C1=F C2=F (vazio)' => ['', self::ESPECIAIS],
            'MC4 C1=F C2=F (zero)' => ['0', self::ESPECIAIS],
        ];
    }

    #[Group('caixa-branca')]
    #[DataProvider('paresMcdcDoTamanho')]
    public function test_mcdc_da_decisao_de_tamanho(string $nome, array $esperado)
    {
        $this->assertSame($esperado, $this->validar($nome));
    }

    public static function prioridadeEntreDecisoes(): array
    {
        return [
            'CB05 D1 antes de D2 e D3' => [str_repeat('1', 101), self::TAMANHO],
            'CB06 D2 antes de D3' => ['Annnna1', self::REPETICAO],
        ];
    }

    #[Group('caixa-branca')]
    #[DataProvider('prioridadeEntreDecisoes')]
    public function test_primeira_decisao_violada_define_a_mensagem(string $nome, array $esperado)
    {
        $this->assertSame($esperado, $this->validar($nome));
    }

    public static function detalhesDaImplementacao(): array
    {
        return [
            'CB07 trim só no tamanho' => [' ' . str_repeat('Ab', 50) . ' ', self::VALIDO],
            'CB08 maiúsculas antes da repetição' => ['maRiAaAa', self::REPETICAO],
            'CB09 só espaços' => ['   ', self::VALIDO],
        ];
    }

    #[Group('caixa-branca')]
    #[DataProvider('detalhesDaImplementacao')]
    public function test_detalhes_da_implementacao(string $nome, array $esperado)
    {
        $this->assertSame($esperado, $this->validar($nome));
    }

    // ---------------------------------------------------------------
    // Caixa-preta
    // ---------------------------------------------------------------

    public static function classesDeEquivalencia(): array
    {
        return [
            'CT01 letras sem acento' => ['Lorem Ipsum', self::VALIDO],
            'CT02 letras acentuadas' => ['João da Conceição', self::VALIDO],
            'CT03 apóstrofo' => ["Maria D'Ávila", self::VALIDO],
            'CT04 dígitos' => ['João 123', self::ESPECIAIS],
            'CT05 hífen' => ['Ana-Maria', self::ESPECIAIS],
            'CT05 ponto' => ['Ana.Maria', self::ESPECIAIS],
            'CT05 arroba' => ['Ana@', self::ESPECIAIS],
            'CT06 4 repetidos sem acento' => ['Lorem Ipsuuuum', self::REPETICAO],
            'CT07 4 repetidos com acento' => ['Joãããão', self::REPETICAO],
        ];
    }

    #[Group('caixa-preta')]
    #[DataProvider('classesDeEquivalencia')]
    public function test_classes_de_equivalencia(string $nome, array $esperado)
    {
        $this->assertSame($esperado, $this->validar($nome));
    }

    public static function valoresLimite(): array
    {
        return [
            'CT08 1 caractere' => ['A', self::VALIDO],
            'CT09 100 sem acento' => [str_repeat('Ab', 50), self::VALIDO],
            'CT10 101 sem acento' => [str_repeat('Ab', 50) . 'A', self::TAMANHO],
            'CT11 100 com acento' => [str_repeat('Áb', 50), self::VALIDO],
            'CT12 101 com acento' => [str_repeat('Áb', 50) . 'Á', self::TAMANHO],
            'CT13 3 repetidos sem acento' => ['Mariaaa', self::VALIDO],
            'CT14 4 repetidos sem acento' => ['Mariaaaa', self::REPETICAO],
            'CT15 3 repetidos com acento' => ['Joããão', self::VALIDO],
        ];
    }

    #[Group('caixa-preta')]
    #[DataProvider('valoresLimite')]
    public function test_valores_limite(string $nome, array $esperado)
    {
        $this->assertSame($esperado, $this->validar($nome));
    }
}
