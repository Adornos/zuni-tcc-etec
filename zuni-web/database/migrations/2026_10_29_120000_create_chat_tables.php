<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Apenas cria tabelas novas; não altera nem apaga dados existentes.
     */
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();

            $table->string('type', 30);

            // Unicidade por par de participantes: "{type}:{menorId}:{maiorId}".
            $table->string('pair_key', 80)->unique();

            $table->foreignId('initiated_by')->constrained('users')->restrictOnDelete();

            // Encerramento (somente coordenador). Reabrir zera os dois campos.
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();

            // Denormalizados para ordenar/mostrar a lista sem varrer messages.
            $table->unsignedBigInteger('last_message_id')->nullable();
            $table->timestamp('last_message_at')->nullable();

            $table->timestamps();

            $table->index(['type', 'last_message_at']);
        });

        Schema::create('conversation_participants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('conversation_id')->constrained('conversations')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();

            $table->timestamp('joined_at')->useCurrent();

            // Arquivamento individual.
            $table->timestamp('archived_at')->nullable();

            // Ponteiros de entrega e leitura (um id cobre todas as mensagens anteriores).
            $table->unsignedBigInteger('last_delivered_message_id')->nullable();
            $table->unsignedBigInteger('last_read_message_id')->nullable();
            $table->timestamp('last_read_at')->nullable();

            $table->timestamps();

            $table->unique(['conversation_id', 'user_id']);
            $table->index(['user_id', 'archived_at']);
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('conversation_id')->constrained('conversations')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->restrictOnDelete();

            $table->text('body');

            // Link interno opcional (um por mensagem): 'report' | 'student'.
            $table->string('link_type', 20)->nullable();
            $table->unsignedBigInteger('link_id')->nullable();

            // Idempotência: reenvios do mesmo envio não duplicam a mensagem.
            $table->string('client_uuid', 36)->nullable();

            $table->timestamps();

            $table->index(['conversation_id', 'id']);
            $table->unique(['conversation_id', 'sender_id', 'client_uuid'], 'messages_idempotency_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversation_participants');
        Schema::dropIfExists('conversations');
    }
};
