# List changes

Get a list with changes.

```
GET /changes/:id?[...]
```

Parameters:

- `id` - The ID of a change
- `teachers` - The comma separated IDs of teachers
- `courses` - The comma separated IDs of courses
- `coveringTeacher` - The ID of a teacher covering the lesson
- `startBy` - Date when a change starts (YYYY-MM-DD)
- `endBy` - Date when a change ends (YYYY-MM-DD)

## Notes

For `startBy` and `endBy` you can use the following aliases:

* `now`
* `tom`: Tommorow
* `i3d`: In three days
* `i1w`: In one week (seven days)
* `i1m`: In one month (28 days)
* `i1y`: In one year (365 days)

## Success

HTTP status: `200 OK`

```json
[
	{
		"id": "2",
		"startingDate": "2015-12-12",
		"startingHour": "",
		"endingDate": "2015-12-12",
		"endingHour": "02",
		"type": "1",
		"course": "0",
		"teacher": "5",
		"coveringTeacher": "2",
		"text": "",
		"reason": "1",
		"privateText": "",
		"added": "2015-12-10 13:33:33",
		"edited": "2015-12-10 13:33:33"
	},
	{
		"id": "3",
		"startingDate": "2015-12-06",
		"startingHour": "",
		"endingDate": "2015-12-06",
		"endingHour": "",
		"type": "0",
		"course": "6",
		"teacher": "2",
		"coveringTeacher": "",
		"text": "Disprove theory of relativity",
		"reason": "0",
		"privateText": "",
		"added": "2015-12-06 10:11:12",
		"edited": "2015-12-06 10:12:13"
	},
	{
		"id": "5",
		"startingDate": "2015-12-24",
		"startingHour": "03",
		"endingDate": "2015-12-24",
		"endingHour": "04",
		"type": "2",
		"course": "14",
		"teacher": "4",
		"coveringTeacher": "",
		"text": "Five minutes later",
		"reason": "2",
		"privateText": "Traffic jam",
		"added": "2015-12-24 09:44:55",
		"edited": "2015-12-24 09:44:55"
	}
]
```

## Failure

### No changes found

HTTP status: `404 Not Found`

### 1100/1102: The (covering) teacher may only contain an integer.

HTTP status: `400 Bad Request`

Error code: `1100`
> The teacher may only contain an integer.

Error code: `1102`
> The covering teacher may only contain an integer.

### 1101/1103: The (covering) teacher does not exist.

HTTP status: `400 Bad Request`

Error code: `1101`
> The teacher does not exist.

Error code: `1103`
> The covering teacher does not exist.

### 1104/1106: The starting/ending date is formatted badly.

HTTP status: `400 Bad Request`

Error code: `1104`
> The starting date is formatted badly.

Error code: `1106`
> The ending date is formatted badly.

### 1105/1107: The starting/ending date does not exist.

HTTP status: `400 Bad Request`

Error code: `1105`
> The starting date does not exist.

Error code: `1107`
> The ending date does not exist.

### 1108: The ending date has to be after the starting date.

HTTP status: `400 Bad Request`

Error code: `1108`
> The ending date has to be after the starting date.
