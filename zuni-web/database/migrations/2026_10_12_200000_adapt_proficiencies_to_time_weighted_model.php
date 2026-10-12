<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adapta as tabelas ao modelo de média ponderada pelo tempo. Somente adiciona colunas:
     * nada é apagado e as tabelas/colunas antigas continuam existindo.
     *
     *  - proficiency_observations.level: nível registrado (neutro ± intensidade). `weight`
     *    passa a ser o peso FIXO da observação; requested_delta/applied_delta/value_* passam
     *    a descrever o efeito no instante do registro. Linhas anteriores à migração mantêm os
     *    números do modelo antigo, mas o valor atual é sempre recalculado a partir de
     *    nível + peso + data, então elas entram corretamente no novo modelo.
     *  - student_proficiencies: cache do valor e da confiança.
     *    base_value passa a ser o valor inicial (prior) da série.
     *  - proficiency_recalculation_logs: não é mais usada (anular não reprocessa mais em cadeia);
     *    a tabela é mantida só como registro histórico.
     */
    public function up(): void
    {
        Schema::table('proficiency_observations', function (Blueprint $table) {
            $table->decimal('level', 6, 4)->nullable()->after('intensity');
        });

        Schema::table('student_proficiencies', function (Blueprint $table) {
            $table->decimal('effective_count', 8, 4)->default(0)->after('value');
            $table->unsignedInteger('observations_count')->default(0)->after('effective_count');
            $table->timestamp('calculated_at')->nullable()->after('last_observed_at');
        });

        $neutral = (float) config('proficiency.neutral', 5.0);
        $k = (float) config('proficiency.level_per_intensity', 1.0);
        $min = (float) config('proficiency.scale.min', 0.0);
        $max = (float) config('proficiency.scale.max', 10.0);

        // Preenche o nível das observações já existentes e fixa o peso delas.
        DB::table('proficiency_observations')->orderBy('id')->chunkById(500, function ($rows) use ($neutral, $k, $min, $max) {
            foreach ($rows as $row) {
                $sign = $row->direction === 'positive' ? 1 : -1;
                $level = round(min($max, max($min, $neutral + $sign * (float) $row->intensity * $k)), 4);

                DB::table('proficiency_observations')->where('id', $row->id)->update([
                    'level' => $level,
                    'weight' => (float) config('proficiency.observation_weight', 1.0),
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_proficiencies', function (Blueprint $table) {
            $table->dropColumn(['effective_count', 'observations_count', 'calculated_at']);
        });

        Schema::table('proficiency_observations', function (Blueprint $table) {
            $table->dropColumn('level');
        });
    }
};
