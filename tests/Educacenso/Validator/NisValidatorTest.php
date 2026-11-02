<?php

use iEducar\Modules\Educacenso\Validator\NisValidator;
use Tests\TestCase;

uses(TestCase::class);

test('nis without repeated characters', function () {
    $validator = new NisValidator('12345678901');

    $this->assertTrue($validator->isValid());
});

test('nis with all characters zero', function () {
    $validator = new NisValidator('00000000000');

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('Os números do campo: NIS (PIS/PASEP) não podem ser todos zeros.', $validator->getMessage());
});

test('nis with all characters one', function () {
    $validator = new NisValidator('11111111111');

    $this->assertTrue($validator->isValid());
});

test('nis with null is valid', function () {
    $validator = new NisValidator(null);
    $this->assertTrue($validator->isValid());
});

test('nis with only spaces is valid', function () {
    $validator = new NisValidator('   ');
    $this->assertTrue($validator->isValid());
});

test('nis with spaces around is valid', function () {
    $validator = new NisValidator(' 12345678901 ');
    $this->assertTrue($validator->isValid());
});
test('empty nis is valid because the field is optional', function () {
    $validator = new NisValidator('');

    $this->assertTrue($validator->isValid());
});

test('nis with less than 11 digits is invalid', function () {
    $validator = new NisValidator('1234567890');

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('O NIS (PIS/PASEP) da pessoa deve conter 11 dígitos.', $validator->getMessage());
});

test('nis with more than 11 digits is invalid', function () {
    $validator = new NisValidator('123456789012');

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('O NIS (PIS/PASEP) da pessoa deve conter 11 dígitos.', $validator->getMessage());
});

test('nis with non numeric characters is invalid', function () {
    $validator = new NisValidator('1234567890a');

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('O NIS (PIS/PASEP) da pessoa deve conter apenas números.', $validator->getMessage());
});
