<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaydigitalService
{
    protected string $apiKey;
    protected int $storeId;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey  = config('services.paydigital.key');
        $this->storeId = (int) config('services.paydigital.store_id', 1);
        $this->baseUrl = config('services.paydigital.base_url', 'http://paydigital.biz.id/api');
    }

    /**
     * Create a QRIS payment via Paydigital API.
     *
     * @param  int    $amount        Amount in IDR (min 1000)
     * @param  string $description   Payment description
     * @param  string $customerName  Customer name
     * @return array{success: bool, data?: array, message?: string}
     */
    public function createPayment(int $amount, string $description = '', string $customerName = ''): array
    {
        try {
            $response = Http::asForm()->post("{$this->baseUrl}/create_payment.php", [
                'api_key'       => $this->apiKey,
                'store_id'      => $this->storeId,
                'amount'        => $amount,
                'description'   => $description,
                'customer_name' => $customerName,
            ]);

            $body = $response->json();

            if ($response->successful() && isset($body['status']) && $body['status'] === 'success') {
                return [
                    'success' => true,
                    'data'    => $body['data'] ?? $body,
                ];
            }

            Log::warning('Paydigital create_payment failed', [
                'status'   => $response->status(),
                'response' => $body,
            ]);

            return [
                'success' => false,
                'message' => $body['message'] ?? 'Gagal membuat pembayaran QRIS.',
            ];
        } catch (\Exception $e) {
            Log::error('Paydigital create_payment exception', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Koneksi ke payment gateway gagal.',
            ];
        }
    }

    /**
     * Check payment status via Paydigital API.
     *
     * @param  string $invoice  Invoice number from Paydigital
     * @return array{success: bool, data?: array, message?: string}
     */
    public function checkStatus(string $invoice): array
    {
        try {
            $response = Http::asForm()->post("{$this->baseUrl}/payment.php", [
                'api_key' => $this->apiKey,
                'invoice' => $invoice,
            ]);

            $body = $response->json();

            if ($response->successful() && isset($body['status']) && $body['status'] === 'success') {
                return [
                    'success' => true,
                    'data'    => $body['data'] ?? $body,
                ];
            }

            return [
                'success' => false,
                'message' => $body['message'] ?? 'Gagal mengecek status pembayaran.',
            ];
        } catch (\Exception $e) {
            Log::error('Paydigital checkStatus exception', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Koneksi ke payment gateway gagal.',
            ];
        }
    }
}
