<?php

namespace Modules\Api\Http\Controllers;

use App\Models\Chat\Chat;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Modules\Api\Events\NewMessage;
use Modules\Api\Http\Requests\MessageRequest;
use Modules\Api\Http\Resources\ChatResource;
use Modules\Api\Repositories\ChatRepository;
use Modules\Api\Services\ChatService;

final class ChatController extends Controller
{
    protected ChatService $service;
    protected ChatRepository $repository;

    public function __construct(ChatService $service, ChatRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    /**
     * @api {get} /chats All chats
     * @apiName IndexChats
     * @apiGroup Chats
     *
     * @apiUse Token
     * @apiSuccess {Number} id Chat id.
     * @apiSuccess {Object} user Chat participant.
     * @apiSuccess {Number} user.i Chat participant's id.
     * @apiSuccess {String} user.nam Chat participant's name.
     * @apiSuccess {String} user.avatar Chat participant's avatar.
     * @apiSuccess {Object} lastMessage Chat last message.
     * @apiSuccess {Number} lastMessage.i Chat last message's id.
     * @apiSuccess {Number} lastMessage.userI Chat last message's author id.
     * @apiSuccess {String} lastMessage.tex Chat last message's text.
     * @apiSuccess {String} lastMessage.image Chat last message's image.
     * @apiSuccess {Date} lastMessage.createdA Chat last message's datetime.
     * @apiSuccess {Boolean} lastMessage.viewed Chat last message's viewed status.
     * @apiSuccess {Boolean} marked Chat is marked.
     */
    public function index(): JsonResponse
    {
        return response()->json(ChatResource::collection($this->repository->getForIndex()));
    }

    /**
     * @api {get} /chats/:id Show chat
     * @apiName ShowChat
     * @apiGroup Chats
     *
     * @apiUse Token
     * @apiSuccess {Number} id Chat id.
     * @apiSuccess {Object} user Chat participant.
     * @apiSuccess {Number} user.id Chat participant's id.
     * @apiSuccess {String} user.name Chat participant's name.
     * @apiSuccess {String} user.avatar Chat participant's avatar.
     * @apiSuccess {Object[]} messages Chat messages.
     * @apiSuccess {Number} messages.id Message id.
     * @apiSuccess {Number} messages.userId Message author id.
     * @apiSuccess {String} messages.text Message text.
     * @apiSuccess {String} messages.image Message image.
     * @apiSuccess {Date} messages.createdAt Message datetime.
     * @apiSuccess {Boolean} messages.viewed Message viewed status.
     * @apiSuccess {Boolean} marked Chat is marked.
     * @apiUse Pagination
     */
    public function show(Chat $chat): JsonResponse
    {
        $response = (new ChatResource($this->repository->getForShow($chat)))->enableListMessages();

        $this->service->markAsViewed($chat);

        return response()->json($response);
    }

    /**
     * @api {post} /users/:id/chat Find or start chat
     * @apiName FindOrStartChats
     * @apiGroup Chats
     *
     * @apiUse Token
     * @apiSuccess {Number} chatId Chat id.
     */
    public function start(User $user): JsonResponse
    {
        $chat = $this->repository->findOrStartChat($user);

        return response()->json(['chatId' => $chat->id]);
    }

    /**
     * @api {post} /chats/:id Send message
     * @apiName SendMessage
     * @apiGroup Chats
     *
     * @apiUse Token
     * @apiParam {String} message Message text.
     *
     * @apiSuccess {String} message Sending status.
     */
    public function storeMessage(Chat $chat, MessageRequest $request): JsonResponse
    {
        $user = \Auth::user();
        $participant = $chat->participants()->where('id', '!=', $user->id)->first();

        abort_if(
            $participant->blacklist()->find($user->id) !== null,
            Response::HTTP_FORBIDDEN,
            __('tattoo.chats.blacklist.you')
        );
        abort_if(
            $user->blacklist()->find($participant->id) !== null,
            Response::HTTP_FORBIDDEN,
            __('tattoo.chats.blacklist.participant')
        );

        $message = $this->repository->createMessage($chat, $request->input('message'), $request->file('media'));

        event(new NewMessage($message, $participant));

        return response()->json(['message' => 'Message has been sent.'], Response::HTTP_CREATED);
    }

    /**
     * @api {post} /chats/:id/mark Mark chat
     * @apiName MarkChat
     * @apiGroup Chats
     *
     * @apiUse Token
     * @apiSuccess {String} message Mark status message.
     * @apiSuccess {Boolean} marked Mark status.
     */
    public function toggleMark(Chat $chat): JsonResponse
    {
        $marked = $this->service->toggleMark($chat);
        $action = $marked ? 'marked' : 'unmarked';
        return response()->json([
            'message' => "Chat has been $action.",
            'marked' => $marked
        ]);
    }

    /**
     * @api {post} /chats/:id Delete chat
     * @apiName DeleteChat
     * @apiGroup Chats
     *
     * @apiUse Token
     * @apiSuccess {String} message Deleting status message.
     */
    public function destroy(Chat $chat): JsonResponse
    {
        $chat->delete();

        return response()->json(['message' => 'Chat has been deleted.']);
    }

    /**
     * @api {post} /broadcasting/auth Auth private channel
     * @apiName AuthBroadcasting
     * @apiGroup Chats
     *
     * @apiUse Token
     * @apiParam {String} channel_name Private channel name like `messages.${participantId}`.
     *
     * @apiSuccess {String} auth Auth token for Pusher.
     */
}
