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

    public function getContacts()
    {
        return Http::withToken($this->token)
            ->get($this->baseUrl . $this->companyId . '/contacts')
            ->json();
    }

    public function getProducts()
    {
        Cache::forget('parasut_all_products');

        return Cache::remember('parasut_all_products', 3600, function () {
            $allProducts = [];
            $page = 1;
            $pageSize = 50;

            do {
                $response = Http::withToken($this->token)
                    ->get($this->baseUrl . $this->companyId . '/products', [
                        'page[number]' => $page,
                        'page[size]'   => $pageSize
                    ]);

                if (!$response->successful()) {
                    \Log::error("Paraşüt Hatası: " . $response->body());
                    break;
                }

                $data = $response->json();
                $currentPageProducts = $data['data'] ?? [];

                if (empty($currentPageProducts)) {
                    break;
                }

                foreach ($currentPageProducts as $product) {
                    $productId   = $product['id'] ?? null;
                    $attributes  = $product['attributes'] ?? [];
                    $stokKodu    = !empty($attributes['code']) ? $attributes['code'] : ('ID_' . $productId);

                    $allProducts[$stokKodu] = [
                        'id'       => $productId,
                        'name'     => $attributes['name'] ?? 'İsimsiz Ürün',
                        'code'     => $attributes['code'] ?? null,
                        'barcode'  => $attributes['barcode'] ?? null,
                        'currency' => $attributes['currency'] ?? null,
                        'vat_rate' => $attributes['vat_rate'] ?? null,
                    ];
                }

                \Log::info("Paraşüt: Sayfa {$page} çekildi, " . count($currentPageProducts) . " ürün geldi.");

                $totalPages = $data['meta']['total_pages'] ?? 1;
                $page++;

            } while ($page <= $totalPages && !empty($currentPageProducts));

            \Log::info("Paraşüt: Toplam " . count($allProducts) . " ürün çekildi.");

            return $allProducts;
        });
    }
}