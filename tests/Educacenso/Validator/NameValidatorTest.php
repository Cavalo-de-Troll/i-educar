<?php

use iEducar\Modules\Educacenso\Validator\NameValidator;
use Tests\TestCase;

uses(TestCase::class);

describe('caixa preta: NameValidator::isValid', function () {
    test('aceita um nome válido com caracteres acentuados', function () {
        $validator = new NameValidator('João da Conceição');

        $this->assertTrue($validator->isValid());
        $this->assertNull($validator->getMessage());
    });

    test('rejeita números e caracteres especiais com mensagem explicativa', function () {
        $validator = new NameValidator('João 123');

        $this->assertFalse($validator->isValid());
        $this->assertSame(
            'O Nome ou Nome Social não pode conter números ou caracteres especiais como: (0-9!@#\$%^&*?_~-.)',
            $validator->getMessage()
        );
    });

    test('rejeita quatro caracteres iguais consecutivos com mensagem explicativa', function () {
        $validator = new NameValidator('Lorem Ipsuuuum');

        $this->assertFalse($validator->isValid());
        $this->assertSame('Nome não pode ter a repetição de 4 caracteres seguidos.', $validator->getMessage());
    });

    test('aceita nomes de até 100 caracteres', function () {
        $validator = new NameValidator(str_repeat('Ab', 50));

        $this->assertTrue($validator->isValid());
    });

    test('rejeita nomes com mais de 100 caracteres', function () {
        $validator = new NameValidator(str_repeat('AB', 51));

        $this->assertFalse($validator->isValid());
        $this->assertSame('O Nome deve ter no máximo 100 caracteres.', $validator->getMessage());
    });
});

/** 
 * TESTE CAIXA BRANCA
 */

describe('caixa branca: decisões de NameValidator::isValid', function () {
    test('aceita três caracteres iguais consecutivos', function () {
        $validator = new NameValidator('Lorem Ipsuuum');

        $this->assertTrue($validator->isValid());
    });

    test('considera o tamanho após remover espaços nas extremidades', function () {
        $validator = new NameValidator(' ' . str_repeat('A', 100) . ' ');

        $this->assertTrue($validator->isValid());
    });

    test('prioriza a validação do tamanho antes das demais regras', function () {
        $validator = new NameValidator(str_repeat('1', 101));

        $this->assertFalse($validator->isValid());
        $this->assertSame('O Nome deve ter no máximo 100 caracteres.', $validator->getMessage());
    });

    test('rejeita nome vazio pela regra de caracteres permitidos', function () {
        $validator = new NameValidator('');

        $this->assertFalse($validator->isValid());
        $this->assertSame(
            'O Nome ou Nome Social não pode conter números ou caracteres especiais como: (0-9!@#\$%^&*?_~-.)',
            $validator->getMessage()
        );
    });
});
