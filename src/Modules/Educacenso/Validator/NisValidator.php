<?php

namespace iEducar\Modules\Educacenso\Validator;

class NisValidator implements EducacensoValidator
{
    private $message;

    private $nis;

    public function __construct(string $nis)
    {
        $this->nis = $nis;
    }

    public function isValid(): bool
    {
        if ($this->nis === '') {
            return true;
        }

        if ($this->isOnlyZeroDigits()) {
            $this->message = 'Os números do campo: NIS (PIS/PASEP) não podem ser todos zeros.';

            return false;
        }

        if ($this->hasInvalidLength()) {
            $this->message = 'O NIS (PIS/PASEP) da pessoa deve conter 11 dígitos.';

            return false;
        }

        if ($this->hasNonNumericCharacters()) {
            $this->message = 'O NIS (PIS/PASEP) da pessoa deve conter apenas números.';

            return false;
        }

        return true;
    }

    private function isOnlyZeroDigits(): bool
    {
        return preg_match('/^(0)\1*$/u', $this->nis);
    }

    private function hasInvalidLength(): bool
    {
        return strlen($this->nis) !== 11;
    }

    private function hasNonNumericCharacters(): bool
    {
        return !ctype_digit($this->nis);
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }
}
