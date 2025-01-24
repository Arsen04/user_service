<?php

namespace App\Presentation\Controller;

use App\Application\DTO\Formatters\UserResponseFormatter;
use App\Application\Requests\CreateUserRequest;
use App\Application\Requests\UpdateUserRequest;
use App\Application\UseCases\Notification\NotifyUser;
use App\Application\UseCases\User\CreateUser;
use App\Application\UseCases\User\DeleteUser;
use App\Application\UseCases\User\GetUser;
use App\Application\UseCases\User\GetUserList;
use App\Application\UseCases\User\UpdateUser;
use App\Application\Validators\JsonRequestValidator;
use App\Domain\Exceptions\InvalidEmailException;
use App\Infrastructure\Logging\Logger;
use App\Presentation\Http\Request;
use App\Presentation\Http\Response;
use App\Presentation\View\LetterView;
use App\Presentation\View\NotificationView;
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

        $formatedUserCollection = [];

        try {
            $userCollection = $this->getUserList->execute();
            foreach ($userCollection as $user) {
                $formatedUserCollection[] = UserView::formatUser($user);
            }
            $message = 'Successfully retrieved users.';
            $status = Response::SUCCESS_STATUS_MESSAGE;
            $statusCode = Response::STATUS_OK;
        } catch (RecordNotFoundException $exception) {
            $message = Response::NO_RECORDS_MESSAGE;
            $errorMessage = [$exception->getMessage()];
            $status = Response::FAILED_STATUS_FAILURE;
            $statusCode = Response::STATUS_NOT_FOUND;
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
            $statusCode = Response::STATUS_BAD_GATEWAY;
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
            ->withJson($data, $statusCode)
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
            $statusCode = Response::STATUS_OK;
        } catch (RecordNotFoundException $exception) {
            $message = Response::NO_RECORDS_MESSAGE;
            $errorMessage = [$exception->getMessage()];
            $status = Response::FAILED_STATUS_FAILURE;
            $statusCode = Response::STATUS_NOT_FOUND;
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
            $statusCode = Response::STATUS_BAD_GATEWAY;
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
            ->withJson($data, $statusCode)
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

        $formattedUser = [];

        try {
            JsonRequestValidator::validate(json_decode($userData, true), CreateUserRequest::rules());

            $user = $this->createUser->execute($userObject);
            $formattedUser = UserView::formatUser($user);

            $letterSubject = "Verify Your Account";
            $letterMessage = sprintf("Hello, %s! We are delighted to have you with us!", $user->getName());

            $formattedLetter = LetterView::formatLetter($letterSubject, $letterMessage);
            $formattedNotification = NotificationView::formatNotification($user, $formattedLetter);

            $this->notifyUser->execute($formattedNotification);
            $message = "User created successfully.";
            $status = Response::SUCCESS_STATUS_MESSAGE;
            $statusCode = Response::STATUS_CREATED;
        } catch (RecordExistsException|InvalidEmailException $exception) {
            $message = $exception->getMessage();
            $errorMessage = [$exception->getMessage()];
            $status = Response::FAILED_STATUS_FAILURE;
            $statusCode = Response::STATUS_BAD_REQUEST;
            $this->logger->warning(
                $message,
                [
                    'error' => $errorMessage,
                    'date' => $newDate->format('d-m-Y H:i:s')
                ]
            );
        } catch (GuzzleException $exception) {
            $message = "Failed to send an email.";
            $errorMessage = [$exception->getMessage()];
            $status = Response::SUCCESS_STATUS_MESSAGE;
            $statusCode = Response::STATUS_OK;
            $this->logger->error(
                $message,
                [
                    'error' => $errorMessage,
                    'date' => $newDate->format('d-m-Y H:i:s')
                ]
            );
        } catch (\InvalidArgumentException $exception) {
            $message = "The request body provided is invalid.";
            $errorMessage = [$exception->getMessage()];
            $status = Response::FAILED_STATUS_FAILURE;
            $statusCode = Response::STATUS_BAD_REQUEST;
            $this->logger->error(
                $message,
                [
                    'error' => $errorMessage,
                    'date' => $newDate->format('d-m-Y H:i:s')
                ]
            );
        } catch (Exception $exception) {
            $message = "Failed to create the user.";
            $errorMessage = [$exception->getMessage()];
            $status = Response::FAILED_STATUS_FAILURE;
            $statusCode = Response::STATUS_BAD_GATEWAY;
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
            ->withJson($data, $statusCode)
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

        $formattedUser = [];

        try {
            JsonRequestValidator::validate(json_decode($userData, true), UpdateUserRequest::rules());

            $user = $this->updateUser->execute($id, $userObject);
            $formattedUser = UserView::formatUser($user);
            $message = "User updated successfully.";
            $status = Response::SUCCESS_STATUS_MESSAGE;
            $statusCode = Response::STATUS_CREATED;
        } catch (RecordExistsException $exception) {
            $message = $exception->getMessage();
            $errorMessage = [$exception->getMessage()];
            $status = Response::FAILED_STATUS_FAILURE;
            $statusCode = Response::STATUS_BAD_REQUEST;
            $this->logger->error(
                $message,
                [
                    'error' => $errorMessage,
                    'date' => $newDate->format('d-m-Y H:i:s')
                ]
            );
        } catch (\InvalidArgumentException $exception) {
            $message = "The request body provided is invalid.";
            $errorMessage = [$exception->getMessage()];
            $status = Response::FAILED_STATUS_FAILURE;
            $statusCode = Response::STATUS_BAD_REQUEST;
            $this->logger->error(
                $message,
                [
                    'error' => $errorMessage,
                    'date' => $newDate->format('d-m-Y H:i:s')
                ]
            );
        } catch (Exception $exception) {
            $message = "Failed to update the user.";
            $errorMessage = [$exception->getMessage()];
            $status = Response::FAILED_STATUS_FAILURE;
            $statusCode = Response::STATUS_BAD_GATEWAY;
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
            ->withJson($data, $statusCode)
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

        $formattedUser = [];

        try {
            $user = $this->getUser->execute($id);
            $formattedUser = UserView::formatUser($user);
            $this->deleteUser->execute($id, $formattedUser);
            $message = "Account deactivated successfully.";
            $status = Response::SUCCESS_STATUS_MESSAGE;
            $statusCode = Response::STATUS_OK;
        } catch (RecordExistsException|\InvalidArgumentException $exception) {
            $message = $exception->getMessage();
            $errorMessage = [$exception->getMessage()];
            $status = Response::FAILED_STATUS_FAILURE;
            $statusCode = Response::STATUS_BAD_REQUEST;
            $this->logger->error(
                $message,
                [
                    'error' => $errorMessage,
                    'date' => $newDate->format('d-m-Y H:i:s')
                ]
            );
        } catch (Exception $exception) {
            $message = "Failed to delete the user.";
            $errorMessage = [$exception->getMessage()];
            $status = Response::FAILED_STATUS_FAILURE;
            $statusCode = Response::STATUS_BAD_GATEWAY;
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
            ->withJson($data, $statusCode)
            ->send();
    }
}