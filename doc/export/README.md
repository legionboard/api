# Export

Since LegionBoard Heart 0.2.1,
the API provides the option to export all data in one request.
It includes all resources except activities.

```
GET /export?[...]
```

Parameters:

- `startBy` - Date when list of changes starts (YYYY-MM-DD) (Default: now)
- `endBy` - Date when list of changes ends (YYYY-MM-DD) (Default: i1w)

## Success

HTTP status: `200 OK`

```json
{
	"resources":{
		"changes":[
			{
				"id":"2",
				"startingDate":"2017-04-25",
				"startingHour":"03",
				"endingDate":"2017-04-26",
				"endingHour":"",
				"type":"0",
				"course":"0",
				"teacher":"1",
				"coveringTeacher":"0",
				"text":"",
				"subject":"2",
				"reason":"1",
				"privateText":"",
				"added":"2017-04-24 17:30:40",
				"edited":"2017-04-24 17:30:40"
			}
		],
		"courses":[
			{
				"id":"1",
				"name":"Q3 Sport 09",
				"subjects":null,
				"archived":false,
				"added":"2017-04-24 16:30:40",
				"edited":"2017-04-24 16:30:40"
			},
			{
				"id": "2",
				"name": "11-1",
				"subjects": "3,4,7",
				"archived": false,
				"added": "2017-04-24 16:38:40",
				"edited": "2017-04-24 16:39:40"
			},
		]
		"subjects":[
			{
				"id":"2",
				"name":"Mathematics",
				"shortcut":"Mat",
				"archived":false,
				"added":"2017-04-24 17:30:57",
				"edited":"2017-04-24 17:31:25"
			}
		],
		"teachers":[
			{
				"id":"1",
				"name":"Miller",
				"subjects":null,
				"archived":false,
				"added":"2017-04-24 17:32:37",
				"edited":"2017-04-24 17:32:37"
			},
			{
				"id":"3",
				"name":"Weber",
				"subjects":"2",
				"archived":false,
				"added":"2017-04-24 17:34:37",
				"edited":"2017-04-24 17:34:37"
			}
		]
	}
}
```

## Failure

### No export data available

HTTP status: `404 Not Found`

### 4100/4102: The starting/ending date is formatted badly.

HTTP status: `400 Bad Request`

Error code: `4100`
> The starting date is formatted badly.

Error code: `4102`
> The ending date is formatted badly.

### 4101/4103: The starting/ending date does not exist.

HTTP status: `400 Bad Request`

Error code: `4101`
> The starting date does not exist.

Error code: `4103`
> The ending date does not exist.

### 4104: The ending date has to be after the starting date.

HTTP status: `400 Bad Request`

Error code: `4104`
> The ending date has to be after the starting date.
