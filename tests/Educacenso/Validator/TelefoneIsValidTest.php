<?php

use iEducar\Modules\Educacenso\Validator\Telefone;
use Tests\TestCase;

uses(TestCase::class);

test('CT01 [AVL] 8 digitos validos sem restricao de primeiro digito retorna true', function () {
    $validator = new Telefone('Telefone', '12345678');

    $this->assertTrue($validator->isValid());
});

test('CT02 [AVL] 9 digitos com primeiro digito 9 retorna true', function () {
    $validator = new Telefone('Telefone', '912345678');

    $this->assertTrue($validator->isValid());
});

test('CT03 [AVL] 7 digitos abaixo do limite minimo retorna false', function () {
    $validator = new Telefone('Telefone', '1234567');

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString(
        'deve possuir de 8 a 9 números',
        implode(' ', $validator->getMessage())
    );
});

test('CT04 [AVL] 10 digitos acima do limite maximo retorna false', function () {
    $validator = new Telefone('Telefone', '9123456789');

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString(
        'deve possuir de 8 a 9 números',
        implode(' ', $validator->getMessage())
    );
});

test('CT05 [PE] string vazia retorna false', function () {
    $validator = new Telefone('Telefone', '');

    $this->assertFalse($validator->isValid());
});

test('CT06 [PE] 9 digitos com primeiro digito 0 retorna false', function () {
    $validator = new Telefone('Telefone', '012345678');

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString(
        'o primeiro dígito deve ser o número 9',
        implode(' ', $validator->getMessage())
    );
});

test('CT07 [AVL] 9 digitos com primeiro digito 8 vizinho invalido retorna false', function () {
    $validator = new Telefone('Telefone', '812345678');

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString(
        'o primeiro dígito deve ser o número 9',
        implode(' ', $validator->getMessage())
    );
});

test('CT08 [PE] 8 digitos todos repetidos retorna false', function () {
    $validator = new Telefone('Telefone', '11111111');

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString(
        'não podem ser todos repetidos',
        implode(' ', $validator->getMessage())
    );
});

test('CT09 [PE] 9 digitos todos 9 repetidos retorna false', function () {
    $validator = new Telefone('Telefone', '999999999');

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString(
        'não podem ser todos repetidos',
        implode(' ', $validator->getMessage())
    );
});

test('CT10 [PE] digitos alternados nao sao todos repetidos retorna true', function () {
    $validator = new Telefone('Telefone', '12121212');

    $this->assertTrue($validator->isValid());
});

test('[MCDC] 8 digitos com primeiro diferente de 9 retorna true (C1=F determina D2=F)', function () {
    $validator = new Telefone('Telefone', '12345678');

    $this->assertTrue($validator->isValid());
});

test('CB01 [CB] 7 digitos todos repetidos acumulam erros de comprimento e repeticao', function () {
    $validator = new Telefone('Telefone', '1111111'); 

    $this->assertFalse($validator->isValid());

    $messages = implode(' ', $validator->getMessage());
    $this->assertStringContainsString('deve possuir de 8 a 9 números', $messages);
    $this->assertStringContainsString('não podem ser todos repetidos', $messages);
});

test('CB02 [CB] 9 digitos com primeiro invalido e todos repetidos acumulam dois erros', function () {
    $validator = new Telefone('Telefone', '000000000'); 

    $this->assertFalse($validator->isValid());

    $messages = implode(' ', $validator->getMessage());
    $this->assertStringContainsString('o primeiro dígito deve ser o número 9', $messages);
    $this->assertStringContainsString('não podem ser todos repetidos', $messages);
});

test('CB03 [CB] telefone valido nao popula getMessage retornando null', function () {
    $validator = new Telefone('Telefone', '912345678');

    $this->assertTrue($validator->isValid());
    $this->assertNull($validator->getMessage());
});

test('CB04 [CB] mensagem de erro contem o nome do campo configurado', function () {
    $nomeCampo = 'TelefoneResidencial';
    $validator = new Telefone($nomeCampo, '123');

    $validator->isValid();

    $this->assertStringContainsString(
        $nomeCampo,
        implode(' ', $validator->getMessage())
    );
});

test('CB05 [CB] nome de campo null nao lanca excecao', function () {
    $validator = new Telefone(null, '123');

    $this->assertFalse($validator->isValid());
    $this->assertNotNull($validator->getMessage());
});