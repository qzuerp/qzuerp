<?php

namespace App\Services;

use App\Interfaces\AccountingInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ParasutService implements AccountingInterface
{
    protected $token;
    protected $baseUrl = 'https://api.parasut.com/v4/';
    protected $companyId;

    public function authenticate(array $config): bool
    {
        $this->companyId = $config['company_id'] ?? '';

        $response = Http::asForm()->post('https://api.parasut.com/oauth/token', [
            'grant_type'    => 'password',
            'client_id'     => $config['CLIENT_ID'],
            'client_secret' => $config['CLIENT_SECRET'],
            'username'      => $config['username'],
            'password'      => $config['password'],
            'redirect_uri'  => 'urn:ietf:wg:oauth:2.0:oob'
        ]);

        if ($response->successful()) {
            $this->token = $response->json()['access_token'];
            return true;
        }

        return false;
    }

    public function createInvoice(array $data)
    {
        if (!$this->token) return ['error' => 'Token yok!'];

        $payload = [
            'data' => [
                'type' => 'shipment_documents',
                'attributes' => [
                    'description'   => $data['description'] ?? '',
                    'issue_date'    => $data['issue_date'],
                    'shipment_date' => $data['shipment_date'] ?? $data['issue_date'],
                    'city' => $data['city'],
                    'district' => $data['district'],
                    'address' => $data['address'],
                    'inflow'     => false,
                ],
                'relationships' => [
                    'contact' => [
                        'data' => [
                            'type' => 'contacts',
                            'id'   => (string) $data['contact_id'],
                        ]
                    ],
                    'stock_movements' => [
                        'data' => collect($data['lines'])->map(fn($line) => [
                            'type' => 'stock_movements',
                            'attributes' => [
                                'quantity'    => (float) $line['quantity'],
                                'description' => $line['product_name'] ?? '',
                            ],
                            'relationships' => [
                                'product' => [
                                    'data' => [
                                        'type' => 'products',
                                        'id'   => (string) $line['product_id'],
                                    ]
                                ]
                            ]
                        ])->values()->all()
                    ]
                ]
            ]
        ];

        $response = Http::withToken($this->token)
        ->post($this->baseUrl . $this->companyId . '/shipment_documents', $payload);

        return $response;
    }

    public function createContact(array $data)
    {
        if (!$this->token) return ['error' => 'Önce giriş yapmalısın!'];

        return Http::withToken($this->token)
            ->post($this->baseUrl . $this->companyId . '/contacts', $data)
            ->json();
    }

    public function getContacts($code)
    {
        return Http::withToken($this->token)
            ->get($this->baseUrl . $this->companyId . '/contacts',[
                'filter[name]' => trim($code)
            ])
            ->json();
    }

    public function getProducts($code)
    {
        return Http::withToken($this->token)
            ->get($this->baseUrl . $this->companyId . '/products', [
                'filter[code]' => trim($code)
            ])
            ->json();
    }
}