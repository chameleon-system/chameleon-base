<?php

declare(strict_types=1);

namespace ChameleonSystem\CoreBundle\Geocoding;

use ChameleonSystem\SecurityBundle\CmsUser\CmsUserModel;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsUserRoleConstants;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

readonly class GeocodingController
{
    public function __construct(
        private GeocoderInterface $geocoder,
        private SecurityHelperAccess $securityHelper)
    {
    }

    public function __invoke(Request $request): Response
    {
        if (false === $this->isBackendUserAuthenticated()) {
            throw new UnauthorizedHttpException('Must be logged in');
        }

        $query = $request->get('query', null);
        if (null === $query || '' === $query) {
            throw new BadRequestHttpException('Please specify `query` parameter.');
        }

        $results = $this->geocoder->geocode($query);
        $serialized = array_map([$this, 'serialize'], $results);

        return new JsonResponse($serialized);
    }

    private function isBackendUserAuthenticated(): bool
    {
        $user = $this->securityHelper->getUser();

        return !(null === $user || false === $this->securityHelper->isGranted(CmsUserRoleConstants::CMS_USER)
            || false === ($user instanceof CmsUserModel));
    }

    private function serialize(GeocodingResult $geocodingResult): array
    {
        return [
            'name' => $geocodingResult->getName(),
            'latitude' => $geocodingResult->getLatitude(),
            'longitude' => $geocodingResult->getLongitude(),
        ];
    }
}
