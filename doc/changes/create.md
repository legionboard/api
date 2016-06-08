# Create change

Create a new change.

```
POST /changes
```

Parameters:

- `teacher` (required) - ID of a teacher linked to a change
- `course` - ID of a course linked to a change
- `coveringTeacher` (required when type == 1) - ID of a teacher covering a lesson
- `startBy` (required) - Time when a change starts (YYYY-MM-DDT[lesson])
- `endBy` (required) - Time when a change ends (YYYY-MM-DDT[lesson])
- `type` (required) - Type of change (0: cancellation, 1: cover, 2: information)
- `text` (required when type == 2) - Text describing a change
- `reason` (required) - The reason for a change (0: ill, 1: official, 2: on leave)
- `privateText` - Private text that is only visible to members of group 9

## Success

HTTP status: `201 Created`

```json
{
	"id": 6
}
```

## Failure

### Parameter(s) missing in request

HTTP status: `400 Bad Request`

```json
{
	"missing": [
		"teacher",
		"startBy",
		"endBy",
		"type",
		"coveringTeacher",
		"text"
	]
}
```

### 1301/1302: The starting/ending time is formatted badly.

HTTP status: `400 Bad Request`

Error code: `1301`
> The starting time is formatted badly.

Error code: `1302`
> The ending time is formatted badly.

### 1303/1304: The starting/ending time does not exist.

HTTP status: `400 Bad Request`

Error code: `1303`
> The starting time does not exist.

Error code: `1304`
> The ending time does not exist.

### 1305/1306: The (covering) teacher may only contain an integer.

HTTP status: `400 Bad Request`

Error code: `1305`
> The teacher may only contain an integer.

Error code: `1306`
> The covering teacher may only contain an integer.

### 1307: The type is not allowed.

HTTP status: `400 Bad Request`

Error code: `1307`
> The type is not allowed.

### 1308/1309: The (covering) teacher does not exist.

HTTP status: `400 Bad Request`

Error code: `1308`
> The teacher does not exist.

Error code: `1309`
> The covering teacher does not exist.

### 1310: The ending time has to be after the start time.

HTTP status: `400 Bad Request`

Error code: `1310`
> The ending time has to be after the start time.

### 1311: The reason is not allowed.

HTTP status: `400 Bad Request`

Error code: `1311`
> The reason is not allowed.

### 1300: The change could not get created.

HTTP status: `409 Conflict`

Error code: `1300`
> The change could not get created.
