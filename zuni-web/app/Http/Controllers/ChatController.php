<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Services\Chat\ChatLinkService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    /**
     * Abre o recurso interno citado numa mensagem. Nunca usa URL vinda do navegador:
     * a rota é resolvida aqui, para o papel de quem abre, depois de revalidar
     * participação na conversa e acesso atual ao recurso.
     */
    public function openLink(Message $message, ChatLinkService $links)
    {
        Gate::authorize('view', $message->conversation);

        abort_unless($message->hasLink(), 404);

        $user = Auth::user();
        $target = $links->resolveFor($user, $message->link_type, $message->link_id);

        if ($target === null) {
            Log::warning('chat.link.denied', ['message_id' => $message->id, 'user_id' => $user->id]);

            abort(403, 'Você não tem acesso a este recurso.');
        }

        return redirect()->route($target['route'], $target['params']);
    }
}
