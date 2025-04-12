<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class BasePaymentService
{
    protected $base_url;
    protected $header;

    protected function buildRequest($method, $url, $data = null, $type = 'json', $additionalHeaders = [], $queryParams = []): \Illuminate\Http\JsonResponse
    {
        try {
            $headers = array_merge($this->header, $additionalHeaders);
            $request = Http::withHeaders($headers);

            if ($queryParams) {
                $request = $request->withQueryParameters($queryParams);
            }

            if (strtoupper($method) === 'GET') {
                $response = $request->get($this->base_url . $url);
            } else {
                $response = $request->send($method, $this->base_url . $url, [
                    $type => $data
                ]);
            }

            return response()->json([
                'success' => $response->successful(),
                'status' => $response->status(),
                'data' => $response->json(),
            ], $response->status());
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

}