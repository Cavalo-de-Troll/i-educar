<?php

namespace iEducar\Modules\Educacenso\Validator;

class NisValidator implements EducacensoValidator
{
    private $message;

    private ?string $nis;

    public function __construct(?string $nis)
    {
        $this->nis = $nis !== null ? trim($nis) : null;
    }

    public function isValid(): bool
    {
        if ($this->isOnlyZeroDigits()) {
            $this->message = 'Os números do campo: NIS (PIS/PASEP) não podem ser todos zeros.';

            return false;
        }
        if ($this->nis === null || trim($this->nis) === '') {
            return true;
        }
        if (strlen($this->nis) !== 11) {
        $this->message = 'O NIS deve ter 11 dígitos.';
        return false;
    }
    if (empty($this->nis)) { 
        return true; 
        }

    if (!ctype_digit($this->nis)) {
        $this->message = 'O NIS deve conter apenas números.';
        return false;
    }

        return true;
    }

    private function isOnlyZeroDigits(): bool
    {
        return preg_match('/^(0)\1*$/u', $this->nis);
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }
}
