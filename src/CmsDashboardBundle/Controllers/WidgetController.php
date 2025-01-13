<?php

namespace ChameleonSystem\CmsDashboardBundle\Controllers;

use ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Attribute\ExposeAsApi;
use ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Dashboard\Widgets\DashboardWidgetInterface;
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

    public function callWidgetMethod(Request $request, string $widgetAlias, string $methodName): Response
    {
        try {
            $widgetService = $this->getWidgetServiceByAlias($widgetAlias);

            $reflectionMethod = new \ReflectionMethod($widgetService, $methodName);
            if (!$reflectionMethod->isPublic()) {
                throw new InvalidArgumentException('Method '.$methodName.' is not public on widget service '.$widgetAlias);
            }

            $attributes = $reflectionMethod->getAttributes(ExposeAsApi::class);
            if (empty($attributes)) {
                throw new InvalidArgumentException('Method '.$methodName.' is not exposed via #[ExposeAsApi] attribute on widget service '.$widgetAlias);
            }

            $expectedParameters = $reflectionMethod->getParameters();
            $arguments = [];
            $providedParameters = array_merge(
                $request->query->all(),
                $request->request->all()
            );

            foreach ($expectedParameters as $parameter) {
                $name = $parameter->getName();

                if (array_key_exists($name, $providedParameters)) {
                    $arguments[] = $providedParameters[$name];
                } elseif ($parameter->isDefaultValueAvailable()) {
                    $arguments[] = $parameter->getDefaultValue();
                } else {
                    throw new InvalidArgumentException("Missing required parameter '$name' for method $methodName.");
                }
            }

            return $reflectionMethod->invokeArgs($widgetService, $arguments);
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

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     */
    private function getWidgetServiceByAlias(string $alias): DashboardWidgetInterface
    {
        $widgetService = $this->container->get($alias);

        if (!$widgetService instanceof DashboardWidgetInterface) {
            throw new InvalidArgumentException(sprintf('Service for alias "%s" does not implement DashboardWidgetInterface.', $alias));
        }

        return $widgetService;
    }
}
