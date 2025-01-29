# CmsDashboardBundle

The `CmsDashboardBundle` provides a customizable dashboard for the CMS, allowing users to configure and manage widgets dynamically. Widgets are loaded using service tags and can be sorted, added, or removed by the user.

## 📌 Features
- **Dynamic Widget System** – Widgets can be registered and managed using Symfony services using tags.
- **Drag & Drop Sorting** – Users can arrange, add and remove widget groups according to their preferences.
- **API for Dynamic Updates** – Widgets can fetch and update data via AJAX.
- **Caching Support** – Supports efficient caching for better performance.

---

## 🚀 Getting Started

### 1️⃣ **Registering a New Widget**
To create a new widget, define a class that extends `DashboardWidget` and implement the required methods.

#### 📌 **Example: Creating a New Widget**
```php
namespace ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Dashboard\Widgets;

use ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Attribute\ExposeAsApi;
use ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Service\DashboardCacheService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

class DashboardExampleWidget extends DashboardWidget
{
    private const WIDGET_ID = 'widget-example';

    public function __construct(
        protected readonly DashboardCacheService $dashboardCacheService,
        protected readonly TranslatorInterface $translator
    ) {
        parent::__construct($dashboardCacheService, $translator);
    }

    public function getTitle(): string
    {
        return $this->translator->trans('chameleon_system_cms_dashboard.widget.example_title');
    }

    public function showWidget(): bool
    {
        return true; // You need to add permission checks here (e.g. user role)
    }

    public function getDropdownItems(): array
    {
        return [];
    }

    #[ExposeAsApi(description: 'API endpoint to reload the widget content dynamically')]
    public function getWidgetHtmlAsJson(): JsonResponse
    {
        return new JsonResponse([
            'htmlTable' => $this->getBodyHtml(true),
            'dateTime' => date('d.m.Y H:i')
        ]);
    }

    protected function generateBodyHtml(): string
    {
        return "<div class='card'><div class='card-body'>This is an example widget.</div></div>";
    }

    public function getWidgetId(): string
    {
        return self::WIDGET_ID;
    }
}
```
---

### 2️⃣ **Registering the Widget in the Dashboard**
To make the widget available in the dashboard, register it as a **Symfony service** and tag it with `chameleon_system.dashboard_widget`.

#### 📌 **Example: Service Registration**
```xml
<service id="app.cms_dashboard.widget.example" class="ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Dashboard\Widgets\DashboardExampleWidget">
    <argument type="service" id="ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Service\DashboardCacheService" />
    <argument type="service" id="translator" />
    <tag name="chameleon_system.dashboard_widget" collection="analytics" priority="10"/>
</service>
```
✔ **`collection="analytics"`** → Defines which collection this widget belongs to.  
✔ **`priority="10"`** → Widgets with a higher priority will appear earlier in the list.

---

## 🐄 Caching Behavior
### 📌 **How Caching Works**
- **In Production Mode:** Cached using the **Chameleon Cache Service** (`DashboardCacheService`).
- **In Development Mode:** Cached using the **Database Cache** (even when caching is disabled).

### 📌 **How to Force Cache Reload**
To force a cache reload when retrieving the widget's HTML, pass `true` to `getBodyHtml()`:

```php
$widgetHtml = $this->getBodyHtml(true); // Forces cache reload
```

---

## 🔄 Widget API
Widgets can expose API methods via **`ExposeAsApi`** attributes.

### 📌 **Example: Exposing an API Endpoint**
```php
#[ExposeAsApi(description: 'API endpoint to reload the widget content dynamically')]
public function getWidgetHtmlAsJson(): JsonResponse
{
    return new JsonResponse([
        'htmlTable' => $this->getBodyHtml(true),
        'dateTime' => date('d.m.Y H:i')
    ]);
}
```
➡ This API can be accessed at:
```
/cms/api/dashboard/widget/{widgetServiceId}/getWidgetHtmlAsJson
```
### 📌 **Reloading Widget Content Dynamically

Each widget can support dynamic content reloading using the **initializeWidgetReload** function. 
This allows refreshing widget data via AJAX without requiring a full page reload.

Initialize the widget reload by calling the **`initializeWidgetReload`** function in the widget's JavaScript file.

```javascript
    document.addEventListener("DOMContentLoaded", function () {
        initializeWidgetReload("#{{ reloadEventButtonId|e('html_attr') }}");
    });
```

---

## 📊 Managing Widget Layout
### 📌 **Saving the User’s Layout**
The user’s **widget order and visibility** is stored in the database and updated dynamically.

#### **API Endpoint to Save Widget Layout**
```yaml
chameleon_system_dashboard.widget_order.api:
  path: /cms/api/dashboard/save-widget-layout
  methods: [POST]
  defaults: { _controller: chameleon_system_cms_dashboard.controllers.widget_controller::saveWidgetLayout }
```
✔ This saves the **order of widgets** and which **widgets are active**.

---

## 🎨 Widget Styling & Colors
### 📌 **Color Generator Service**
The `ColorGeneratorService` is available in PHP and Twig as **generate_color** to generate colors dynamically.

#### **Example: Using the Service**
```php
$color = $colorGeneratorService->generateColor($index, $total);
```
✔ Generates colors **based on the index & total count** to ensure unique visual identifiers.

#### **Example: Using in Twig**
```twig
<div style="backgroundColor: '{{ generate_color(1, 4) }}'"></div>
```

---

## 📚 Summary
✔ **Extend `DashboardWidget` to create a new widget**  
✔ **Register the widget as a Symfony service & tag it**  
✔ **Use caching efficiently with `getBodyHtml(true)`**  
✔ **Expose API endpoints using `ExposeAsApi`**  
✔ **Users can customize layout, saved via `/cms/api/dashboard/save-widget-layout`**  
✔ **Use `ColorGeneratorService` for consistent widget styling**

---
