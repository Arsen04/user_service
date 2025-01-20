<?php

namespace App\Presentation\Controller;

use App\Application\DTO\Formatters\UserResponseFormatter;
use App\Application\UseCases\Notification\NotifyUser;
use App\Application\UseCases\User\CreateUser;
use App\Application\UseCases\User\DeleteUser;
use App\Application\UseCases\User\GetUser;
use App\Application\UseCases\User\GetUserList;
use App\Application\UseCases\User\UpdateUser;
use App\Domain\Exceptions\InvalidEmailException;
use App\Infrastructure\Logging\Logger;
use App\Presentation\Http\Request;
use App\Presentation\Http\Response;
use App\Presentation\View\UserView;
use App\Shared\Exceptions\RecordExistsException;
use App\Shared\Exceptions\RecordNotFoundException;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Monolog\DateTimeImmutable;

class UserController
{
    private Logger $logger;
    private CreateUser $createUser;
    private UpdateUser $updateUser;
    private DeleteUser $deleteUser;
    private GetUser $getUser;
    private GetUserList $getUserList;
    private NotifyUser $notifyUser;

    /**
     * @param Logger $logger
     * @param CreateUser $createUser
     * @param UpdateUser $updateUser
     * @param DeleteUser $deleteUser
     * @param GetUser $getUser
     * @param GetUserList $getUserList
     * @param NotifyUser $notifyUser
     */
    public function __construct(
        Logger $logger,
        CreateUser $createUser,
        UpdateUser $updateUser,
        DeleteUser $deleteUser,
        GetUser $getUser,
        GetUserList $getUserList,
        NotifyUser $notifyUser
    ) {
        $this->logger = $logger;
        $this->createUser = $createUser;
        $this->updateUser = $updateUser;
        $this->deleteUser = $deleteUser;
        $this->getUser = $getUser;
        $this->getUserList = $getUserList;
        $this->notifyUser = $notifyUser;
    }

    /**
     * @return bool|string
     */
    public function getUserList(): bool|string
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
     * @return bool|string
     * @throws Exception
     */
    public function getUser(int $id): bool|string
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
     * @param Request $request
     * @return bool|string
     */
    public function createUser(Request $request): bool|string
    {
        $response = new Response();
        $newDate = new DateTimeImmutable(false);
        $userData = $request->getBody()->getContents();
        $userObject = json_decode($userData);

        try {
            $user = $this->createUser->execute($userObject);
            $formattedUser = UserView::formatUser($user);
            $this->notifyUser->execute($formattedUser);
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
        } catch (GuzzleException $exception) {
            $formattedUser = [];
            $message = "Failed to send an email.";
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
     * @param Request $request
     * @return bool|string
     */
    public function updateUser(int $id, Request $request): bool|string
    {
        $response = new Response();
        $newDate = new DateTimeImmutable(false);
        $userData = $request->getBody()->getContents();
        $userObject = json_decode($userData);

        try {
            $user = $this->updateUser->execute($id, $userObject);
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

    /**
     * @param int $id
     * @return bool|string
     */
    public function deleteUser(int $id): bool|string
    {
        $response = new Response();
        $newDate = new DateTimeImmutable(false);

        try {
            $user = $this->deleteUser->execute($id);
            $formattedUser = UserView::formatUser($user);
            $message = "User created successfully.";
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
            $message = "Failed to delete the user.";
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