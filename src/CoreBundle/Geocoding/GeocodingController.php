<?php declare(strict_types=1);

namespace ChameleonSystem\CoreBundle\Geocoding;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class GeocodingController
{

    /** @var GeocoderInterface */
    private $geocoder;

    public function __construct(GeocoderInterface $geocoder)
    {
        $this->geocoder = $geocoder;
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
        $serialized = array_map([ $this, 'serialize' ], $results);

        return new JsonResponse($serialized);
    }

    private function isBackendUserAuthenticated(): bool
    {
        $user = \TdbCmsUser::GetActiveUser();

        if (null === $user || false === $user->bLoggedIn) {
            return false;
        }

        return true;
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
