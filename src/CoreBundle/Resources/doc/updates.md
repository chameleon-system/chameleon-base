# Updates

## General

Updates (or migrations) are used to transfer database changes from one system to another (e.g. from a development system to production, or from a bundle vendor to the user system).

## Naming and Identification

A database update is identified by the combination of bundle name and build number (together they represent the "primary key" for an update).

Example:

An update in AppBundle/Bridge/Chameleon/Migration/Script/update-12345.inc.php is referenced by its bundle name "AppBundle" and its build number "12345". As long as the update resides in a place where it is found by the update manager, it does not matter in which sub-directory it lies or what the filename is (although the filename must end with the build number and ".inc.php").

Note that it is mandatory that each update in a bundle uses a unique build number. Different bundles may use the same build numbers for their updates.

Recommendations:

- Use the current Unix timestamp as build number for every update. This ensures uniqueness (in practice, not in theory) as well as execution order. The Chameleon update recorder does this automatically.
- Use a single update directory per bundle to avoid confusion.

## Execution Order

Within a bundle updates are executed in build number order; lower build numbers are executed first. If Unix timestamps are used, this should avoid ordering problems.

Between different bundles there is no defined order. Do not rely on a certain execution order. If it is required that a certain update is run before another one, use the method TCMSLogChange::requireBundleUpdates() to enforce execution of some updates in the desired order.

## Temporarily Disabling Updates

To temporarily disable an update (for example when you update a large database and there is a long-running update), add it to the updateBlacklist container parameter values.

The key holds an assoc array of this form:

```yaml
parameters:
    updateBlacklist:
        ChameleonSystemCoreBundle:
            - 1402983036 # replace legacy mysql calls
            - 1383125464 # change cmsident from bigint to int
```