# List subjects

Get a list with subjects.

```
GET /subjects
```

Parameters:

- `id` - The ID of a subject

## Success

HTTP status: `200 OK`

```json
[
    {
        "id": "1",
        "name": "Sports",
        "shortcut": "Spo",
        "archived": false,
        "added": "2015-11-12 13:33:33",
        "edited": "2015-11-12 13:33:33"
    },
    {
        "id": "2",
        "name": "Biology",
        "shortcut": "Bio",
        "archived": true,
        "added": "2015-11-12 13:33:53",
        "edited": "2015-11-12 13:33:53"
    },
    {
        "id": "4",
        "name": "Ethics",
        "shortcut": "Eth",
        "archived": false,
        "added": "2015-11-15 10:48:36",
        "edited": "2015-11-15 10:49:06"
    },
    {
        "id": "5",
        "name": "Math",
        "archived": false,
        "shortcut": "Mat",
        "added": "2015-11-16 14:23:39",
        "edited": "2015-11-16 14:23:39"
    }
]
```

## Failure

HTTP status: `404 Not Found`
