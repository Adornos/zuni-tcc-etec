<?php

namespace Database\Seeders\Concerns;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Utilidades compartilhadas pelos seeders: senha padrão, documentos de teste e gravação idempotente
 * de usuários (rodar o seeder de novo atualiza os mesmos registros em vez de duplicá-los).
 */
trait SeedsPeople
{
    /** Senha de todos os usuários de teste. Calculada uma vez só (bcrypt é lento). */
    protected function password(): string
    {
        static $hash = null;

        return $hash ??= Hash::make('123456');
    }

    /**
     * CPF de teste com dígitos verificadores válidos, único para cada $seed.
     * Documento fictício: serve só para desenvolvimento.
     */
    protected function cpf(int $seed): string
    {
        $digits = array_map('intval', str_split(str_pad((string) (100000000 + ($seed * 1234567) % 899999999), 9, '0', STR_PAD_LEFT)));

        foreach ([10, 11] as $length) {
            $sum = 0;
            for ($i = 0; $i < $length - 1; $i++) {
                $sum += $digits[$i] * ($length - $i);
            }
            $rest = ($sum * 10) % 11;
            $digits[] = $rest === 10 ? 0 : $rest;
        }

        return sprintf('%d%d%d.%d%d%d.%d%d%d-%d%d', ...$digits);
    }

    /** RG de teste, único para cada $seed. */
    protected function rg(int $seed): string
    {
        return sprintf('%02d.%03d.%03d-%d', 10 + $seed % 80, ($seed * 137) % 1000, ($seed * 911) % 1000, $seed % 10);
    }

    /**
     * Cria ou atualiza o usuário pelo username. Usa forceFill porque `status` não é preenchível em massa
     * no Model (sem isso o status seria descartado e o usuário ficaria "pendente").
     */
    protected function upsertUser(string $username, array $attributes): User
    {
        $user = User::query()->firstOrNew(['username' => $username]);

        $user->forceFill($attributes + ['password' => $this->password()])->save();

        return $user;
    }
}
