<?php

namespace ChameleonSystem\CmsBackendBundle\BackendSession;

interface BackendSessionInterface
{
    public function getCurrentEditLanguageIso6391(): ?string;

    public function getCurrentEditLanguageId(): string;


    public function setCurrentEditLanguageIso6391(string $language): void;

    public function resetCurrentEditLanguage(): void;
}