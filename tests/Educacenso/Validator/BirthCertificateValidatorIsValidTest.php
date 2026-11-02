<?php

use iEducar\Modules\Educacenso\Validator\BirthCertificateValidator;
use Tests\TestCase;

uses(TestCase::class);

const MESSAGE_M1 = 'O campo: Tipo certidão civil (novo formato) possui valor inválido';
const MESSAGE_M2 = 'O campo: Tipo certidão civil (novo formato) foi preenchido com o ano inválido. O número localizado nas posições de 11 a 14, não pode ser anterior ao ano de nascimento e nem posterior ao ano corrente';

/*
 * Monta um número de matrícula seguindo a composição do padrão CNJ:
 * serventia (1-6) | acervo (7-8) | serviço (9-10) | ano (11-14) | tipo do livro (15)
 * | livro (16-20) | folha (21-23) | termo (24-30) | dígito verificador (31-32)
 * Os valores padrão formam uma certidão válida segundo a especificação.
 */
if (!function_exists('certidaoCP')) {
    function certidaoCP(
        string $ano = '2015',
        string $servico = '55',
        string $tipoLivro = '1',
        string $dv = 'XX',
        string $termo = '0005678'
    ): string {
        return '104539' . '01' . $servico . $ano . $tipoLivro . '00012' . '034' . $termo . $dv;
    }
}

/* ============================== CAIXA-BRANCA ============================== */

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

/* ============================== CAIXA-PRETA =============================== */

test('CP01 - Classes válidas (1)(2)(6)(7)(8)(15): certidão conforme a especificação', function () {
    $validator = new BirthCertificateValidator(certidaoCP(), '2014-11-20');

    $this->assertTrue($validator->isValid());
    $this->assertNull($validator->getMessage());
});

test('CP02 - Classe (3), off point inferior de R2: 31 caracteres', function () {
    $validator = new BirthCertificateValidator(certidaoCP(termo: '000567'), '2014-11-20');

    $this->assertFalse($validator->isValid());
    $this->assertSame(MESSAGE_M1, $validator->getMessage());
});

test('CP03 - Classe (17): dígito verificador "X1" não é numérico nem "XX"', function () {
    $validator = new BirthCertificateValidator(certidaoCP(dv: 'X1'), '2014-11-20');

    $this->assertFalse($validator->isValid());
    $this->assertSame(MESSAGE_M1, $validator->getMessage());
});

test('CP04 - Classe (9), off point inferior: código do serviço 54', function () {
    $validator = new BirthCertificateValidator(certidaoCP(servico: '54'), '2014-11-20');

    $this->assertFalse($validator->isValid());
    $this->assertSame(MESSAGE_M1, $validator->getMessage());
});

test('CP05 - Classe (9), off point superior: código do serviço 56', function () {
    $validator = new BirthCertificateValidator(certidaoCP(servico: '56'), '2014-11-20');

    $this->assertFalse($validator->isValid());
    $this->assertSame(MESSAGE_M1, $validator->getMessage());
});

test('CP06 - Classe (10), R7: tipo do livro 2 (certidão de casamento)', function () {
    $validator = new BirthCertificateValidator(certidaoCP(tipoLivro: '2'), '2014-11-20');

    $this->assertFalse($validator->isValid());
    $this->assertSame(MESSAGE_M1, $validator->getMessage());
});

test('CP07 - Classe (11), off point inferior: tipo do livro 0', function () {
    $validator = new BirthCertificateValidator(certidaoCP(tipoLivro: '0'), '2014-11-20');

    $this->assertFalse($validator->isValid());
    $this->assertSame(MESSAGE_M1, $validator->getMessage());
});

test('CP08 - On point de R5: ano do registro igual ao ano de nascimento', function () {
    $validator = new BirthCertificateValidator(certidaoCP(ano: '2015'), '2015-03-10');

    $this->assertTrue($validator->isValid());
    $this->assertNull($validator->getMessage());
});

test('CP09 - Classe (12), off point de R5: ano do registro um ano antes do nascimento', function () {
    $validator = new BirthCertificateValidator(certidaoCP(ano: '2015'), '2016-01-05');

    $this->assertFalse($validator->isValid());
    $this->assertSame(MESSAGE_M2, $validator->getMessage());
});

test('CP10 - On point de R6: ano do registro igual ao ano corrente', function () {
    $validator = new BirthCertificateValidator(certidaoCP(ano: date('Y')), '2020-01-15');

    $this->assertTrue($validator->isValid());
    $this->assertNull($validator->getMessage());
});

test('CP11 - Classe (13), off point de R6: ano do registro posterior ao ano corrente', function () {
    $anoSeguinte = (string) ((int) date('Y') + 1);
    $validator = new BirthCertificateValidator(certidaoCP(ano: $anoSeguinte), '2020-01-15');

    $this->assertFalse($validator->isValid());
    $this->assertSame(MESSAGE_M2, $validator->getMessage());
});

test('CP12 - Classe (18), R4: dois DVs diferentes para os mesmos 30 dígitos não podem ser ambos aceitos', function () {
    $comDv00 = new BirthCertificateValidator(certidaoCP(dv: '00'), '2014-11-20');
    $comDv01 = new BirthCertificateValidator(certidaoCP(dv: '01'), '2014-11-20');

    $this->assertFalse(
        $comDv00->isValid() && $comDv01->isValid(),
        'O DV é calculado a partir dos 30 dígitos anteriores: no máximo um dos dois pode estar correto'
    );
});
