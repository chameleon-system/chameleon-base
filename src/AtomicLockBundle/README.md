# Chameleon System AtomicLockBundle

Overview
--------
The AtomicLockBundle provides a simple atomic locking mechanism using the database. 
It is suitable for preventing concurrent execution of critical sections in distributed or multi-process environments.

Features
--------
- Acquire locks by key to ensure mutual exclusion.
- Automatically clear stale locks via a Cronjob.

Installation
------------
This bundle is included in the Chameleon System core and enabled by default. To enable it manually, register the bundle in your `AppKernel` or `bundles.php`:

```php
// in AppKernel::registerBundles() or bundles.php
new ChameleonSystem\AtomicLockBundle\ChameleonSystemAtomicLockBundle(),
```

Database
--------
The bundle uses the `data_atomic_lock` table to track active locks.

Usage
-----
1. Instantiate the `AtomicLock` class:

    ```php
    $oAtomicLock = new \AtomicLock();
    ```

2. (Optional) Generate a lock key from an object:

    ```php
    $key = $oAtomicLock->getKeyForObject($myObject);
    ```

3. Acquire the lock:

    ```php
    $oLock = $oAtomicLock->acquireLock($key);
    if (null === $oLock) {
        // Lock is already held by another process
    } else {
        try {
            // Perform critical operations here
        } finally {
            $oLock->release();
        }
    }
    ```

Cronjob
-------
A Cronjob is provided to clear stale locks periodically. Configure the job `chameleon_system_atomic_lock.cronjob.clear_atomic_locks_cronjob` to run (e.g., every 10 minutes) to avoid stale locks:

```xml
<service id="chameleon_system_atomic_lock.cronjob.clear_atomic_locks_cronjob" class="TCMSCronjob_ClearAtomicLocks" shared="false">
    <tag name="chameleon_system.cronjob" />
</service>
```

License
-------
This bundle is licensed under the MIT License. See the `LICENSE` file at the project root for details.
