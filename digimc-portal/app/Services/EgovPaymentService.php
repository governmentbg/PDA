<?php

namespace App\Services;

use App\Enums\SettingEnum;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Exception;

class EgovPaymentService
{

    private string $clientId;
    private string $secret;

    public function __construct()
    {
        $this->clientId = config('services.egov.client');
        $this->secret   = config('services.egov.secret');
    }

    private function http()
    {
        return Http::asForm()
            ->timeout(30)
            ->withHeaders([
                'Content-Type' => 'application/x-www-form-urlencoded',
            ]);
    }

    private function buildPayload(array $dataObj): array
    {
        $json = json_encode($dataObj, JSON_UNESCAPED_UNICODE);
        $data = base64_encode($json);
        $hmac = base64_encode(
            hash_hmac('sha256', $data, $this->secret, binary: true)
        );

        return [
            'clientId' => $this->clientId,
            'eserviceClientId' => $this->clientId,
            'hmac'     => $hmac,
            'data'     => $data,
        ];
    }

    public function createPaymentRequest(Payment $payment): array
    {
        /** @var \App\Models\User $user */
        $user = $payment->user;

        $dataObj = [
            'currency'                             => 'EUR',
            'paymentAmount'                        => number_format($payment->total_amount, 2, '.', ''),
            'paymentReason'                        => SettingEnum::getValueByKeyword(SettingEnum::PAYMENT_REASON),
            'applicantUinTypeId'                   => config('services.egov.applicant_uin_type'),
            'applicantUin'                         => config('services.egov.applicant_uin'),
            'applicantName'                        => $user->name,
            'paymentReferenceNumber'               => str_pad((string) $payment->id, 6, '0', STR_PAD_LEFT),
            'paymentReferenceDate'                 => $payment->created_at,
            'expirationDate'                       => $payment->expires_at,
            'administrativeServiceNotificationURL' => route('api.payments.callback'),
        ];

        $response = $this->http()->post(
            config('services.egov.create_request_url'),
            $this->buildPayload($dataObj)
        );

        if (!$response->successful()) {
            throw new Exception('Egov get payment code failed');
        }

        $body = $response->json();


        return $body;
    }

    public function checkPaymentStatus(array $ids): array
    {
        $dataObj = [
            'requestIds' => $ids,
        ];

        $response = $this->http()->post(
            config('services.egov.status_url'),
            $this->buildPayload($dataObj)
        );

        if (!$response->successful()) {
            throw new \Exception('Egov status check failed: ' . $response->body());
        }

        return $response->json();
    }

    public function suspendRequest(string $id): array
    {
        $dataObj = [
            'id' => $id,
        ];

        $response = $this->http()->post(
            config('services.egov.suspend_request_url'),
            $this->buildPayload($dataObj)
        );

        if (!$response->successful()) {
            throw new \Exception('Egov suspend failed: ' . $response->body());
        }

        return $response->json();
    }

}
