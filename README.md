# Medisol Pagination

This is a simple package to generate the correct pagination links.

## Usage

A new `Paginator` object can be instantiated with the `Paginator::create` method. This method takes two parameters;

1. `int $currentPage`
2. `int $totalPages`.

### Generating links

After instantiating a new `Paginator` object, the `generate` method can be used to generate the required links.

### Getting the links

After generating links, you can use these links by calling the `Paginator::getLinks` method. When returning the result
in JSON format, the links will be serialized to JSON.

### Available link methods

- `PaginationLink::getPage`: returns the link's page number
- `PaginationLink::isActive`: returns whether the current page is this page
- `PaginationLink::isDisabled`: returns whether this page is disabled (for example, the ellipsis between different link
  ranges).
