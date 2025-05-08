Chameleon System CmsEventBundle
=================================

Overview
--------
The CmsEventBundle provides a simple event/observer mechanism. It is deprecated since Chameleon System 8.0 in favor of the Symfony Event Dispatcher but remains available for backward compatibility.

Features
--------
- Define and trigger events with context and name
- Register observers for specific events
- Pass arbitrary data and subject objects with events
- Static singleton event manager via `TPkgCmsEventManager`

Usage
-----
1. **Trigger an event**
```php
use TPkgCmsEvent;
use TPkgCmsEventManager;
use IPkgCmsEvent;

$event = TPkgCmsEvent::GetNewInstance(
    $this,  // subject (any object)
    IPkgCmsEvent::CONTEXT_CUSTOMER,
    'order.placed',
    ['orderId' => 123]
);
TPkgCmsEventManager::GetInstance()->NotifyObservers($event);
```

2. **Register an observer**
```php
use IPkgCmsEvent;
use IPkgCmsEventObserver;

class OrderPlacedObserver implements IPkgCmsEventObserver
{
    public function PkgCmsEventNotify(IPkgCmsEvent $event)
    {
        $data = $event->GetData();
        // handle order placed event
        return $event;
    }
}

TPkgCmsEventManager::GetInstance()->RegisterObserver(
    IPkgCmsEvent::CONTEXT_CUSTOMER,
    'order.placed',
    new OrderPlacedObserver()
);
```

Customization
-------------
Implement the interfaces `IPkgCmsEvent` or `IPkgCmsEventObserver` to define custom events and observers.

License
-------
This bundle is licensed under the MIT License. See the `LICENSE` file at the project root for details.