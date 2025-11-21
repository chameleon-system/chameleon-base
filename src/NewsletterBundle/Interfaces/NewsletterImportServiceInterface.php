<?php

namespace ChameleonSystem\NewsletterBundle\Interfaces;

interface NewsletterImportServiceInterface
{
    public function importZipFile(string $recordId): void;
}