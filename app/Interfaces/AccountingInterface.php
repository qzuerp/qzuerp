<?php

namespace App\Interfaces;

interface AccountingInterface
{
    public function authenticate(array $config): bool;
    public function createInvoice(array $data);
    public function createContact(array $data);
    public function getContacts($code);
    public function getProducts($code);
}