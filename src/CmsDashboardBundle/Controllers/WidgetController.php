<?php

namespace ChameleonSystem\CmsDashboardBundle\Controllers;

use ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Dashboard\Widgets\DashboardWidgetInterface;
use ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Attribute\ExposeAsApi;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

readonly class WidgetController
{
    public function __construct(private ContainerInterface $container)
    {
    }

    public function callWidgetMethod(Request $request, string $widgetServiceId, string $methodName): Response
    {
        try {
            $parameters = array_merge(
                $request->query->all(),
                $request->request->all()
            );

            $widgetService = $this->getWidgetService($widgetServiceId);

            $reflectionMethod = new \ReflectionMethod($widgetService, $methodName);

            if (!$reflectionMethod->isPublic()) {
                throw new InvalidArgumentException('Method '.$methodName.' is not public on widget service '.$widgetServiceId);
            }

            // Check if #[ExposeAsApi] Attribut is set for method
            $attributes = $reflectionMethod->getAttributes(ExposeAsApi::class);
            if (empty($attributes)) {
                throw new InvalidArgumentException('Method '.$methodName.' is not exposed via #[ExposeAsApi] attribute on widget service '.$widgetServiceId);
            }

            return $reflectionMethod->invokeArgs($widgetService, $parameters);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     */
    private function getWidgetService(string $widgetServiceId): DashboardWidgetInterface
    {
        $widgetService = $this->container->get($widgetServiceId);

        if (!$widgetService instanceof DashboardWidgetInterface) {
            throw new InvalidArgumentException('Service '.$widgetServiceId.' does not implement DashboardWidgetInterface');
        }

        return $widgetService;
    }
}