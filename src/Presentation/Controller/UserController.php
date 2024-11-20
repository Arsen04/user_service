<?php

namespace App\Presentation\Controller;

use App\Application\DTO\Formatters\UserResponseFormatter;
use App\Application\UseCases\CreateUser;
use App\Application\UseCases\GetUser;
use App\Application\UseCases\GetUserList;
use App\Application\UseCases\UpdateUser;
use App\Domain\Exceptions\InvalidEmailException;
use App\Infrastructure\Logging\Logger;
use App\Presentation\Http\Response;
use App\Presentation\View\UserView;
use App\Shared\Exceptions\RecordExistsException;
use App\Shared\Exceptions\RecordNotFoundException;
use Exception;
use Monolog\DateTimeImmutable;
use Psr\Http\Message\StreamInterface;

class UserController
{
    private Logger $logger;
    private CreateUser $createUser;
    private UpdateUser $updateUser;
    private GetUser $getUser;
    private GetUserList $getUserList;

    /**
     * @param Logger $logger
     * @param CreateUser $createUser
     * @param UpdateUser $updateUser
     * @param GetUser $getUser
     * @param GetUserList $getUserList
     */
    public function __construct(
        Logger $logger,
        CreateUser $createUser,
        UpdateUser $updateUser,
        GetUser $getUser,
        GetUserList $getUserList
    ) {
        $this->logger = $logger;
        $this->createUser = $createUser;
        $this->updateUser = $updateUser;
        $this->getUser = $getUser;
        $this->getUserList = $getUserList;
    }

    /**
     * @return StreamInterface
     */
    public function getUserList(): StreamInterface
    {
        $response = new Response();
        $newDate = new DateTimeImmutable(false);

        try {
            $formatedUserCollection = [];
            $userCollection = $this->getUserList->execute();
            foreach ($userCollection as $user) {
                $formatedUserCollection[] = UserView::formatUser($user);
            }
            $message = 'Successfully retrieved users.';
            $status = Response::SUCCESS_STATUS_MESSAGE;
        } catch (RecordNotFoundException $exception) {
            $message = Response::NO_RECORDS_MESSAGE;
            $errorMessage = [$exception->getMessage()];
            $status = Response::FAILED_STATUS_FAILURE;
            $this->logger->warning(
                $message,
                [
                    'error' => $errorMessage,
                    'date' => $newDate->format('d-m-Y H:i:s')
                ]
            );
        } catch (Exception $exception) {
            $message = "Unable to get users.";
            $errorMessage = [$exception->getMessage()];
            $status = Response::FAILED_STATUS_FAILURE;
            $this->logger->error(
                $message,
                [
                    'error' => $errorMessage,
                    'date' => $newDate->format('d-m-Y H:i:s')
                ]
            );
        }
        $data = UserResponseFormatter::format($formatedUserCollection, $message, $status);

        return $response
            ->withJson($data, Response::STATUS_OK)
            ->send();
    }

    /**
     * @param int $id
     * @return StreamInterface
     * @throws Exception
     */
    public function getUser(int $id): StreamInterface
    {
        $response = new Response();
        $newDate = new DateTimeImmutable(false);

        $formattedUser = [];
        try {
            $user = $this->getUser->execute($id);
            $formattedUser = UserView::formatUser($user);
            $message = "Successfully retrieved user.";
            $status = Response::SUCCESS_STATUS_MESSAGE;
        } catch (RecordNotFoundException $exception) {
            $message = Response::NO_RECORDS_MESSAGE;
            $errorMessage = [$exception->getMessage()];
            $status = Response::FAILED_STATUS_FAILURE;
            $this->logger->warning(
                $message,
                [
                    'error' => $errorMessage,
                    'date' => $newDate->format('d-m-Y H:i:s')
                ]
            );
        } catch (Exception $exception) {
            $message = "Unable to get user.";
            $errorMessage = [$exception->getMessage()];
            $status = Response::FAILED_STATUS_FAILURE;
            $this->logger->error(
                $message,
                [
                    'error' => $errorMessage,
                    'date' => $newDate->format('d-m-Y H:i:s')
                ]
            );
        }
        $data = UserResponseFormatter::format($formattedUser, $message, $status);

        return $response
            ->withJson($data, Response::STATUS_OK)
            ->send();
    }

    /**
     * @param object $userData
     * @return bool|string
     */
    public function createUser(object $userData): bool|string
    {
        $response = new Response();
        $newDate = new DateTimeImmutable(false);

        try {
            $user = $this->createUser->execute($userData);
            $formattedUser = UserView::formatUser($user);
            $message = "User created successfully.";
            $status = Response::SUCCESS_STATUS_MESSAGE;
        } catch (RecordExistsException|InvalidEmailException $exception) {
            $formattedUser = [];
            $message = $exception->getMessage();
            $errorMessage = [$exception->getMessage()];
            $status = Response::FAILED_STATUS_FAILURE;
            $this->logger->warning(
                $message,
                [
                    'error' => $errorMessage,
                    'date' => $newDate->format('d-m-Y H:i:s')
                ]
            );
        } catch (Exception $exception) {
            $formattedUser = [];
            $message = "Failed to create the user.";
            $errorMessage = [$exception->getMessage()];
            $status = Response::FAILED_STATUS_FAILURE;
            $this->logger->error(
                $message,
                [
                    'error' => $errorMessage,
                    'date' => $newDate->format('d-m-Y H:i:s')
                ]
            );
        }
        $data = UserResponseFormatter::format($formattedUser, $message, $status);

        return $response
            ->withJson($data, Response::STATUS_OK)
            ->send();
    }

    /**
     * @param int $id
     * @param object $userData
     * @return bool|string
     */
    public function updateUser(int $id, object $userData): bool|string
    {
        $response = new Response();
        $newDate = new DateTimeImmutable(false);

        try {
            $user = $this->updateUser->execute($id, $userData);
            $formattedUser = UserView::formatUser($user);
            $message = "User updated successfully.";
            $status = Response::SUCCESS_STATUS_MESSAGE;
        } catch (RecordExistsException $exception) {
            $formattedUser = [];
            $message = $exception->getMessage();
            $errorMessage = [$exception->getMessage()];
            $status = Response::FAILED_STATUS_FAILURE;
            $this->logger->error(
                $message,
                [
                    'error' => $errorMessage,
                    'date' => $newDate->format('d-m-Y H:i:s')
                ]
            );
        } catch (Exception $exception) {
            $formattedUser = [];
            $message = "Failed to update the user.";
            $errorMessage = [$exception->getMessage()];
            $status = Response::FAILED_STATUS_FAILURE;
            $this->logger->error(
                $message,
                [
                    'error' => $errorMessage,
                    'date' => $newDate->format('d-m-Y H:i:s')
                ]
            );
        }
        $data = UserResponseFormatter::format($formattedUser, $message, $status);

        return $response
            ->withJson($data, Response::STATUS_OK)
            ->send();
    }
}