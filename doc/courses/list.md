# List courses

Get a list with courses.

```
GET /courses
```

Parameters:

- `id` - The ID of a course

## Success

HTTP status: `200 OK`

```json
[
    {
        "id": "1",
        "name": "5a"
    },
    {
        "id": "2",
        "name": "5b"
    },
    {
        "id": "4",
        "name": "6a"
    },
    {
        "id": "5",
        "name": "7a"
    }
]
```

## Failure

HTTP status: `404 Not Found`
