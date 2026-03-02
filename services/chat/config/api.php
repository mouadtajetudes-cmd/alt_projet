<?php
declare(strict_types=1);

use alt\api\actions\GetMessagesAction;
use alt\api\actions\CreateMessageAction;
use alt\api\actions\GetConversationsAction;
use alt\api\actions\GetConversationByIdAction;
use alt\api\actions\CreateConversationAction;
use alt\api\middlewares\AuthMiddleware;
use alt\core\application\ports\api\MessageServiceInterface;
use alt\core\application\ports\api\ConversationServiceInterface;

return [
    GetMessagesAction::class => function ($c) {
        return new GetMessagesAction(
            $c->get(MessageServiceInterface::class)
        );
    },

    CreateMessageAction::class => function ($c) {
        return new CreateMessageAction(
            $c->get(MessageServiceInterface::class)
        );
    },

    GetConversationsAction::class => function ($c) {
        return new GetConversationsAction(
            $c->get(ConversationServiceInterface::class)
        );
    },

    GetConversationByIdAction::class => function ($c) {
        return new GetConversationByIdAction(
            $c->get(ConversationServiceInterface::class)
        );
    },

    CreateConversationAction::class => function ($c) {
        return new CreateConversationAction(
            $c->get(ConversationServiceInterface::class)
        );
    },
];
