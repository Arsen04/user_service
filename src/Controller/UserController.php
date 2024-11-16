<?php

namespace App\Controller;

use App\Http\Response;
use App\Repository\UserRepositoryInterface;
use App\View\UserView;
use Psr\Http\Message\StreamInterface;

class UserController
{
    private UserRepositoryInterface $userRepository;

    /**
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @return StreamInterface
     */
    public function index(): StreamInterface
    {
        $response = new Response();
        $userCollection = $this->userRepository->findAll();
        $formatedUserCollection = [];

        if (count($userCollection) > 0) {
            foreach ($userCollection as $user) {
                $formatedUserCollection[] = UserView::formatUser($user);
            }
            $message = 'Successfully retrieved users.';
        } else {
            $message = Response::NO_RECORDS_MESSAGE;
        }

        $data = [
            'data'    => $formatedUserCollection,
            'message' => $message,
            'status'  => Response::SUCCESS_STATUS_MESSAGE
        ];

        return $response
            ->withJson($data, Response::STATUS_OK)
            ->send();
    }

    /**
     * @return bool|string
     */
    public function createUser(): bool|string
    {
        return json_encode('User Created');
    }
}