<?php

namespace App\Presentation\Controller;

use App\Application\DTO\Formatters\LoginResponseFormatter;
use App\Application\Exceptions\InvalidCredentialsException;
use App\Application\Requests\AuthenticateRequest;
use App\Application\UseCases\Authentication\GetJWT;
use App\Application\UseCases\Authentication\LoginAction;
use App\Application\UseCases\Notification\NotifyUser;
use App\Application\Validators\JsonRequestValidator;
use App\Domain\Exceptions\InvalidEmailException;
use App\Infrastructure\Logging\Logger;
use App\Presentation\Http\Request;
use App\Presentation\Http\Response;
use App\Presentation\View\LetterView;
use App\Presentation\View\NotificationView;
use App\Presentation\View\UserView;
use App\Shared\Exceptions\RecordExistsException;
use DateTime;
use DateTimeZone;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Monolog\DateTimeImmutable;

class AuthController
{
    private Logger $logger;
    private LoginAction $loginAction;
    private GetJWT $getJwt;
    private NotifyUser $notifyUser;

    /**
     * @param Logger $logger
     * @param LoginAction $loginAction
     * @param GetJWT $getJwt
     * @param NotifyUser $notifyUser
     */
    public function __construct(
        Logger $logger,
        LoginAction $loginAction,
        GetJWT $getJwt,
        NotifyUser $notifyUser
    ) {
        $this->logger = $logger;
        $this->loginAction = $loginAction;
        $this->getJwt = $getJwt;
        $this->notifyUser = $notifyUser;
    }

    /**
     * @param Request $request
     * @return bool|string
     * @throws Exception
     */
    public function login(Request $request): bool|string
    {
        $response = new Response();
        $newDate = new DateTimeImmutable(false);

        $formattedUser = [];
        $token = '';

        try {
            $loginData = $request->getBody()->getContents();
            $loginObject = json_decode($loginData);

            JsonRequestValidator::validate(json_decode($loginData, true), AuthenticateRequest::rules());

            $user = $this->loginAction->execute($loginObject);
            $formattedUser = UserView::formatUser($user);
            $token = $this->getJwt->execute($formattedUser);

            $timezone = new DateTimeZone('Asia/Yerevan');
            $dateTime = new DateTime('now', $timezone);
            $currentTime = $dateTime->format('g:i A T');

            $letterSubject = sprintf("New Login to Booking account from %s", $_SERVER['HTTP_USER_AGENT']);
            $letterMessage = sprintf(
                "We noticed a new login, %s at %s in Yerevan timezone",
                $user->getName(),
                $currentTime
            );

            $formattedLetter = LetterView::formatLetter($letterSubject, $letterMessage);
            $formattedNotification = NotificationView::formatNotification($user, $formattedLetter);

            $this->notifyUser->execute($formattedNotification);

            $message = "You have logged in successfully!";
            $status = Response::SUCCESS_STATUS_MESSAGE;
            $statusCode = Response::STATUS_OK;
        } catch (GuzzleException $exception) {
            $message = "Failed to send an email.";
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
        } catch (InvalidCredentialsException|InvalidEmailException $exception) {
            $message = "Email or password is not correct.";
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
        } catch (RecordExistsException $exception) {
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
            $message = "Unable to log in.";
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
        $data = LoginResponseFormatter::format($formattedUser, $token, $message, $status);

        return $response
            ->withJson($data, $statusCode)
            ->send();
    }
}