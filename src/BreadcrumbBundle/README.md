# Breacrumb Bundle

## Overview
The Breadcrumb Bundle replaces the old MTBreadcrumb. It's used to generate
the standard breadcrumb of the Chameleon System.

## Generator
The Breadcrumnb Generator is an extension of the `AbstractBreadcrumbGenerator` and need to implement
the functions to generate a breadcrumb and handle the caching.

### Service-Tag
Every generator that implements the Tag `chameleon_system_breadcrumb.generator.breadcrumb_generator` will be loaded
by the `BreadrumbGeneratorProvider`. Within the tag you need the attribute `order` having an integer value to indicate
the priority of the generator. The first one that return "true" will be used generator to generate the breadcrumb.

### Caching
Every Generator needs handle its own caching. The AbstractBreadcrumbGenerator provides
the necessary functions, that need to be implemented for caching.
