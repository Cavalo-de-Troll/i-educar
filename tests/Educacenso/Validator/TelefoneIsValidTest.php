<?php

/**
 * Testes Unitários: iEducar\Modules\Educacenso\Validator\Telefone::isValid()
 *
 * Arquivo-alvo: src/Modules/Educacenso/Validator/Telefone.php
 * Abordagem:    Caixa-preta suplementada por caixa-branca
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * PROPÓSITO E COMPORTAMENTO
 * ─────────────────────────────────────────────────────────────────────────────
 * isValid() valida um número de telefone brasileiro sem DDD. Executa três
 * validações INDEPENDENTEMENTE (sem short-circuit), acumulando erros:
 *   V1 – validaQuantidadeDeDigitosDoTelefone(): strlen deve ser 8 ou 9.
 *   V2 – validaPrimeiroDigitoDoTelefone():
 *          condição composta: (strlen == 9) && (primeiro_dígito != '9')
 *   V3 – validaDigitosSequenciaisDoTelefone():
 *          rejeita números cujos dígitos são todos iguais (regex /^(.)\1*$/).
 *
 * Retorna true apenas se as três validações passam; false caso contrário.
 * getMessage() retorna null quando não há erros, ou array de strings com as
 * mensagens acumuladas.
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * CONDIÇÕES DE ENTRADA E CLASSES DE EQUIVALÊNCIA
 * ─────────────────────────────────────────────────────────────────────────────
 *
 * Condição: Quantidade de dígitos (strlen)
 *   Classe (1) Válida:   strlen ∈ {8, 9}
 *   Classe (2) Inválida: strlen < 8
 *   Classe (3) Inválida: strlen > 9
 *
 * Condição: Primeiro dígito (quando strlen == 9)
 *   Classe (4) Válida:   primeiroDigito == '9'
 *   Classe (5) Inválida: primeiroDigito != '9'
 *
 * Condição: Primeiro dígito (quando strlen == 8)
 *   Classe (6) Válida:   qualquer dígito (sem restrição de primeiro dígito)
 *
 * Condição: Padrão de dígitos
 *   Classe (7) Válida:   dígitos não são todos iguais
 *   Classe (8) Inválida: todos os dígitos são iguais
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * VALORES-LIMITE
 * ─────────────────────────────────────────────────────────────────────────────
 *
 * Entrada: Quantidade de dígitos (domínio válido: 8..9)
 *   Limite inferior inválido:  7  (abaixo do mínimo)
 *   Limite inferior válido:    8  (mínimo aceito)
 *   Limite superior válido:    9  (máximo aceito)
 *   Limite superior inválido: 10  (acima do máximo)
 *
 * Entrada: Primeiro dígito para strlen == 9 (deve ser '9')
 *   Limite inferior inválido: '8' (vizinho abaixo de '9')
 *   Limite válido:            '9'
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * CASOS DE TESTE – CAIXA-PRETA
 * ─────────────────────────────────────────────────────────────────────────────
 *
 * ID    | Entrada       | Classes | Saída esperada
 * CT01  | '12345678'    | 1, 6, 7 | true  (8 dígitos válidos)
 * CT02  | '912345678'   | 1, 4, 7 | true  (9 dígitos, primeiro '9')
 * CT03  | '1234567'     | 2       | false (7 dígitos — abaixo do limite)
 * CT04  | '9123456789'  | 3       | false (10 dígitos — acima do limite)
 * CT05  | ''            | 2       | false (string vazia — strlen = 0)
 * CT06  | '012345678'   | 1, 5    | false (9 dígitos, primeiro '0')
 * CT07  | '812345678'   | 1, 5    | false (9 dígitos, primeiro '8')
 * CT08  | '11111111'    | 1, 6, 8 | false (8 dígitos, todos '1')
 * CT09  | '999999999'   | 1, 4, 8 | false (9 dígitos, todos '9')
 * CT10  | '12121212'    | 1, 6, 7 | true  (alternados, não são todos iguais)
 *
 * ─────────────────────────────────────────────────────────────────────────────
 * TESTES COMPLEMENTARES – CAIXA-BRANCA
 * ─────────────────────────────────────────────────────────────────────────────
 *
 * Decisões e branches:
 *   D1 – V1: strlen < 8 || strlen > 9  (Branch TRUE = inválido, Branch FALSE = válido)
 *   D2 – V2: strlen == 9 && primeiroDigito != '9'  (condição composta — ver MC/DC)
 *   D3 – V3: preg_match('/^(.)\1*$/', ...)  (Branch TRUE = inválido, FALSE = válido)
 *
 * Tabela-verdade de D2 (C1 = strlen==9; C2 = primeiroDigito!='9'):
 *   Linha | C1 | C2 | D2 | Exemplo      | Caso
 *     1   |  V |  V |  V | '012345678'  | CT06
 *     2   |  V |  F |  F | '912345678'  | CT02
 *     3   |  F |  V |  F | '12345678'   | CT01 — strlen 8, primeiro '1'
 *     4   |  F |  F |  F | '12345678'   | CT01 — strlen 8, primeiro '1'
 *
 * Pares de independência para MC/DC:
 *   C1 determina D2: linhas 1 e 3 (C2=V, C1 muda: V→F, D2: V→F)
 *   C2 determina D2: linhas 1 e 2 (C1=V, C2 muda: V→F, D2: V→F)
 * Conjunto mínimo MC/DC: {CT01, CT02, CT06}
 *
 * Casos complementares caixa-branca:
 *   CB01 – 7 dígitos todos iguais: D1 e D3 ambas falsas → duas mensagens de erro
 *   CB02 – 9 dígitos, primeiro '0', todos iguais: D2 e D3 ambas verdadeiras → dois erros
 *   CB03 – getMessage() sem erros → retorna null (sem chamada prévia de isValid)
 *   CB04 – nomeCampo aparece nas mensagens de erro (rastreabilidade)
 *   CB05 – nomeCampo null não lança exceção (contrato legado)
 */

use iEducar\Modules\Educacenso\Validator\Telefone;
use Tests\TestCase;

uses(TestCase::class);

// ═════════════════════════════════════════════════════════════════════════════
// CAIXA-PRETA — Particionamento de Equivalência + Análise de Valor Limite
// ═════════════════════════════════════════════════════════════════════════════

// CT01 – Classes (1, 6, 7) — Limite inferior válido (8 dígitos)
test('CT01 [AVL] 8 digitos validos sem restricao de primeiro digito retorna true', function () {
    $validator = new Telefone('Telefone', '12345678');

    $this->assertTrue($validator->isValid());
});

// CT02 – Classes (1, 4, 7) — Limite superior válido (9 dígitos, primeiro '9')
test('CT02 [AVL] 9 digitos com primeiro digito 9 retorna true', function () {
    $validator = new Telefone('Telefone', '912345678');

    $this->assertTrue($validator->isValid());
});

// CT03 – Classe (2) — Limite inferior inválido (7 dígitos)
test('CT03 [AVL] 7 digitos abaixo do limite minimo retorna false', function () {
    $validator = new Telefone('Telefone', '1234567');

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString(
        'deve possuir de 8 a 9 números',
        implode(' ', $validator->getMessage())
    );
});

// CT04 – Classe (3) — Limite superior inválido (10 dígitos)
test('CT04 [AVL] 10 digitos acima do limite maximo retorna false', function () {
    $validator = new Telefone('Telefone', '9123456789');

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString(
        'deve possuir de 8 a 9 números',
        implode(' ', $validator->getMessage())
    );
});

// CT05 – Classe (2) — String vazia (strlen = 0, extremo da partição inválida)
test('CT05 [PE] string vazia retorna false', function () {
    $validator = new Telefone('Telefone', '');

    $this->assertFalse($validator->isValid());
});

// CT06 – Classes (1, 5) — 9 dígitos, primeiro dígito '0' (inválido)
test('CT06 [PE] 9 digitos com primeiro digito 0 retorna false', function () {
    $validator = new Telefone('Telefone', '012345678');

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString(
        'o primeiro dígito deve ser o número 9',
        implode(' ', $validator->getMessage())
    );
});

// CT07 – Classes (1, 5) — 9 dígitos, primeiro dígito '8' (vizinho inválido de '9')
test('CT07 [AVL] 9 digitos com primeiro digito 8 vizinho invalido retorna false', function () {
    $validator = new Telefone('Telefone', '812345678');

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString(
        'o primeiro dígito deve ser o número 9',
        implode(' ', $validator->getMessage())
    );
});

// CT08 – Classes (1, 6, 8) — 8 dígitos, todos '1' (repetidos)
test('CT08 [PE] 8 digitos todos repetidos retorna false', function () {
    $validator = new Telefone('Telefone', '11111111');

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString(
        'não podem ser todos repetidos',
        implode(' ', $validator->getMessage())
    );
});

// CT09 – Classes (1, 4, 8) — 9 dígitos começando com '9', mas todos iguais
test('CT09 [PE] 9 digitos todos 9 repetidos retorna false', function () {
    $validator = new Telefone('Telefone', '999999999');

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString(
        'não podem ser todos repetidos',
        implode(' ', $validator->getMessage())
    );
});

// CT10 – Classes (1, 6, 7) — Dígitos alternados não são considerados "todos repetidos"
test('CT10 [PE] digitos alternados nao sao todos repetidos retorna true', function () {
    $validator = new Telefone('Telefone', '12121212');

    $this->assertTrue($validator->isValid());
});

// ═════════════════════════════════════════════════════════════════════════════
// CAIXA-BRANCA — MC/DC para D2 (C1 = strlen==9; C2 = primeiroDigito!='9')
// ═════════════════════════════════════════════════════════════════════════════

// MC/DC linha 1: C1=V, C2=V → D2=V (já coberto por CT06, confirmado aqui)
// MC/DC linha 2: C1=V, C2=F → D2=F (já coberto por CT02)
// MC/DC linha 3: C1=F, C2=V → D2=F — C1 determina o resultado
test('[MCDC] 8 digitos com primeiro diferente de 9 retorna true (C1=F determina D2=F)', function () {
    // strlen=8 → C1=FALSE; mesmo com primeiro != '9' (C2=TRUE), D2 é FALSE
    $validator = new Telefone('Telefone', '12345678');

    $this->assertTrue($validator->isValid());
});

// ═════════════════════════════════════════════════════════════════════════════
// CAIXA-BRANCA — Complementares (acumulação de erros e interface pública)
// ═════════════════════════════════════════════════════════════════════════════

// CB01 – 7 dígitos todos repetidos: D1 (strlen inválido) E D3 (repetidos) ativadas
// Detectado via análise estrutural: isValid() não interrompe no primeiro erro
test('CB01 [CB] 7 digitos todos repetidos acumulam erros de comprimento e repeticao', function () {
    $validator = new Telefone('Telefone', '1111111'); // strlen=7, todos '1'

    $this->assertFalse($validator->isValid());

    $messages = implode(' ', $validator->getMessage());
    $this->assertStringContainsString('deve possuir de 8 a 9 números', $messages);
    $this->assertStringContainsString('não podem ser todos repetidos', $messages);
});

// CB02 – 9 dígitos, primeiro '0', todos iguais: D2 e D3 ativadas simultaneamente
test('CB02 [CB] 9 digitos com primeiro invalido e todos repetidos acumulam dois erros', function () {
    $validator = new Telefone('Telefone', '000000000'); // strlen=9, primeiro='0', repetidos

    $this->assertFalse($validator->isValid());

    $messages = implode(' ', $validator->getMessage());
    $this->assertStringContainsString('o primeiro dígito deve ser o número 9', $messages);
    $this->assertStringContainsString('não podem ser todos repetidos', $messages);
});

// CB03 – Sem erros: getMessage() retorna null (propriedade não foi populada)
test('CB03 [CB] telefone valido nao popula getMessage retornando null', function () {
    $validator = new Telefone('Telefone', '912345678');

    $this->assertTrue($validator->isValid());
    $this->assertNull($validator->getMessage());
});

// CB04 – nomeCampo aparece nas mensagens de erro (rastreabilidade funcional)
test('CB04 [CB] mensagem de erro contem o nome do campo configurado', function () {
    $nomeCampo = 'TelefoneResidencial';
    $validator = new Telefone($nomeCampo, '123');

    $validator->isValid();

    $this->assertStringContainsString(
        $nomeCampo,
        implode(' ', $validator->getMessage())
    );
});

// CB05 – nomeCampo null não lança exceção (contrato legado aceito pelo construtor)
test('CB05 [CB] nome de campo null nao lanca excecao', function () {
    $validator = new Telefone(null, '123');

    $this->assertFalse($validator->isValid());
    $this->assertNotNull($validator->getMessage());
});
