<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Cpf implements Rule
{
    public function passes($attribute, $value)
    {
        // Remove caracteres não numéricos
        $value = preg_replace('/\D/', '', $value);

        // Verifica se o CPF tem exatamente 11 dígitos
        if (strlen($value) != 11) {
            return false;
        }

        // Verificação do primeiro dígito verificador
        $sum = 0;
        $weight = [10, 9, 8, 7, 6, 5, 4, 3, 2];
        for ($i = 0; $i < 9; $i++) {
            $sum += $value[$i] * $weight[$i];
        }
        $mod = $sum % 11;
        $firstDigit = $mod < 2 ? 0 : 11 - $mod;

        if ($value[9] != $firstDigit) {
            return false;
        }

        // Verificação do segundo dígito verificador
        $sum = 0;
        $weight = [11, 10, 9, 8, 7, 6, 5, 4, 3, 2];
        for ($i = 0; $i < 10; $i++) {
            $sum += $value[$i] * $weight[$i];
        }
        $mod = $sum % 11;
        $secondDigit = $mod < 2 ? 0 : 11 - $mod;

        return $value[10] == $secondDigit;
    }

    public function message()
    {
        return 'O CPF informado é inválido.';
    }
}
