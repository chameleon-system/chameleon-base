<?php
/**
 * @var string $sModuleSpotName
 * @var string $errorMessage
 * @var bool $hasError
 * @var string $uploaderFormAction
 * @var string $uploadUrl
 * @var array $hiddenFields
 * @var int $chunkSize
 * @var int $maxUploadSize
 * @var ChameleonSystem\CoreBundle\UniversalUploader\Library\DataModel\UploaderParametersDataModel $parameterBag
 */
$oViewRenderer = new ViewRenderer();

if (true === $hasError) {
    $oViewRenderer->AddSourceObject('hasError', $hasError);
    $oViewRenderer->AddSourceObject('errorMessage', $errorMessage);
} else {
    $oViewRenderer->AddSourceObject('uploaderFormAction', $uploaderFormAction);
    $oViewRenderer->AddSourceObject('uploadUrl', $uploadUrl);
    $oViewRenderer->AddSourceObject('moduleSpotName', $sModuleSpotName);
    $oViewRenderer->AddSourceObject('hiddenFields', $hiddenFields);
    $oViewRenderer->AddSourceObject('chunkSize', $chunkSize);
    $oViewRenderer->AddSourceObject('maxUploadSize', $maxUploadSize);
    $oViewRenderer->AddSourceObject('singleMode', $parameterBag->isSingleMode());
    $oViewRenderer->AddSourceObject('showMetaFields', $parameterBag->isShowMetaFields());
    $oViewRenderer->AddSourceObject('uploadName', $parameterBag->getUploadName());
    $oViewRenderer->AddSourceObject('uploadDescription', $parameterBag->getUploadDescription());
    $oViewRenderer->AddSourceObject('allowedFileTypes', $parameterBag->getAllowedFileTypes());
    $oViewRenderer->AddSourceObject('uploadSuccessCallback', $parameterBag->getUploadSuccessCallback());
    $oViewRenderer->AddSourceObject('queueCompleteCallback', $parameterBag->getQueueCompleteCallback());
    $oViewRenderer->AddSourceObject('maxUploadWidth', $parameterBag->getMaxUploadWidth());
    $oViewRenderer->AddSourceObject('maxUploadHeight', $parameterBag->getMaxUploadHeight());
    $oViewRenderer->AddSourceObject('proportionExactMatch', $parameterBag->isProportionExactMatch());
}

echo $oViewRenderer->Render('CMSModuleUniversalUploader/uploader.html.twig');
