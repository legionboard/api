# Documentation

## Resources

- [Activities](activities/README.md)
- [Changes](changes/README.md)
- [Courses](courses/README.md)
- [Subjects](subjects/README.md)
- [Teachers](teachers/README.md)

## Official Clients

* [Eye](https://gitlab.com/legionboard/eye)
* [KISS](https://gitlab.com/legionboard/kiss)

## Unofficial Clients

* [substitution-schedule-parser](https://github.com/johan12345/substitution-schedule-parser)

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

## Save bandwith by using data hashes

Each GET request comes along with the `LegionBoard-Heart-Data-Hash-SHA-512` header
containing the SHA-512 hash of its data.
You can use this hash to save bandwith by sending it as the query parameter `dataHash`.
If the hash of the data equals to the hash of the query paramter,
no JSON will be handed out and instead the status "304 Not Modified" is set.

## Errors

For more information, see [errors.md](errors.md).
