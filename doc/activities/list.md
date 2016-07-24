# List activities

Get a list with activities.

```
GET /activities
```

Parameters:

- `id` - The ID of an activity

## Success

HTTP status: `200 OK`

```json
[
	{
        "id": "1",
        "user": "2",
        "action": "0",
        "affectedResource": "changes",
        "affectedID": "284",
        "time": "2016-07-10 09:20:52"
    },
    {
        "id": "2",
        "user": "5",
        "action": "1",
        "affectedResource": "teachers",
        "affectedID": "37",
        "time": "2016-07-11 11:17:18"
    },
    {
        "id": "3",
        "user": "3",
        "action": "1",
        "affectedResource": "courses",
        "affectedID": "56",
        "time": "2016-07-12 13:07:39"
    },
    {
        "id": "4",
        "user": "3",
        "action": "2",
        "affectedResource": "courses",
        "affectedID": "43",
        "time": "2016-07-12 13:08:01"
    },
    {
        "id": "5",
        "user": "8",
        "action": "0",
        "affectedResource": "changes",
        "affectedID": "285",
        "time": "2016-07-13 10:48:22"
    }
]
```

## Failure

HTTP status: `404 Not Found`
