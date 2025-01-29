<?php

namespace ChameleonSystem\CmsDashboardBundle\Controllers;

use ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Attribute\ExposeAsApi;
use ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Dashboard\Widgets\DashboardWidgetInterface;
use ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Module\Dashboard;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

readonly class WidgetController
{
    public function __construct(
        private ContainerInterface $container,
        private Dashboard $dashboardModule)
    {
    }

    public function callWidgetMethod(Request $request, string $widgetAlias, string $methodName): Response
    {
        try {
            $widgetService = $this->getWidgetServiceByAlias($widgetAlias);
            $this->checkForValidExposedApiMethod($widgetService, $methodName, $widgetAlias);

            $reflectionMethod = new \ReflectionMethod($widgetService, $methodName);
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

            return (new \ReflectionMethod($widgetService, $methodName))->invokeArgs($widgetService, $arguments);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    private function checkForValidExposedApiMethod(mixed $service, string $methodName, ?string $widgetAlias): void
    {
        $reflectionMethod = new \ReflectionMethod($service, $methodName);
        if (false === $reflectionMethod->isPublic()) {
            if (null === $widgetAlias) {
                throw new InvalidArgumentException('Method '.$methodName.' is not public on widget service '.$widgetAlias);
            }

            throw new InvalidArgumentException('Method '.$methodName.' is not public on service class '.get_class($service));
        }

        $attributes = $reflectionMethod->getAttributes(ExposeAsApi::class);
        if (empty($attributes)) {
            if (null === $widgetAlias) {
                throw new InvalidArgumentException(
                    'Method '.$methodName.' is not exposed via #[ExposeAsApi] attribute on widget service '.$widgetAlias
                );
            }

            throw new InvalidArgumentException(
                'Method '.$methodName.' is not exposed via #[ExposeAsApi] attribute on service class '.get_class($service)
            );
        }
    }

    public function saveWidgetLayout(Request $request): Response
    {
        try {
            $this->checkForValidExposedApiMethod($this->dashboardModule, 'saveWidgetLayout', null);

            $content = $request->getContent();

            $data = json_decode($content, true);
            if (!isset($data['widgetLayout'])) {
                return new JsonResponse(['error' => 'Missing required parameter "widgetLayout".'], 400);
            }

            $this->dashboardModule->saveWidgetLayout($data['widgetLayout']);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => $e->getMessage(),
            ], 400);
        }

        return new JsonResponse(['updateSuccessful' => true]);
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
