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
        "subjects": "1,3",
        "archived": false,
        "added": "2015-11-12 13:33:33",
        "edited": "2015-11-12 13:33:33"
    },
    {
        "id": "2",
        "name": "Smith",
        "subjects": "4,10",
        "archived": false,
        "added": "2015-11-12 13:33:53",
        "edited": "2015-11-12 13:33:53"
    },
    {
        "id": "4",
        "name": "Williams",
        "subjects": "5,7",
        "archived": false,
        "added": "2015-11-15 10:48:36",
        "edited": "2015-11-15 10:49:06"
    },
    {
        "id": "5",
        "name": "Miller",
        "subjects": "3,10,11",
        "archived": true,
        "added": "2015-11-16 14:23:39",
        "edited": "2015-11-16 14:23:39"
    }
]
```

## Failure

HTTP status: `404 Not Found`
