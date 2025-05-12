Chameleon System CmsCounterBundle
=================================

Overview
--------
The CmsCounterBundle provides a simple, atomic counter service to generate and track numeric sequences
on a per-owner basis. Useful for order numbers, invoice IDs, serial counters, or any feature requiring
unique, incremental values scoped by a database record.

Features
--------
- Atomic next-value generation with database row locking
- Retrieve the current counter without incrementing
- Initialize or reset counters manually
- Persistence by owner record and counter name
- Lightweight and backend-agnostic (uses Doctrine DBAL)

Installation
------------
Included by default in the Chameleon System core. To register manually, add to your AppKernel:
```php
// in AppKernel::registerBundles() or bundles.php
new ChameleonSystem\\CmsCounterBundle\\ChameleonSystemCmsCounterBundle(),
```

Service Configuration
---------------------
The bundle registers a single service: `chameleon_system_cms_counter.counter`

Usage
-----
Inject the `CmsCounter` service (interface `esono\\pkgCmsCounter\\CmsCounter`) into your code:
```php
use esono\\pkgCmsCounter\\CmsCounter;
use TCMSRecord;

class OrderService
{
    public function __construct(private CmsCounter $counter) {}

    public function issueInvoice(TCMSRecord $order): int
    {
        // Generate the next invoice number for this order
        $nextNumber = $this->counter->get($order, 'invoice_number');
        // ...persist $nextNumber on $order or other logic
        return $nextNumber;
    }

    public function getCurrentInvoiceNumber(TCMSRecord $order): ?int
    {
        return $this->counter->getCurrentValue($order, 'invoice_number');
    }

    public function resetInvoiceCounter(TCMSRecord $order, int $value = 1): void
    {
        // Manually set counter to $value
        $this->counter->set($order, 'invoice_number', $value);
    }
}
```

Database Schema
---------------
Counters are stored in the `pkg_cms_counter` table with columns:
- `id` (UUID)
- `owner_table_name` (string)
- `owner` (record ID)
- `system_name` (identifier)
- `name` (label)
- `value` (int)

License
-------
Licensed under the MIT License. See the `LICENSE` file at the project root for details.
