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
        "name": "Green",
        "subject": "1",
        "archived": false,
        "added": "2015-11-12 13:33:33",
        "edited": "2015-11-12 13:33:33"
    },
    {
        "id": "2",
        "name": "Smith",
        "subject": "10",
        "archived": false,
        "added": "2015-11-12 13:33:53",
        "edited": "2015-11-12 13:33:53"
    },
    {
        "id": "4",
        "name": "Williams",
        "subject": "5",
        "archived": false,
        "added": "2015-11-15 10:48:36",
        "edited": "2015-11-15 10:49:06"
    },
    {
        "id": "5",
        "name": "Miller",
        "subject": "10",
        "archived": true,
        "added": "2015-11-16 14:23:39",
        "edited": "2015-11-16 14:23:39"
    }
]
```

## Failure

HTTP status: `404 Not Found`
