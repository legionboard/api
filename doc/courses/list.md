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
        "name": "5a",
        "subjects": "1,3,4,5,6,8,9,10",
        "archived": false,
        "added": "2015-11-12 13:33:33",
        "edited": "2015-11-12 13:33:33"
    },
    {
        "id": "2",
        "name": "5b",
        "subjects": "1,3,4,5,6,8,9,11",
        "archived": true,
        "added": "2015-11-12 13:33:53",
        "edited": "2015-11-12 13:33:53"
    },
    {
        "id": "4",
        "name": "6a",
        "subjects": "1,3,4,5,6,8,9,10,12",
        "archived": false,
        "added": "2015-11-15 10:48:36",
        "edited": "2015-11-15 10:49:06"
    },
    {
        "id": "5",
        "name": "7a",
        "subjects": "1,3,4,5,6,8,9,10,12,13",
        "archived": false,
        "added": "2015-11-16 14:23:39",
        "edited": "2015-11-16 14:23:39"
    }
]
```

## Failure

HTTP status: `404 Not Found`
