<?php

namespace ChameleonSystem\ImageEditorBundle\Interface;

interface ImageEditorUrlServiceInterface
{
    public function getImageEditorUrl(string $mediaItemId): string;
}