<?php

namespace App\Traits;

use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Throwable;

trait ApiResponser
{
    protected function successResponse($data, $message = null, $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function errorResponse($message = null, $code = 400, $options = [])
    {
        $res = array(
            'success' => false,
            'message' => $message,
        );
        if ($options) {
            $res = array_merge($res, $options);
        }
        return response()->json($res, $code);
    }
    protected function errorReport(Throwable $exception)
    {
        if (config("app.env") == "production" && !is_null(env("TELEGRAM_BOT_TOKEN"))) {
            $client  = new Client();
            $url = "https://api.telegram.org/bot" . env("TELEGRAM_BOT_TOKEN", "xxxx.xxxx.xxx.xxx.xxxxxxx") . "/sendMessage"; //<== ganti jadi token yang kita tadi
            $data    = $client->request('GET', $url, [
                'json' => [
                    "chat_id" => env("BOTTELEGRAM_CHATID", "-729266779"), //<== ganti dengan id_message yang kita dapat tadi
                    "text" => "File : " . $exception->getFile() . "\nLine : " . $exception->getLine() . "\nCode : " . $exception->getCode() . "\nMessage : " . $exception->getMessage(), "disable_notification" => true
                ]
            ]);

            $json = $data->getBody();
        }
    }
    protected function unknownResponse($throwable)
    {
        $this->errorReport($throwable);
        if (!config('app.debug')) {
            return $this->errorResponse('An error has been occured', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->errorResponse($throwable->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
