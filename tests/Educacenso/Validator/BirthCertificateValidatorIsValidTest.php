<?php

use iEducar\Modules\Educacenso\Validator\BirthCertificateValidator;
use Tests\TestCase;

uses(TestCase::class);

const MESSAGE_M1 = 'O campo: Tipo certidão civil (novo formato) possui valor inválido';
const MESSAGE_M2 = 'O campo: Tipo certidão civil (novo formato) foi preenchido com o ano inválido. O número localizado nas posições de 11 a 14, não pode ser anterior ao ano de nascimento e nem posterior ao ano corrente';


test('CB01 - Decisao D1 verdadeira por A=V e B=F (caminho a-c-f)', function () {
    $birthCertificate = '520131452020152142145454545678901';
    $birthDate = '2016-05-20';

    $validator = new BirthCertificateValidator($birthCertificate, $birthDate);

    $this->assertFalse($validator->validateCertificateLength(), 'Condição A deve ser V (validateCertificateLength falso)');
    $this->assertTrue($validator->validateCertificateDigits(), 'Condição B deve ser F (validateCertificateDigits verdadeiro)');

    $this->assertFalse($validator->isValid());
    $this->assertSame(MESSAGE_M1, $validator->getMessage());
});

test('CB02 - Decisao D1 verdadeira por A=F e B=V (caminho a-b-e-f)', function () {
    $birthCertificate = '12345678902020567890123456789A12';
    $birthDate = '2019-06-24';

    $validator = new BirthCertificateValidator($birthCertificate, $birthDate);

    $this->assertTrue($validator->validateCertificateLength(), 'Condição A deve ser F (validateCertificateLength verdadeiro)');
    $this->assertFalse($validator->validateCertificateDigits(), 'Condição B deve ser V (validateCertificateDigits falso)');

    $this->assertFalse($validator->isValid());
    $this->assertSame(MESSAGE_M1, $validator->getMessage());
});

test('CB03 - Decisao D1 falsa e D2 verdadeira (caminho a-b-d-h-i)', function () {
    $birthCertificate = '52013145202018131452099988866688';
    $birthDate = '2020-07-05';

    $validator = new BirthCertificateValidator($birthCertificate, $birthDate);

    $this->assertTrue($validator->validateCertificateLength(), 'Condição A deve ser F (validateCertificateLength verdadeiro)');
    $this->assertTrue($validator->validateCertificateDigits(), 'Condição B deve ser F (validateCertificateDigits verdadeiro)');
    $this->assertFalse($validator->validateCertificateYear(), 'Condição C deve ser V (validateCertificateYear falso)');

    $this->assertFalse($validator->isValid());
    $this->assertSame(MESSAGE_M2, $validator->getMessage());
});

test('CB04 - Decisao D1 falsa e D2 falsa (caminho a-b-d-g)', function () {
    $birthCertificate = '52013145202023131452099988866688';
    $birthDate = '2023-08-26';

    $validator = new BirthCertificateValidator($birthCertificate, $birthDate);

    $this->assertTrue($validator->validateCertificateLength(), 'Condição A deve ser F (validateCertificateLength verdadeiro)');
    $this->assertTrue($validator->validateCertificateDigits(), 'Condição B deve ser F (validateCertificateDigits verdadeiro)');
    $this->assertTrue($validator->validateCertificateYear(), 'Condição C deve ser F (validateCertificateYear verdadeiro)');

    $this->assertTrue($validator->isValid());
    $this->assertNull($validator->getMessage());
});
