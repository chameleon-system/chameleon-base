# CmsDashboardBundle

The `CmsDashboardBundle` provides a customizable dashboard for the CMS, allowing users to configure and manage widgets dynamically. Widgets are loaded using service tags and can be sorted, added, or removed by the user.

## 📌 Features
- **Dynamic Widget System** – Widgets can be registered and managed using Symfony services using tags.
- **Drag & Drop Sorting** – Users can arrange, add and remove widget groups according to their preferences.
- **API for Dynamic Updates** – Widgets fetch and update data via AJAX by default. You can add your own API endpoints as well.
- **Caching Support** – Supports efficient caching for better performance.

---

## 🚀 Getting Started

### 1️⃣ **Registering a New Widget**
To create a new widget, you need to do the following:

- define a class that extends `DashboardWidget` and implement the required methods
- register the widget as a Symfony service and tag it with `chameleon_system.dashboard_widget`
- create a service alias with the widget id of the widget class (the widget id is a constant in the widget class)
- create a Twig template for the widget content (used in `generateBodyHtml()` method) - optional, you can also return HTML directly from the `generateBodyHtml()` method.
- make sure that any scripts in the template listen to the `widget:loaded` event (instead of `DOMContentLoaded`) - as the widget content is loaded dynamically via AJAX.

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
        // a reload widget button is automatically added for you
        return [];
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

#### In PHP:

To force a cache reload when retrieving the widget's HTML, pass `true` to `getBodyHtml()`:

```php
$widgetHtml = $this->getBodyHtml(true); // Forces cache reload
```

#### In Twig/JS:

To force a cache reload pass `true` as the second parameter of loadWidgetContent:

```js
loadWidgetContent(serviceAlias, true);
```

```twig
<button class="dropdown-item"
        type="button"
        data-service-alias="{{ widget.widgetId|e('html_attr') }}"
        onclick="loadWidgetContent('{{ widget.widgetId|e('js') }}', true)">
    <i class="fas fa-sync-alt me-2"></i> {{ 'chameleon_system_cms_dashboard.widget.reload_button_label'|trans }}
</button>
```

---

## 🔄 Widget API
Widgets can expose API methods via **`ExposeAsApi`** attributes.

### 📌 **Example: Exposing an API Endpoint**
```php
#[ExposeAsApi(description: 'API endpoint to get the widget clicks (possibly inverted)')]
public function getWidgetClicks(bool $invert = false): JsonResponse
{
    return new JsonResponse([
        'clicks' => $this->getClickNumber() * ($invert ? -1 : 1),
    ]);
}
```
➡ This API can be accessed at:
```
/cms/api/dashboard/widget/{widgetServiceId}/getWidgetClicks
```
If you would want to invert the clicks (i.e. setting the `$invert` parameter), you can call:
```
/cms/api/dashboard/widget/{widgetServiceId}/getWidgetClicks?invert=true
```
Please note that parameters without default values are treated as required parameters and must be passed in the URL. So if $invert was not set to `false` in the example above, you would need to pass it in the URL like this:
```
/cms/api/dashboard/widget/{widgetServiceId}/getWidgetClicks?invert
```

### 📌 **Reloading Widget Content Dynamically**

Each widget supports (and uses) lazy loading by default. The `getWidgetHtmlAsJson` method is employed to...

1. lazy load the widget content on initial load.
2. reload the widget using the option in the widget dropdown.
3. reload all widgets using the "Reload all widgets" button.

There is a default implementation for this in the `DashboardWidget` class. You can override it if you want to change the behavior.

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

## Configuring the Bundle

You may set the cache TTL for all widgets in your configuration. Default is 1 day.

```yaml
chameleon_system_cms_dashboard:
    cache_ttl: 86400
```

## Google Search Console Widget

The Google Search Console widget is a widget that requires access to the Google Search Console API.
https://console.cloud.google.com/apis/dashboard

- Add a new service account to the Google Cloud Console and add this account as read only user to your Search Console property
  for the desired website.
- Add the Google Search Console API to the APIs
- Configure the API access and and the domain you want to show data for in the widget.

config.yml:

```yaml
chameleon_system_cms_dashboard:
    google_search_console_domain_property: 'your-domain.de'
    google_api_auth_json: '%google_api_auth_json%'
```

parameters.yml (or env):

```yaml
parameters:
    google_api_auth_json: |
        {
            "type": "service_account",
            "project_id": "your-project-id",
            "private_key_id": "your-private-key-id",
            "private_key": "-----BEGIN PRIVATE KEY-----\nYOUR_PRIVATE_KEY\n-----END PRIVATE KEY-----\n",
            "client_email": "your-service-account@your-project.iam.gserviceaccount.com",
            "client_id": "your-client-id",
            "auth_uri": "https://accounts.google.com/o/oauth2/auth",
            "token_uri": "https://oauth2.googleapis.com/token",
            "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
            "client_x509_cert_url": "https://www.googleapis.com/robot/v1/metadata/x509/your-service-account%40your-project.iam.gserviceaccount.com"
        }
```

---

## Google Analytics Widget

Google Analytics API Documentation: https://developers.google.com/analytics/devguides/reporting/data/v1/api-schema

- Add the User to analytics

config.yml:

```yaml
chameleon_system_cms_dashboard:
    google_api_auth_json: '%google_api_auth_json%'
    google_analytics_property_id: '123456789'
    google_analytics_period_days: 28
```

parameters.yml (or env):

```yaml
parameters:
    google_api_auth_json: |
        {
            "type": "service_account",
            "project_id": "your-project-id",
            "private_key_id": "your-private-key-id",
            "private_key": "-----BEGIN PRIVATE KEY-----\nYOUR_PRIVATE_KEY\n-----END PRIVATE KEY-----\n",
            "client_email": "your-service-account@your-project.iam.gserviceaccount.com",
            "client_id": "your-client-id",
            "auth_uri": "https://accounts.google.com/o/oauth2/auth",
            "token_uri": "https://oauth2.googleapis.com/token",
            "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
            "client_x509_cert_url": "https://www.googleapis.com/robot/v1/metadata/x509/your-service-account%40your-project.iam.gserviceaccount.com"
        }
```

## 📚 Summary
✔ **Extend `DashboardWidget` to create a new widget**  
✔ **Register the widget as a Symfony service & tag it**  
✔ **Use caching efficiently with `getBodyHtml(true)`**  
✔ **Expose API endpoints using `ExposeAsApi`**  
✔ **Users can customize layout, saved via `/cms/api/dashboard/save-widget-layout`**  
✔ **Use `ColorGeneratorService` for consistent widget styling**

---
