# Documentation

## Resources

- [Changes](changes/README.md)
- [Teachers](teachers/README.md)

## Clients

* [Eye](https://gitlab.com/legionboard/eye)
* [KISS](https://gitlab.com/legionboard/kiss)

## Introduction

All API requests require authentication. The authentication key may be
64 digits long and should be the SHA-256 hash of the following string:

```
[Username (lowercase)]//[Password]
```

How to send the key to the API depends on which HTTP method you use:

### GET / DELETE

The key is sent as the query parameter `k`.

```
/[resource]/[id]?k=[key]
```

### POST / PUT

The key is sent as the variable `k` with using
`application/x-www-form-urlencoded` as the HTTP Content-Type in the
request.

## Errors

For more information, see [errors.md](errors.md).
