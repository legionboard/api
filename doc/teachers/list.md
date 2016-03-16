# List teachers

Get a list with teachers.

```
GET /teachers
```

Parameters:

- `id` - The ID of a teacher

## Success

HTTP status: `200 OK`

```json
[
    {
        "id": "1",
        "name": "Alle"
    },
    {
        "id": "2",
        "name": "Smith"
    },
    {
        "id": "4",
        "name": "Williams"
    },
    {
        "id": "5",
        "name": "Miller"
    }
]
```

## Failure

HTTP status: `404 Not Found`
